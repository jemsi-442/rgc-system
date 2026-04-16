<?php

namespace Tests\Feature\Operations;

use App\Models\Announcement;
use App\Models\Branch;
use App\Models\BranchMessage;
use App\Models\District;
use App\Models\Expense;
use App\Models\Offering;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class BranchOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_post_branch_chat_messages_and_feed_only_returns_their_branch_messages(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        [$otherRegion, $otherDistrict, $otherBranch] = $this->makeBranchInAnotherRegion();

        $member = $this->makeUser('member', $darRegion, $temekeDistrict, $hqBranch, 'member.chat@rgc.test');
        $otherMember = $this->makeUser('member', $otherRegion, $otherDistrict, $otherBranch, 'other.chat@rgc.test');

        BranchMessage::query()->create([
            'church_id' => $otherBranch->id,
            'user_id' => $otherMember->id,
            'message' => 'Outside branch message',
        ]);

        $this->actingAs($member)
            ->post(route('messages.store'), [
                'message' => 'Inside branch message',
            ])
            ->assertRedirect();

        $this->actingAs($member)
            ->getJson(route('messages.feed'))
            ->assertOk()
            ->assertJsonFragment(['message' => 'Inside branch message'])
            ->assertJsonMissing(['message' => 'Outside branch message']);

        $this->assertDatabaseHas('branch_messages', [
            'church_id' => $hqBranch->id,
            'user_id' => $member->id,
            'message' => 'Inside branch message',
        ]);
    }

    public function test_branch_chat_page_uses_clear_branch_coordination_copy(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $member = $this->makeUser('member', $region, $district, $branch, 'member.chat.page@rgc.test');

        $this->actingAs($member)
            ->get(route('messages.index'))
            ->assertOk()
            ->assertSee('Branch Chat')
            ->assertSee('Use this space for branch coordination, quick updates, replies, and file sharing with the people in your branch.')
            ->assertSee('Shared branch conversation')
            ->assertSee('Share images, reports, or quick ministry files here');
    }

    public function test_member_can_upload_a_branch_chat_attachment_and_attachment_access_is_branch_scoped(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        [$otherRegion, $otherDistrict, $otherBranch] = $this->makeBranchInAnotherRegion();

        $member = $this->makeUser('member', $darRegion, $temekeDistrict, $hqBranch, 'member.attachment@rgc.test');
        $otherMember = $this->makeUser('member', $otherRegion, $otherDistrict, $otherBranch, 'other.attachment@rgc.test');

        $attachment = UploadedFile::fake()->image('service-update.jpg', 1200, 900);

        $this->actingAs($member)
            ->post(route('messages.store'), [
                'message' => '',
                'attachment' => $attachment,
            ])
            ->assertRedirect();

        $message = BranchMessage::query()->latest('id')->firstOrFail();

        $this->assertSame($member->id, $message->user_id);
        $this->assertSame($hqBranch->id, $message->church_id);
        $this->assertSame('service-update.jpg', $message->attachment_name);
        Storage::disk('public')->assertExists($message->attachment_path);

        $this->actingAs($member)
            ->get(route('messages.attachment', $message))
            ->assertOk()
            ->assertHeader('x-content-type-options', 'nosniff');

        $downloadResponse = $this->actingAs($member)
            ->get(route('messages.attachment', ['message' => $message, 'download' => 1]));

        $downloadResponse->assertOk();
        $this->assertStringContainsString('attachment;', (string) $downloadResponse->headers->get('content-disposition'));

        $this->actingAs($member)
            ->getJson(route('messages.feed'))
            ->assertOk()
            ->assertJsonFragment(['name' => 'service-update.jpg']);

        $this->actingAs($otherMember)
            ->get(route('messages.attachment', $message))
            ->assertForbidden();
    }

    public function test_member_can_upload_multiple_branch_chat_attachments(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $member = $this->makeUser('member', $region, $district, $branch, 'member.multi-attachment@rgc.test');

        $image = UploadedFile::fake()->image('service-photo.jpg', 1200, 900);
        $document = UploadedFile::fake()->create('weekly-report.pdf', 128, 'application/pdf');

        $this->actingAs($member)
            ->post(route('messages.store'), [
                'message' => 'Two files attached',
                'attachments' => [$image, $document],
            ])
            ->assertRedirect();

        $message = BranchMessage::query()->latest('id')->firstOrFail();
        $attachmentItems = $message->attachmentItems();

        $this->assertCount(2, $attachmentItems);
        $this->assertSame('service-photo.jpg', $attachmentItems[0]['name']);
        $this->assertSame('weekly-report.pdf', $attachmentItems[1]['name']);
        $this->assertSame('local', $attachmentItems[0]['disk']);
        $this->assertSame('local', $attachmentItems[1]['disk']);
        Storage::disk('local')->assertExists($attachmentItems[0]['path']);
        Storage::disk('local')->assertExists($attachmentItems[1]['path']);

        $this->actingAs($member)
            ->get(route('messages.attachments.show', ['message' => $message, 'index' => 1]))
            ->assertOk();

        $this->actingAs($member)
            ->getJson(route('messages.feed'))
            ->assertOk()
            ->assertJsonFragment(['name' => 'service-photo.jpg'])
            ->assertJsonFragment(['name' => 'weekly-report.pdf']);
    }

    public function test_member_cannot_upload_more_than_five_total_branch_chat_attachments(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $member = $this->makeUser('member', $region, $district, $branch, 'member.too-many-attachments@rgc.test');

        $attachments = [
            UploadedFile::fake()->create('report-1.pdf', 64, 'application/pdf'),
            UploadedFile::fake()->create('report-2.pdf', 64, 'application/pdf'),
            UploadedFile::fake()->create('report-3.pdf', 64, 'application/pdf'),
            UploadedFile::fake()->create('report-4.pdf', 64, 'application/pdf'),
            UploadedFile::fake()->create('report-5.pdf', 64, 'application/pdf'),
        ];

        $this->actingAs($member)
            ->postJson(route('messages.store'), [
                'message' => 'Too many files',
                'attachment' => UploadedFile::fake()->image('cover.jpg'),
                'attachments' => $attachments,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['attachments']);

        $this->assertDatabaseMissing('branch_messages', [
            'church_id' => $branch->id,
            'user_id' => $member->id,
            'message' => 'Too many files',
        ]);
    }

    public function test_branch_admin_can_create_announcements_scoped_to_their_branch(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.announcements@rgc.test');

        $this->actingAs($branchAdmin)
            ->post(route('announcements.store'), [
                'title' => 'Branch Admin Notice',
                'body' => 'Operational update for the branch.',
            ])
            ->assertRedirect(route('announcements.index'));

        $this->assertDatabaseHas('announcements', [
            'title' => 'Branch Admin Notice',
            'region_id' => $region->id,
            'district_id' => $district->id,
            'church_id' => $branch->id,
            'created_by' => $branchAdmin->id,
        ]);
    }

    public function test_member_cannot_create_announcements(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $member = $this->makeUser('member', $region, $district, $branch, 'member.announcements@rgc.test');

        $this->actingAs($member)
            ->post(route('announcements.store'), [
                'title' => 'Blocked Notice',
                'body' => 'Members should not be able to post this.',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('announcements', [
            'title' => 'Blocked Notice',
        ]);
    }

    public function test_branch_admin_can_record_offerings_and_expenses_for_their_branch(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.finance@rgc.test');

        $this->actingAs($branchAdmin)
            ->post(route('offerings.store'), [
                'offering_date' => '2026-03-18',
                'amount' => '45000',
                'description' => 'Sunday service',
            ])
            ->assertRedirect(route('offerings.index'));

        $this->actingAs($branchAdmin)
            ->post(route('expenses.store'), [
                'expense_date' => '2026-03-18',
                'amount' => '12000',
                'category' => 'Transport',
                'description' => 'Regional coordination visit',
            ])
            ->assertRedirect(route('expenses.index'));

        $this->assertDatabaseHas('offerings', [
            'church_id' => $branch->id,
            'recorded_by' => $branchAdmin->name,
            'amount' => 45000,
        ]);

        $this->assertDatabaseHas('expenses', [
            'church_id' => $branch->id,
            'recorded_by' => $branchAdmin->id,
            'amount' => 12000,
            'description' => 'Transport: Regional coordination visit',
        ]);
    }

    public function test_expense_pages_show_saved_category_and_details_clearly(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.expense.pages@rgc.test');

        $this->actingAs($branchAdmin)
            ->post(route('expenses.store'), [
                'expense_date' => '2026-03-18',
                'amount' => '12000',
                'category' => 'Transport',
                'description' => 'Regional coordination visit',
            ])
            ->assertRedirect(route('expenses.index'));

        $expense = Expense::query()->latest('id')->firstOrFail();

        $this->actingAs($branchAdmin)
            ->get(route('expenses.index'))
            ->assertOk()
            ->assertSee('Transport')
            ->assertSee('Regional coordination visit')
            ->assertDontSee('General');

        $this->actingAs($branchAdmin)
            ->get(route('expenses.edit', $expense))
            ->assertOk()
            ->assertSee('Edit Expense')
            ->assertSee('Adjust the category, amount, or the note attached to this expense record.')
            ->assertSee('Regional coordination visit');
    }

    public function test_branch_admin_cannot_update_an_offering_from_another_branch(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.finance.lock@rgc.test');
        [$otherRegion, $otherDistrict, $otherBranch] = $this->makeBranchInAnotherRegion();

        $offering = Offering::query()->create([
            'church_id' => $otherBranch->id,
            'amount' => 90000,
            'date' => '2026-03-17',
            'recorded_by' => 'Outside Recorder',
        ]);

        $this->actingAs($branchAdmin)
            ->patch(route('offerings.update', $offering), [
                'offering_date' => '2026-03-18',
                'amount' => '100000',
                'description' => 'Should be blocked',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('offerings', [
            'id' => $offering->id,
            'church_id' => $otherBranch->id,
            'amount' => 90000,
        ]);
    }

    public function test_message_sender_can_delete_own_branch_message_and_attachment_file(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $member = $this->makeUser('member', $region, $district, $branch, 'member.delete-own@rgc.test');

        $attachment = UploadedFile::fake()->image('delete-proof.jpg', 1200, 900);

        $this->actingAs($member)
            ->post(route('messages.store'), [
                'message' => 'This should be deleted',
                'attachment' => $attachment,
            ])
            ->assertRedirect();

        $message = BranchMessage::query()->latest('id')->firstOrFail();
        Storage::disk('public')->assertExists($message->attachment_path);

        $this->actingAs($member)
            ->deleteJson(route('messages.destroy', $message))
            ->assertOk()
            ->assertJsonFragment(['message' => 'Message deleted.']);

        $this->assertDatabaseMissing('branch_messages', [
            'id' => $message->id,
        ]);

        Storage::disk('public')->assertMissing($message->attachment_path);
    }

    public function test_branch_admin_can_delete_branch_member_message_but_other_branch_member_cannot(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        [$otherRegion, $otherDistrict, $otherBranch] = $this->makeBranchInAnotherRegion();

        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.chat.admin@rgc.test');
        $member = $this->makeUser('member', $region, $district, $branch, 'branch.chat.member@rgc.test');
        $otherMember = $this->makeUser('member', $otherRegion, $otherDistrict, $otherBranch, 'branch.chat.outside@rgc.test');

        $message = BranchMessage::query()->create([
            'church_id' => $branch->id,
            'user_id' => $member->id,
            'message' => 'Branch scoped message',
        ]);

        $this->actingAs($otherMember)
            ->deleteJson(route('messages.destroy', $message))
            ->assertForbidden();

        $this->actingAs($branchAdmin)
            ->deleteJson(route('messages.destroy', $message))
            ->assertOk()
            ->assertJsonFragment(['message' => 'Message deleted.']);

        $this->assertDatabaseMissing('branch_messages', [
            'id' => $message->id,
        ]);
    }

    public function test_sender_can_edit_own_recent_message(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $member = $this->makeUser('member', $region, $district, $branch, 'member.edit-own@rgc.test');

        $message = BranchMessage::query()->create([
            'church_id' => $branch->id,
            'user_id' => $member->id,
            'message' => 'Original branch update',
        ]);

        $message->forceFill([
            'created_at' => now()->subMinutes(5),
            'updated_at' => now()->subMinutes(5),
        ])->saveQuietly();

        $this->actingAs($member)
            ->patchJson(route('messages.update', $message), [
                'message' => 'Updated branch update',
            ])
            ->assertOk()
            ->assertJsonFragment(['message' => 'Message updated.'])
            ->assertJsonPath('data.message', 'Updated branch update')
            ->assertJsonPath('data.was_edited', true);

        $this->assertDatabaseHas('branch_messages', [
            'id' => $message->id,
            'message' => 'Updated branch update',
        ]);
    }

    public function test_sender_cannot_edit_message_after_window_expires(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $member = $this->makeUser('member', $region, $district, $branch, 'member.edit-expired@rgc.test');

        $message = BranchMessage::query()->create([
            'church_id' => $branch->id,
            'user_id' => $member->id,
            'message' => 'Expired branch update',
        ]);

        $message->forceFill([
            'created_at' => now()->subMinutes(20),
            'updated_at' => now()->subMinutes(20),
        ])->saveQuietly();

        $this->actingAs($member)
            ->patchJson(route('messages.update', $message), [
                'message' => 'Late edit should fail',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('branch_messages', [
            'id' => $message->id,
            'message' => 'Expired branch update',
        ]);
    }

    public function test_member_can_reply_to_branch_message_and_feed_contains_parent_preview(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $member = $this->makeUser('member', $region, $district, $branch, 'member.reply.test');

        $parent = BranchMessage::query()->create([
            'church_id' => $branch->id,
            'user_id' => $member->id,
            'message' => 'Parent branch update',
        ]);

        $this->actingAs($member)
            ->post(route('messages.store'), [
                'message' => 'Reply branch update',
                'parent_id' => $parent->id,
            ])
            ->assertRedirect();

        $reply = BranchMessage::query()->latest('id')->firstOrFail();

        $this->assertSame($parent->id, $reply->parent_id);

        $feed = $this->actingAs($member)
            ->getJson(route('messages.feed'))
            ->assertOk()
            ->json();

        $replyPayload = collect($feed)->firstWhere('id', $reply->id);

        $this->assertNotNull($replyPayload);
        $this->assertSame($parent->id, data_get($replyPayload, 'parent.id'));
        $this->assertSame('Parent branch update', data_get($replyPayload, 'parent.excerpt'));
    }

    public function test_member_cannot_reply_to_message_from_another_branch(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        [$otherRegion, $otherDistrict, $otherBranch] = $this->makeBranchInAnotherRegion();

        $member = $this->makeUser('member', $region, $district, $branch, 'member.reply-block.test');
        $otherMember = $this->makeUser('member', $otherRegion, $otherDistrict, $otherBranch, 'other.reply-block.test');

        $outsideMessage = BranchMessage::query()->create([
            'church_id' => $otherBranch->id,
            'user_id' => $otherMember->id,
            'message' => 'Outside branch message',
        ]);

        $this->actingAs($member)
            ->postJson(route('messages.store'), [
                'message' => 'This reply should fail',
                'parent_id' => $outsideMessage->id,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['parent_id']);

        $this->assertDatabaseMissing('branch_messages', [
            'message' => 'This reply should fail',
        ]);
    }

    public function test_branch_chat_stream_endpoint_returns_event_stream_snapshot(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $member = $this->makeUser('member', $region, $district, $branch, 'member.stream.test');

        BranchMessage::query()->create([
            'church_id' => $branch->id,
            'user_id' => $member->id,
            'message' => 'Streamed branch message',
        ]);

        $response = $this->actingAs($member)->get(route('messages.stream'));

        $response->assertOk();
        $this->assertStringContainsString('text/event-stream', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('event: snapshot', $response->streamedContent());
        $this->assertStringContainsString('Streamed branch message', $response->streamedContent());
    }

    public function test_super_admin_can_publish_a_global_announcement_with_image_visible_to_other_branches(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        $superAdmin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        [$region, $district, $branch] = $this->makeBranchInAnotherRegion();
        $member = $this->makeUser('member', $region, $district, $branch, 'member.global.notice@rgc.test');

        $image = UploadedFile::fake()->image('national-update.jpg', 1200, 900);

        $this->actingAs($superAdmin)
            ->post(route('announcements.store'), [
                'title' => 'National Leadership Update',
                'body' => 'This announcement must reach every branch.',
                'image' => $image,
                'is_pinned' => '1',
            ])
            ->assertRedirect(route('announcements.index'));

        $announcement = Announcement::query()->latest('id')->firstOrFail();

        $this->assertTrue($announcement->is_global);
        $this->assertTrue($announcement->is_pinned);
        $this->assertNotNull($announcement->pinned_at);
        $this->assertNull($announcement->region_id);
        $this->assertNull($announcement->district_id);
        $this->assertNull($announcement->church_id);
        $this->assertNotNull($announcement->image_path);
        $this->assertContains($announcement->image_mime_type, ['image/jpeg', 'image/png']);
        Storage::disk('public')->assertExists($announcement->image_path);

        $this->actingAs($member)
            ->get(route('announcements.index'))
            ->assertOk()
            ->assertSee('National Leadership Update')
            ->assertSee('This announcement must reach every branch.');

        $this->actingAs($member)
            ->get(route('announcements.image', $announcement))
            ->assertOk();
    }


    public function test_pinned_announcement_appears_before_newer_unpinned_notice_in_scope(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.announcements.pinned@rgc.test');
        $member = $this->makeUser('member', $region, $district, $branch, 'member.announcements.pinned@rgc.test');

        Announcement::query()->create([
            'title' => 'Fresh Branch Notice',
            'body' => 'This one is newer but not pinned.',
            'region_id' => $region->id,
            'district_id' => $district->id,
            'church_id' => $branch->id,
            'created_by' => $branchAdmin->id,
            'is_global' => false,
            'is_pinned' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Announcement::query()->create([
            'title' => 'Pinned Branch Notice',
            'body' => 'This one should stay above the others.',
            'region_id' => $region->id,
            'district_id' => $district->id,
            'church_id' => $branch->id,
            'created_by' => $branchAdmin->id,
            'is_global' => false,
            'is_pinned' => true,
            'pinned_at' => now(),
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        $this->actingAs($member)
            ->get(route('announcements.index'))
            ->assertOk()
            ->assertSeeInOrder(['Pinned Branch Notice', 'Fresh Branch Notice'])
            ->assertSee('Pinned');
    }


    public function test_member_can_open_a_visible_announcement_details_page(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.announcements.details@rgc.test');
        $member = $this->makeUser('member', $region, $district, $branch, 'member.announcements.details@rgc.test');

        $announcement = Announcement::query()->create([
            'title' => 'Weekly Fellowship Schedule',
            'body' => "Choir at 8:00
Main service at 10:00",
            'region_id' => $region->id,
            'district_id' => $district->id,
            'church_id' => $branch->id,
            'created_by' => $branchAdmin->id,
            'is_global' => false,
            'expires_at' => now()->addDays(7)->endOfDay(),
        ]);

        $this->actingAs($member)
            ->get(route('announcements.show', $announcement))
            ->assertOk()
            ->assertSee('Weekly Fellowship Schedule')
            ->assertSee('Choir at 8:00')
            ->assertSee('Delivery scope');
    }

    public function test_active_announcement_stays_above_expired_notice_in_scope(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.announcements.expiry@rgc.test');
        $member = $this->makeUser('member', $region, $district, $branch, 'member.announcements.expiry@rgc.test');

        Announcement::query()->create([
            'title' => 'Expired Notice',
            'body' => 'Older information now out of date.',
            'region_id' => $region->id,
            'district_id' => $district->id,
            'church_id' => $branch->id,
            'created_by' => $branchAdmin->id,
            'is_global' => false,
            'expires_at' => now()->subDay(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Announcement::query()->create([
            'title' => 'Current Notice',
            'body' => 'This should stay above expired communication.',
            'region_id' => $region->id,
            'district_id' => $district->id,
            'church_id' => $branch->id,
            'created_by' => $branchAdmin->id,
            'is_global' => false,
            'expires_at' => now()->addDays(5)->endOfDay(),
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        $this->actingAs($member)
            ->get(route('announcements.index'))
            ->assertOk()
            ->assertSeeInOrder(['Current Notice', 'Expired Notice'])
            ->assertSee('Expired');
    }

    public function test_branch_admin_gets_validation_error_for_invalid_announcement_image_upload(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.announcements.invalid.image@rgc.test');

        $this->actingAs($branchAdmin)
            ->from(route('announcements.create'))
            ->post(route('announcements.store'), [
                'title' => 'Invalid Image Attempt',
                'body' => 'Trying to upload a non-image file.',
                'image' => UploadedFile::fake()->create('poster.pdf', 120, 'application/pdf'),
            ])
            ->assertRedirect(route('announcements.create'))
            ->assertSessionHasErrors(['image']);

        $this->assertDatabaseMissing('announcements', [
            'title' => 'Invalid Image Attempt',
        ]);
    }

    public function test_branch_admin_cannot_upload_svg_as_announcement_image(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.announcements.invalid.svg@rgc.test');

        $this->actingAs($branchAdmin)
            ->from(route('announcements.create'))
            ->post(route('announcements.store'), [
                'title' => 'SVG Image Attempt',
                'body' => 'SVG should not be accepted on public announcement images.',
                'image' => UploadedFile::fake()->create('poster.svg', 10, 'image/svg+xml'),
            ])
            ->assertRedirect(route('announcements.create'))
            ->assertSessionHasErrors(['image']);

        $this->assertDatabaseMissing('announcements', [
            'title' => 'SVG Image Attempt',
        ]);
    }


    public function test_member_can_download_visible_announcement_image_as_attachment(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.announcements.download@rgc.test');
        $member = $this->makeUser('member', $region, $district, $branch, 'member.announcements.download@rgc.test');

        $image = UploadedFile::fake()->image('weekly-poster.jpg', 1200, 900);

        $this->actingAs($branchAdmin)
            ->post(route('announcements.store'), [
                'title' => 'Weekly Poster',
                'body' => 'Poster for this week.',
                'image' => $image,
            ])
            ->assertRedirect(route('announcements.index'));

        $announcement = Announcement::query()->latest('id')->firstOrFail();

        $this->actingAs($member)
            ->get(route('announcements.image', ['announcement' => $announcement, 'download' => 1]))
            ->assertOk()
            ->assertHeader('content-disposition', 'attachment; filename="weekly-poster.jpg"');
    }



    public function test_regional_admin_can_publish_a_district_only_announcement_inside_their_region(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $otherDistrict = District::query()
            ->where('region_id', $region->id)
            ->where('id', '!=', $district->id)
            ->orderBy('name')
            ->firstOrFail();

        $otherBranch = $this->makeBranch('District Scope Test Branch', $region, $otherDistrict);
        $regionalAdmin = $this->makeUser('regional_admin', $region, $district, $branch, 'regional.district.notice@rgc.test');
        $inScopeMember = $this->makeUser('member', $region, $district, $branch, 'member.temeke.notice@rgc.test');
        $outOfScopeMember = $this->makeUser('member', $region, $otherDistrict, $otherBranch, 'member.otherdistrict.notice@rgc.test');

        $this->actingAs($regionalAdmin)
            ->post(route('announcements.store'), [
                'title' => 'Temeke District Notice',
                'body' => 'District-targeted announcement from regional admin.',
                'delivery_scope' => 'district',
                'district_id' => $district->id,
            ])
            ->assertRedirect(route('announcements.index'));

        $this->assertDatabaseHas('announcements', [
            'title' => 'Temeke District Notice',
            'region_id' => $region->id,
            'district_id' => $district->id,
            'church_id' => null,
            'is_global' => false,
            'created_by' => $regionalAdmin->id,
        ]);

        $this->actingAs($inScopeMember)
            ->get(route('announcements.index'))
            ->assertOk()
            ->assertSee('Temeke District Notice');

        $this->actingAs($outOfScopeMember)
            ->get(route('announcements.index'))
            ->assertOk()
            ->assertDontSee('Temeke District Notice');
    }

    public function test_regional_admin_announcement_create_form_uses_clear_audience_labels(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $regionalAdmin = $this->makeUser('regional_admin', $region, $district, $branch, 'regional.notice.form@rgc.test');

        $this->actingAs($regionalAdmin)
            ->get(route('announcements.create'))
            ->assertOk()
            ->assertSee('Choose who should receive this announcement.')
            ->assertSee('Audience')
            ->assertSee('Whole region')
            ->assertSee('One district')
            ->assertSee('One branch')
            ->assertSee('Audience preview');
    }

    public function test_district_admin_announcement_is_visible_across_their_district(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $sisterBranch = $this->makeBranch('Temeke Sister Branch', $region, $district);
        $districtAdmin = $this->makeUser('district_admin', $region, $district, $branch, 'district.notice@rgc.test');
        $sisterMember = $this->makeUser('member', $region, $district, $sisterBranch, 'member.sister.notice@rgc.test');

        $this->actingAs($districtAdmin)
            ->post(route('announcements.store'), [
                'title' => 'District Operations Notice',
                'body' => 'This should reach every branch in the district.',
            ])
            ->assertRedirect(route('announcements.index'));

        $this->assertDatabaseHas('announcements', [
            'title' => 'District Operations Notice',
            'region_id' => $region->id,
            'district_id' => $district->id,
            'church_id' => null,
            'is_global' => false,
            'created_by' => $districtAdmin->id,
        ]);

        $this->actingAs($sisterMember)
            ->get(route('announcements.index'))
            ->assertOk()
            ->assertSee('District Operations Notice');
    }


    public function test_regional_admin_can_publish_a_branch_only_announcement_inside_their_region(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $sisterBranch = $this->makeBranch('Temeke Branch Focus', $region, $district);
        $regionalAdmin = $this->makeUser('regional_admin', $region, $district, $branch, 'regional.branch.notice@rgc.test');
        $targetMember = $this->makeUser('member', $region, $district, $sisterBranch, 'member.target.branch.notice@rgc.test');
        $hqMember = $this->makeUser('member', $region, $district, $branch, 'member.hq.branch.notice@rgc.test');

        $this->actingAs($regionalAdmin)
            ->post(route('announcements.store'), [
                'title' => 'Temeke Branch Focus Notice',
                'body' => 'This should reach one branch only.',
                'delivery_scope' => 'branch',
                'district_id' => $district->id,
                'branch_id' => $sisterBranch->id,
            ])
            ->assertRedirect(route('announcements.index'));

        $this->assertDatabaseHas('announcements', [
            'title' => 'Temeke Branch Focus Notice',
            'region_id' => $region->id,
            'district_id' => $district->id,
            'church_id' => $sisterBranch->id,
            'is_global' => false,
            'created_by' => $regionalAdmin->id,
        ]);

        $this->actingAs($targetMember)
            ->get(route('announcements.index'))
            ->assertOk()
            ->assertSee('Temeke Branch Focus Notice');

        $this->actingAs($hqMember)
            ->get(route('announcements.index'))
            ->assertOk()
            ->assertDontSee('Temeke Branch Focus Notice');
    }

    public function test_super_admin_can_publish_an_announcement_to_selected_branches_only(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $hqBranch] = $this->darHeadquartersContext();
        $selectedDistrictBranch = $this->makeBranch('Temeke Selected Branch', $region, $district);
        [$otherRegion, $otherDistrict, $outsideSelectedBranch] = $this->makeBranchInAnotherRegion();
        $outsideUntargetedBranch = $this->makeBranch($otherRegion->name . ' Untargeted Branch', $otherRegion, $otherDistrict);

        $superAdmin = $this->makeUser('super_admin', $region, $district, $hqBranch, 'super.selected.notice@rgc.test');
        $hqMember = $this->makeUser('member', $region, $district, $hqBranch, 'member.hq.selected.notice@rgc.test');
        $selectedDistrictMember = $this->makeUser('member', $region, $district, $selectedDistrictBranch, 'member.selected.district.notice@rgc.test');
        $selectedOutsideMember = $this->makeUser('member', $otherRegion, $otherDistrict, $outsideSelectedBranch, 'member.selected.outside.notice@rgc.test');
        $untargetedMember = $this->makeUser('member', $otherRegion, $otherDistrict, $outsideUntargetedBranch, 'member.untargeted.notice@rgc.test');

        $this->actingAs($superAdmin)
            ->post(route('announcements.store'), [
                'title' => 'Selected Branches Notice',
                'body' => 'Only the chosen branches should see this update.',
                'delivery_scope' => 'selected_branches',
                'selected_branch_ids' => [$selectedDistrictBranch->id, $outsideSelectedBranch->id],
            ])
            ->assertRedirect(route('announcements.index'));

        $announcement = Announcement::query()->latest('id')->firstOrFail();
        $announcement->load('targetBranches');

        $this->assertFalse($announcement->is_global);
        $this->assertNull($announcement->region_id);
        $this->assertNull($announcement->district_id);
        $this->assertNull($announcement->church_id);
        $this->assertCount(2, $announcement->targetBranches);

        $this->actingAs($selectedDistrictMember)
            ->get(route('announcements.index'))
            ->assertOk()
            ->assertSee('Selected Branches Notice')
            ->assertSee('Selected Branches');

        $this->actingAs($selectedOutsideMember)
            ->get(route('announcements.index'))
            ->assertOk()
            ->assertSee('Selected Branches Notice');

        $this->actingAs($hqMember)
            ->get(route('announcements.index'))
            ->assertOk()
            ->assertDontSee('Selected Branches Notice');

        $this->actingAs($untargetedMember)
            ->get(route('announcements.index'))
            ->assertOk()
            ->assertDontSee('Selected Branches Notice');
    }

    public function test_super_admin_cannot_target_inactive_selected_branches(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $hqBranch] = $this->darHeadquartersContext();
        $inactiveBranch = $this->makeBranch('Inactive Selected Branch', $region, $district);
        $inactiveBranch->forceFill(['status' => 'inactive'])->save();

        $superAdmin = $this->makeUser('super_admin', $region, $district, $hqBranch, 'super.inactive.selected.notice@rgc.test');

        $this->actingAs($superAdmin)
            ->from(route('announcements.create'))
            ->post(route('announcements.store'), [
                'title' => 'Invalid Selected Branch Notice',
                'body' => 'This should fail because the branch is inactive.',
                'delivery_scope' => 'selected_branches',
                'selected_branch_ids' => [$inactiveBranch->id],
            ])
            ->assertRedirect(route('announcements.create'))
            ->assertSessionHasErrors(['selected_branch_ids']);

        $this->assertDatabaseMissing('announcements', [
            'title' => 'Invalid Selected Branch Notice',
        ]);
    }
    public function test_member_can_download_visible_announcement_as_pdf(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        [$region, $district, $branch] = $this->darHeadquartersContext();
        $branchAdmin = $this->makeUser('branch_admin', $region, $district, $branch, 'branch.announcements.pdf@rgc.test');
        $member = $this->makeUser('member', $region, $district, $branch, 'member.announcements.pdf@rgc.test');

        $this->actingAs($branchAdmin)
            ->post(route('announcements.store'), [
                'title' => 'National Prayer Focus',
                'body' => 'Prayer focus for this week across the branch.',
            ])
            ->assertRedirect(route('announcements.index'));

        $announcement = Announcement::query()->latest('id')->firstOrFail();

        $response = $this->actingAs($member)
            ->get(route('announcements.pdf', $announcement));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('attachment; filename=announcement-' . $announcement->id . '.pdf', (string) $response->headers->get('content-disposition'));
    }

    private function darHeadquartersContext(): array
    {
        $region = Region::query()->where('name', 'Dar es Salaam')->firstOrFail();
        $district = District::query()->where('region_id', $region->id)->where('name', 'Temeke')->firstOrFail();
        $branch = Branch::query()->where('name', 'Toangoma')->firstOrFail();

        return [$region, $district, $branch];
    }

    private function makeBranchInAnotherRegion(): array
    {
        $region = Region::query()->where('name', '!=', 'Dar es Salaam')->orderBy('name')->firstOrFail();
        $district = District::query()->where('region_id', $region->id)->orderBy('name')->firstOrFail();
        $branch = $this->makeBranch($region->name . ' Operations Branch', $region, $district);

        return [$region, $district, $branch];
    }

    private function makeBranch(string $name, Region $region, District $district): Branch
    {
        return Branch::query()->create([
            'name' => $name,
            'type' => 'local',
            'slug' => Str::slug($name),
            'region_id' => $region->id,
            'district_id' => $district->id,
            'status' => 'active',
        ]);
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
