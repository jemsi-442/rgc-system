<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Requests\StoreAnnouncementRequest;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $showArchived = $request->boolean('archived');

        $announcements = Announcement::query()
            ->with(['creator', 'region', 'district', 'branch'])
            ->visibleTo($user)
            ->when($showArchived, fn ($query) => $query->archivedOnly(), fn ($query) => $query->activeListing()->orderedForDisplay())
            ->paginate(12)
            ->withQueryString();

        return view('panel.announcements.index', compact('announcements', 'showArchived'));
    }

    public function show(Announcement $announcement)
    {
        $this->authorize('view', $announcement);
        $announcement->loadMissing(['creator', 'region', 'district', 'branch']);

        return view('panel.announcements.show', compact('announcement'));
    }


    public function pdf(Announcement $announcement)
    {
        $this->authorize('view', $announcement);
        $announcement->loadMissing(['creator', 'region', 'district', 'branch']);

        $pdf = Pdf::loadView('panel.announcements.pdf', [
            'announcement' => $announcement,
            'imageDataUri' => $this->pdfImageDataUri($announcement),
            'logoDataUri' => $this->pdfLogoDataUri(),
            'qrCodeSvg' => $this->pdfQrCodeSvg($announcement),
            'announcementUrl' => route('announcements.show', $announcement),
        ])->setPaper('a4');

        return $pdf->download('announcement-' . $announcement->id . '.pdf');
    }

    public function create()
    {
        $this->authorize('create', Announcement::class);

        return view('panel.announcements.create');
    }

    public function store(StoreAnnouncementRequest $request)
    {
        $user = $request->user();
        $image = $this->storeImageFromRequest($request, $user);

        try {
            Announcement::query()->create(array_merge([
                'title' => $request->string('title')->toString(),
                'body' => trim($request->string('body')->toString()) ?: null,
                'created_by' => $user->id,
            ], $this->scopeAttributes($user), $this->pinAttributes($request), $this->expiryAttributes($request), $image));
        } catch (Throwable $exception) {
            $this->deleteStoredImagePath($image['image_path'] ?? null);
            throw $exception;
        }

        return redirect()->route('announcements.index')->with('status', __('Announcement posted.'));
    }

    public function edit(Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        return view('panel.announcements.edit', compact('announcement'));
    }

    public function update(StoreAnnouncementRequest $request, Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        $imageUpdate = $this->syncAnnouncementImage($request, $announcement);

        try {
            $announcement->update(array_merge([
                'title' => $request->string('title')->toString(),
                'body' => trim($request->string('body')->toString()) ?: null,
            ], $this->syncPinAttributes($request, $announcement), $this->expiryAttributes($request), $imageUpdate['attributes']));
        } catch (Throwable $exception) {
            $this->deleteStoredImagePath($imageUpdate['cleanup_new'] ?? null);
            throw $exception;
        }

        $this->deleteStoredImagePath($imageUpdate['cleanup_old'] ?? null);

        return redirect()->route('announcements.index')->with('status', __('Announcement updated.'));
    }

    public function destroy(Announcement $announcement)
    {
        $this->authorize('delete', $announcement);

        $this->deleteImage($announcement);
        $announcement->delete();

        return back()->with('status', __('Announcement deleted.'));
    }

    public function image(Request $request, Announcement $announcement): Response
    {
        $this->authorize('view', $announcement);
        abort_unless($announcement->hasImage(), 404);
        abort_unless(Storage::disk('public')->exists($announcement->image_path), 404);

        $headers = [
            'Content-Type' => $announcement->image_mime_type ?: 'application/octet-stream',
        ];
        $filename = $announcement->image_name ?: basename((string) $announcement->image_path);
        $response = $request->boolean('download')
            ? Storage::disk('public')->download($announcement->image_path, $filename, $headers)
            : Storage::disk('public')->response($announcement->image_path, $filename, $headers);

        $disposition = $request->boolean('download') ? 'attachment' : 'inline';
        $response->headers->set('Content-Disposition', $disposition . '; filename="' . addslashes($filename) . '"');

        return $response;
    }

    private function scopeAttributes(User $user): array
    {
        if ($user->hasSystemRole('super_admin')) {
            return [
                'is_global' => true,
                'region_id' => null,
                'district_id' => null,
                'church_id' => null,
            ];
        }

        if ($user->hasSystemRole('regional_admin')) {
            return [
                'is_global' => false,
                'region_id' => $user->region_id,
                'district_id' => null,
                'church_id' => null,
            ];
        }

        if ($user->hasSystemRole('district_admin')) {
            return [
                'is_global' => false,
                'region_id' => $user->region_id,
                'district_id' => $user->district_id,
                'church_id' => null,
            ];
        }

        return [
            'is_global' => false,
            'region_id' => $user->region_id,
            'district_id' => $user->district_id,
            'church_id' => $user->effectiveBranchId(),
        ];
    }

    private function pinAttributes(StoreAnnouncementRequest $request): array
    {
        $isPinned = $request->boolean('is_pinned');

        return [
            'is_pinned' => $isPinned,
            'pinned_at' => $isPinned ? now() : null,
        ];
    }

    private function syncPinAttributes(StoreAnnouncementRequest $request, Announcement $announcement): array
    {
        $isPinned = $request->boolean('is_pinned');

        return [
            'is_pinned' => $isPinned,
            'pinned_at' => $isPinned
                ? ($announcement->is_pinned ? $announcement->pinned_at : now())
                : null,
        ];
    }

    private function expiryAttributes(StoreAnnouncementRequest $request): array
    {
        return [
            'expires_at' => $request->filled('expires_at')
                ? Carbon::parse((string) $request->input('expires_at'))->endOfDay()
                : null,
        ];
    }

    private function storeImageFromRequest(StoreAnnouncementRequest $request, User $user): array
    {
        if (! $request->hasFile('image')) {
            return [
                'image_path' => null,
                'image_name' => null,
                'image_mime_type' => null,
            ];
        }

        return $this->storeUploadedImage($request->file('image'), $user);
    }

    private function syncAnnouncementImage(StoreAnnouncementRequest $request, Announcement $announcement): array
    {
        if (! $request->hasFile('image')) {
            if ($request->boolean('remove_image')) {
                return [
                    'attributes' => [
                        'image_path' => null,
                        'image_name' => null,
                        'image_mime_type' => null,
                    ],
                    'cleanup_old' => $announcement->image_path,
                    'cleanup_new' => null,
                ];
            }

            return [
                'attributes' => [],
                'cleanup_old' => null,
                'cleanup_new' => null,
            ];
        }

        $uploaded = $this->storeUploadedImage($request->file('image'), $request->user());

        return [
            'attributes' => $uploaded,
            'cleanup_old' => $announcement->image_path,
            'cleanup_new' => $uploaded['image_path'] ?? null,
        ];
    }

    private function storeUploadedImage(UploadedFile $file, User $user): array
    {
        if (! $file->isValid()) {
            throw ValidationException::withMessages([
                'image' => __('Image upload failed. Please try again with another image.'),
            ]);
        }

        try {
            $path = $file->store('announcements/' . ($user->id ?: 'system'), 'public');
        } catch (Throwable $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'image' => __('Image upload failed. Please try again with another image.'),
            ]);
        }

        if (! filled($path)) {
            throw ValidationException::withMessages([
                'image' => __('Image upload failed. Please try again with another image.'),
            ]);
        }

        return [
            'image_path' => $path,
            'image_name' => $file->getClientOriginalName(),
            'image_mime_type' => $file->getClientMimeType(),
        ];
    }





    private function pdfLogoDataUri(): ?string
    {
        $logoPath = public_path('images/RGC_logo.png');

        if (! is_file($logoPath)) {
            return null;
        }

        $mimeType = mime_content_type($logoPath) ?: 'image/png';
        $contents = file_get_contents($logoPath);

        if ($contents === false) {
            return null;
        }

        return 'data:' . $mimeType . ';base64,' . base64_encode($contents);
    }

    private function pdfQrCodeSvg(Announcement $announcement): ?string
    {
        try {
            return QrCode::format('svg')
                ->size(150)
                ->margin(1)
                ->generate(route('announcements.show', $announcement));
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }
    }
    private function pdfImageDataUri(Announcement $announcement): ?string
    {
        if (! $announcement->hasImage() || ! Storage::disk('public')->exists($announcement->image_path)) {
            return null;
        }

        $mimeType = $announcement->image_mime_type ?: Storage::disk('public')->mimeType($announcement->image_path) ?: 'application/octet-stream';
        $contents = Storage::disk('public')->get($announcement->image_path);

        return 'data:' . $mimeType . ';base64,' . base64_encode($contents);
    }
    private function deleteImage(Announcement $announcement): void
    {
        $this->deleteStoredImagePath($announcement->image_path);
    }

    private function deleteStoredImagePath(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
