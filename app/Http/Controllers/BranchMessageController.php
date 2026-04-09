<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBranchMessageRequest;
use App\Models\BranchMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BranchMessageController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('panel.messages.index', [
            'messages' => $this->messageCollection($user),
            'currentUser' => $user,
            'branchName' => $user->branch?->name ?? __('Your branch'),
            'streamUrl' => route('messages.stream'),
        ]);
    }

    public function store(StoreBranchMessageRequest $request)
    {
        $user = $request->user();
        $branchId = $user->effectiveBranchId();
        $messageText = trim((string) $request->input('message', ''));
        $attachments = $this->storeAttachments($this->uploadedAttachments($request), $branchId);
        $parent = null;

        if ($request->filled('parent_id')) {
            $parent = BranchMessage::query()
                ->with('user:id,name')
                ->where('church_id', $branchId)
                ->find($request->integer('parent_id'));
        }

        $payload = [
            'church_id' => $branchId,
            'user_id' => $user->id,
            'parent_id' => $parent?->id,
            'message' => $messageText !== '' ? $messageText : null,
            'attachments' => $attachments ?: null,
        ];

        if ($attachments !== []) {
            $first = $attachments[0];
            $payload['attachment_path'] = $first['path'];
            $payload['attachment_name'] = $first['name'];
            $payload['attachment_mime_type'] = $first['mime_type'];
            $payload['attachment_size'] = $first['size'];
        }

        $message = BranchMessage::query()->create($payload)->load(['user:id,name', 'parent.user:id,name']);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'ok',
                'message' => __('Message sent.'),
                'data' => $this->messagePayload($message, $user),
            ]);
        }

        return back()->with('status', __('Message sent.'));
    }

    public function update(Request $request, BranchMessage $message)
    {
        $this->authorize('update', $message);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $message->update([
            'message' => trim((string) $validated['message']),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'ok',
                'message' => __('Message updated.'),
                'data' => $this->messagePayload($message->fresh(['user:id,name', 'parent.user:id,name']), $request->user()),
            ]);
        }

        return back()->with('status', __('Message updated.'));
    }

    public function destroy(Request $request, BranchMessage $message)
    {
        $this->authorize('delete', $message);

        collect($message->attachmentItems())
            ->pluck('path')
            ->filter()
            ->unique()
            ->each(function (string $path): void {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            });

        $message->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'ok',
                'message' => __('Message deleted.'),
            ]);
        }

        return back()->with('status', __('Message deleted.'));
    }

    public function attachment(Request $request, BranchMessage $message): Response
    {
        $this->ensureMessageIsInBranchScope($request, $message);

        $attachment = $message->primaryAttachment();
        abort_unless($attachment !== null, 404);

        return $this->streamAttachment($request, $attachment);
    }

    public function attachmentItem(Request $request, BranchMessage $message, int $index): Response
    {
        $this->ensureMessageIsInBranchScope($request, $message);

        $attachments = $message->attachmentItems();
        $attachment = $attachments[$index] ?? null;
        abort_unless($attachment !== null, 404);

        return $this->streamAttachment($request, $attachment);
    }

    public function feed(): JsonResponse
    {
        return response()->json($this->conversationPayload(auth()->user()));
    }

    public function stream(Request $request): StreamedResponse
    {
        $user = $request->user();
        $iterations = app()->environment('testing') ? 1 : 12;
        $sleepMicroseconds = app()->environment('testing') ? 0 : 2500000;

        return response()->stream(function () use ($user, $iterations, $sleepMicroseconds): void {
            ignore_user_abort(true);
            @ini_set('output_buffering', 'off');
            @ini_set('zlib.output_compression', '0');
            @ini_set('implicit_flush', '1');

            if (! app()->environment('testing')) {
                while (ob_get_level() > 0) {
                    ob_end_flush();
                }
            }

            $lastSignature = null;

            for ($iteration = 0; $iteration < $iterations; $iteration++) {
                if (connection_aborted()) {
                    break;
                }

                $payload = $this->conversationPayload($user);
                $signature = sha1(json_encode($payload));

                if ($signature !== $lastSignature) {
                    echo "event: snapshot\n";
                    echo 'data: ' . json_encode($payload) . "\n\n";
                    $lastSignature = $signature;
                } else {
                    echo ': heartbeat ' . now()->timestamp . "\n\n";
                }

                flush();

                if ($iteration < $iterations - 1 && $sleepMicroseconds > 0) {
                    usleep($sleepMicroseconds);
                }
            }
        }, 200, [
            'Content-Type' => 'text/event-stream; charset=UTF-8',
            'Cache-Control' => 'no-cache, no-transform',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * @return Collection<int, BranchMessage>
     */
    private function messageCollection(User $user): Collection
    {
        return BranchMessage::query()
            ->select([
                'id',
                'church_id',
                'user_id',
                'parent_id',
                'message',
                'attachment_path',
                'attachment_name',
                'attachment_mime_type',
                'attachment_size',
                'attachments',
                'created_at',
                'updated_at',
            ])
            ->with(['user:id,name', 'parent.user:id,name'])
            ->where('church_id', $user->effectiveBranchId())
            ->latest()
            ->limit(50)
            ->get()
            ->reverse()
            ->values();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function conversationPayload(User $user): array
    {
        return $this->messageCollection($user)
            ->map(fn (BranchMessage $message) => $this->messagePayload($message, $user))
            ->all();
    }

    private function messagePayload(BranchMessage $message, User $viewer): array
    {
        $timestamp = optional($message->created_at)->timezone(config('app.timezone'));
        $dayLabel = null;
        $canDelete = $viewer->can('delete', $message);
        $canEdit = $viewer->can('update', $message);
        $wasEdited = $message->updated_at && $message->created_at && $message->updated_at->gt($message->created_at);

        if ($timestamp) {
            $dayLabel = $timestamp->isToday()
                ? __('Today')
                : ($timestamp->isYesterday()
                    ? __('Yesterday')
                    : $timestamp->translatedFormat('d M Y'));
        }

        $attachments = collect($message->attachmentItems())
            ->values()
            ->map(function (array $attachment, int $index) use ($message): array {
                return [
                    'name' => $attachment['name'],
                    'mime_type' => $attachment['mime_type'],
                    'url' => route('messages.attachments.show', ['message' => $message, 'index' => $index]),
                    'download_url' => route('messages.attachments.show', ['message' => $message, 'index' => $index, 'download' => 1]),
                    'is_image' => $message->isImageAttachment($attachment),
                    'size_label' => $message->attachmentSizeLabel($attachment),
                    'type_label' => $message->attachmentTypeLabel($attachment),
                ];
            })
            ->all();

        $parent = $message->parent;
        $parentPayload = null;

        if ($parent instanceof BranchMessage && $parent->church_id === $message->church_id) {
            $parentPayload = [
                'id' => $parent->id,
                'author' => $parent->user_id === $viewer->id ? __('You') : ($parent->user?->name ?? __('Branch member')),
                'excerpt' => $parent->previewText(),
                'has_attachments' => $parent->hasAttachment(),
                'attachment_count' => count($parent->attachmentItems()),
                'is_mine' => $parent->user_id === $viewer->id,
            ];
        }

        return [
            'id' => $message->id,
            'message' => $message->message,
            'sent_at' => $timestamp?->format('d M Y H:i'),
            'sent_label' => $timestamp?->diffForHumans(),
            'sent_day_key' => $timestamp?->toDateString(),
            'sent_day_label' => $dayLabel,
            'was_edited' => $wasEdited,
            'edited_label' => $wasEdited ? __('Edited') : null,
            'attachment' => $attachments[0] ?? null,
            'attachments' => $attachments,
            'parent' => $parentPayload,
            'user' => [
                'id' => $message->user_id,
                'name' => $message->user?->name ?? __('Branch member'),
            ],
            'can_edit' => $canEdit,
            'edit_url' => $canEdit ? route('messages.update', $message) : null,
            'can_delete' => $canDelete,
            'delete_url' => $canDelete ? route('messages.destroy', $message) : null,
            'can_reply' => true,
            'is_mine' => $message->user_id === $viewer->id,
        ];
    }

    /**
     * @return Collection<int, UploadedFile>
     */
    private function uploadedAttachments(StoreBranchMessageRequest $request): Collection
    {
        $files = collect();

        if ($request->hasFile('attachment')) {
            $files->push($request->file('attachment'));
        }

        if ($request->hasFile('attachments')) {
            $files = $files->merge(collect($request->file('attachments'))->filter());
        }

        return $files;
    }

    /**
     * @param  Collection<int, UploadedFile>  $files
     * @return array<int, array{path:string,name:string|null,mime_type:string|null,size:int|null}>
     */
    private function storeAttachments(Collection $files, ?int $branchId): array
    {
        if ($branchId === null) {
            return [];
        }

        return $files
            ->map(function (UploadedFile $file) use ($branchId): array {
                return [
                    'path' => $file->store('branch-messages/' . $branchId, 'public'),
                    'name' => $this->safeUploadedFilename($file),
                    'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                    'size' => $file->getSize(),
                ];
            })
            ->values()
            ->all();
    }

    private function ensureMessageIsInBranchScope(Request $request, BranchMessage $message): void
    {
        $user = $request->user();

        abort_unless($message->church_id === $user->effectiveBranchId(), 403);
    }

    /**
     * @param  array{path:string,name:string|null,mime_type:string|null,size:int|null}  $attachment
     */
    private function streamAttachment(Request $request, array $attachment): Response
    {
        $path = $attachment['path'];
        abort_unless(filled($path) && Storage::disk('public')->exists($path), 404);

        $headers = [
            'Content-Type' => $attachment['mime_type'] ?: 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
        ];
        $filename = $attachment['name'] ?: basename($path);

        if ($request->boolean('download')) {
            return Storage::disk('public')->download($path, $filename, $headers);
        }

        $response = Storage::disk('public')->response($path, $filename, $headers);
        $disposition = $this->allowsInlineAttachment((string) ($attachment['mime_type'] ?? ''))
            ? 'inline'
            : 'attachment';

        $response->headers->set('Content-Disposition', $disposition . '; filename="' . addslashes($filename) . '"');

        return $response;
    }

    private function allowsInlineAttachment(string $mimeType): bool
    {
        if (Str::startsWith($mimeType, 'image/')) {
            return true;
        }

        return in_array($mimeType, ['application/pdf', 'text/plain'], true);
    }

    private function safeUploadedFilename(UploadedFile $file): string
    {
        $name = trim(basename((string) $file->getClientOriginalName()));
        $name = preg_replace('/[\r\n\t]+/', ' ', $name) ?? $name;
        $name = Str::limit($name, 180, '');

        return $name !== '' ? $name : ($file->hashName() ?: 'upload');
    }
}
