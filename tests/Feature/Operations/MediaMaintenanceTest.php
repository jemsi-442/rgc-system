<?php

namespace Tests\Feature\Operations;

use App\Models\Announcement;
use App\Models\Branch;
use App\Models\BranchMessage;
use App\Models\District;
use App\Models\HomeSlider;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class MediaMaintenanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_media_prune_orphans_dry_run_reports_orphans_without_deleting_files(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $user = $this->makeUser('branch_admin', $region, $district, $branch, 'media.dryrun@rgc.test');

        $slidePath = UploadedFile::fake()->image('kept-slide.jpg')->store('sliders', 'public');
        HomeSlider::query()->create([
            'title' => 'Kept slide',
            'subtitle' => 'Still referenced',
            'image_path' => $slidePath,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $orphanPath = UploadedFile::fake()->image('orphan-slide.jpg')->store('sliders', 'public');

        $this->artisan('media:prune-orphans', ['--dry-run' => true])
            ->expectsOutput('Dry run: found 1 orphaned public uploads.')
            ->expectsOutput($orphanPath)
            ->assertExitCode(0);

        Storage::disk('public')->assertExists($slidePath);
        Storage::disk('public')->assertExists($orphanPath);
    }

    public function test_media_prune_orphans_deletes_only_unreferenced_public_uploads(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $user = $this->makeUser('branch_admin', $region, $district, $branch, 'media.cleanup@rgc.test');

        $slidePath = UploadedFile::fake()->image('kept-slide.jpg')->store('sliders', 'public');
        HomeSlider::query()->create([
            'title' => 'Kept slide',
            'subtitle' => 'Still referenced',
            'image_path' => $slidePath,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $announcementPath = UploadedFile::fake()->image('kept-announcement.jpg')->store('announcements/' . $user->id, 'public');
        Announcement::query()->create([
            'title' => 'Kept announcement',
            'body' => 'Still referenced',
            'church_id' => $branch->id,
            'created_by' => $user->id,
            'is_global' => false,
            'image_path' => $announcementPath,
            'image_name' => 'kept-announcement.jpg',
            'image_mime_type' => 'image/jpeg',
        ]);

        $messageAttachmentPath = UploadedFile::fake()->create('kept-notes.pdf', 32, 'application/pdf')->store('branch-messages/' . $branch->id, 'public');
        BranchMessage::query()->create([
            'church_id' => $branch->id,
            'user_id' => $user->id,
            'message' => 'Attachment kept',
            'attachment_path' => $messageAttachmentPath,
            'attachment_name' => 'kept-notes.pdf',
            'attachment_mime_type' => 'application/pdf',
            'attachment_size' => 32000,
        ]);

        $orphanSlide = UploadedFile::fake()->image('orphan-slide.jpg')->store('sliders', 'public');
        $orphanAnnouncement = UploadedFile::fake()->image('orphan-announcement.jpg')->store('announcements/orphans', 'public');
        $orphanAttachment = UploadedFile::fake()->create('orphan.txt', 8, 'text/plain')->store('branch-messages/orphans', 'public');

        $this->artisan('media:prune-orphans')
            ->expectsOutput('Deleted 3 orphaned public uploads.')
            ->assertExitCode(0);

        Storage::disk('public')->assertExists($slidePath);
        Storage::disk('public')->assertExists($announcementPath);
        Storage::disk('public')->assertExists($messageAttachmentPath);
        Storage::disk('public')->assertMissing($orphanSlide);
        Storage::disk('public')->assertMissing($orphanAnnouncement);
        Storage::disk('public')->assertMissing($orphanAttachment);
    }

    private function darHeadquartersContext(): array
    {
        $region = Region::query()->where('name', 'Dar es Salaam')->firstOrFail();
        $district = District::query()->where('region_id', $region->id)->where('name', 'Temeke')->firstOrFail();
        $branch = Branch::query()->where('name', 'Toangoma')->firstOrFail();

        return [$region, $district, $branch];
    }

    private function makeUser(string $role, Region $region, District $district, Branch $branch, string $email): User
    {
        $user = User::query()->create([
            'name' => Str::headline(str_replace(['@rgc.test', '.'], ['', ' '], $email)),
            'email' => $email,
            'password' => 'ChangeMe123!',
            'role' => $role,
            'status' => 'active',
            'region_id' => $region->id,
            'district_id' => $district->id,
            'branch_id' => $branch->id,
            'church_id' => $branch->id,
            'email_verified_at' => now(),
        ]);

        $user->syncRoles([$role]);

        return $user;
    }
}
