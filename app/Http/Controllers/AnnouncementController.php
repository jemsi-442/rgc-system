<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnnouncementRequest;
use App\Models\Announcement;
use App\Models\Branch;
use App\Models\District;
use App\Models\User;
use App\Support\SafeImageUpload;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $showArchived = $request->boolean('archived');

        $announcements = Announcement::query()
            ->with(['creator', 'region', 'district', 'branch', 'targetBranches'])
            ->visibleTo($user)
            ->when($showArchived, fn ($query) => $query->archivedOnly(), fn ($query) => $query->activeListing()->orderedForDisplay())
            ->paginate(12)
            ->withQueryString();

        return view('panel.announcements.index', compact('announcements', 'showArchived'));
    }

    public function show(Announcement $announcement)
    {
        $this->authorize('view', $announcement);
        $announcement->loadMissing(['creator', 'region', 'district', 'branch', 'targetBranches.region', 'targetBranches.district']);

        return view('panel.announcements.show', compact('announcement'));
    }

    public function pdf(Announcement $announcement)
    {
        $this->authorize('view', $announcement);
        $announcement->loadMissing(['creator', 'region', 'district', 'branch', 'targetBranches.region', 'targetBranches.district']);

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

        return view('panel.announcements.create', [
            'availableDistricts' => $this->availableDistrictsFor(auth()->user()),
            'availableBranches' => $this->availableBranchesFor(auth()->user()),
        ]);
    }

    public function store(StoreAnnouncementRequest $request)
    {
        $user = $request->user();
        $image = $this->storeImageFromRequest($request, $user);

        try {
            DB::transaction(function () use ($request, $user, $image): void {
                $announcement = Announcement::query()->create(array_merge([
                    'title' => $request->string('title')->toString(),
                    'body' => trim($request->string('body')->toString()) ?: null,
                    'created_by' => $user->id,
                ], $this->resolveScopeAttributes($request, $user), $this->pinAttributes($request), $this->expiryAttributes($request), $image));

                $this->syncTargetBranches($announcement, $request, $user);
            });
        } catch (Throwable $exception) {
            $this->deleteStoredImagePath($image['image_path'] ?? null);
            throw $exception;
        }

        return redirect()->route('announcements.index')->with('status', __('Announcement posted.'));
    }

    public function edit(Announcement $announcement)
    {
        $this->authorize('update', $announcement);
        $announcement->loadMissing('targetBranches');

        return view('panel.announcements.edit', [
            'announcement' => $announcement,
            'availableDistricts' => $this->availableDistrictsFor(auth()->user()),
            'availableBranches' => $this->availableBranchesFor(auth()->user()),
        ]);
    }

    public function update(StoreAnnouncementRequest $request, Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        $imageUpdate = $this->syncAnnouncementImage($request, $announcement);

        try {
            DB::transaction(function () use ($request, $announcement, $imageUpdate): void {
                $announcement->update(array_merge([
                    'title' => $request->string('title')->toString(),
                    'body' => trim($request->string('body')->toString()) ?: null,
                ], $this->resolveScopeAttributes($request, $request->user()), $this->syncPinAttributes($request, $announcement), $this->expiryAttributes($request), $imageUpdate['attributes']));

                $this->syncTargetBranches($announcement, $request, $request->user());
            });
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
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, max-age=0',
        ];
        $filename = $announcement->image_name ?: basename((string) $announcement->image_path);
        $path = Storage::disk('public')->path($announcement->image_path);
        $response = $request->boolean('download')
            ? response()->download($path, $filename, $headers)
            : response()->file($path, $headers);

        $disposition = $request->boolean('download') ? 'attachment' : 'inline';
        $response->headers->set('Content-Disposition', $disposition . '; filename="' . addslashes($filename) . '"');

        return $response;
    }

    private function availableDistrictsFor(User $user): Collection
    {
        if (! $user->hasSystemRole('regional_admin') || ! $user->region_id) {
            return collect();
        }

        return District::query()
            ->where('region_id', $user->region_id)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    private function availableBranchesFor(User $user): Collection
    {
        if (! $user->hasSystemRole('super_admin')) {
            return collect();
        }

        return Branch::query()
            ->with(['region:id,name', 'district:id,name'])
            ->orderBy('name')
            ->get(['id', 'name', 'region_id', 'district_id']);
    }

    private function resolveScopeAttributes(StoreAnnouncementRequest $request, User $user): array
    {
        if ($user->hasSystemRole('super_admin')) {
            $scope = $request->input('delivery_scope', 'global');

            if ($scope === 'selected_branches') {
                return [
                    'is_global' => false,
                    'region_id' => null,
                    'district_id' => null,
                    'church_id' => null,
                ];
            }

            return [
                'is_global' => true,
                'region_id' => null,
                'district_id' => null,
                'church_id' => null,
            ];
        }

        if ($user->hasSystemRole('regional_admin')) {
            $scope = $request->input('delivery_scope', 'region');

            if ($scope === 'branch') {
                $branch = Branch::query()->findOrFail($request->integer('branch_id'));

                return [
                    'is_global' => false,
                    'region_id' => $user->region_id,
                    'district_id' => $branch->district_id,
                    'church_id' => $branch->id,
                ];
            }

            if ($scope === 'district') {
                return [
                    'is_global' => false,
                    'region_id' => $user->region_id,
                    'district_id' => $request->integer('district_id'),
                    'church_id' => null,
                ];
            }

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

    private function syncTargetBranches(Announcement $announcement, StoreAnnouncementRequest $request, User $user): void
    {
        if ($user->hasSystemRole('super_admin')) {
            if ($request->input('delivery_scope', 'global') === 'selected_branches') {
                $announcement->targetBranches()->sync(
                    collect($request->input('selected_branch_ids', []))
                        ->filter(fn ($value) => filled($value))
                        ->map(fn ($value) => (int) $value)
                        ->unique()
                        ->values()
                        ->all()
                );

                return;
            }

            $announcement->targetBranches()->sync([]);
            return;
        }

        $announcement->targetBranches()->sync([]);
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
        $stored = SafeImageUpload::storePublicImage(
            $file,
            'announcements/' . ($user->id ?: 'system'),
            $this->safeUploadedFilename($file)
        );

        return [
            'image_path' => $stored['path'],
            'image_name' => $stored['name'],
            'image_mime_type' => $stored['mime_type'],
        ];
    }

    private function safeUploadedFilename(UploadedFile $file): string
    {
        $name = trim(basename((string) $file->getClientOriginalName()));
        $name = preg_replace('/[\r\n\t]+/', ' ', $name) ?? $name;
        $name = Str::limit($name, 180, '');

        return $name !== '' ? $name : ($file->hashName() ?: 'upload');
    }

    private function pdfLogoDataUri(): ?string
    {
        $logoPath = public_path('images/rgc_logo.png');

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
