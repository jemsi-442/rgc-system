<?php

namespace Tests\Feature\SystemAssistant;

use App\Models\Branch;
use App\Models\District;
use App\Models\Region;
use App\Models\SystemAssistantInteraction;
use App\Models\SystemAssistantTopic;
use App\Models\SystemAssistantTopicVersion;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Tests\TestCase;

class SystemAssistantManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_assistant_knowledge_workspace(): void
    {
        $this->seed(DatabaseSeeder::class);

        $superAdmin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $this->actingAs($superAdmin, 'web')
            ->get(route('assistant.topics.index'))
            ->assertOk()
            ->assertSeeText(__('Assistant Knowledge'))
            ->assertSeeText(__('Recent assistant questions'));
    }

    public function test_regional_admin_can_view_only_topics_for_their_region(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        $regionalAdmin = $this->makeUser('regional_admin', $darRegion, $temekeDistrict, $hqBranch, 'regional.assistant.view@rgc.test');
        $otherRegion = Region::query()->whereKeyNot($regionalAdmin->region_id)->firstOrFail();

        SystemAssistantTopic::query()->create([
            'slug' => 'regional-choir-help',
            'locale' => 'en',
            'region_id' => $regionalAdmin->region_id,
            'title' => 'Regional choir help',
            'answer' => 'Coordinate regional choir training through your region team.',
            'keywords' => ['regional choir', 'choir training'],
            'suggestions' => ['How do I send a regional announcement?'],
            'roles' => ['regional_admin'],
            'is_active' => true,
            'is_system' => false,
            'sort_order' => 250,
            'created_by' => $regionalAdmin->id,
            'updated_by' => $regionalAdmin->id,
        ]);

        SystemAssistantTopic::query()->create([
            'slug' => 'other-region-help',
            'locale' => 'en',
            'region_id' => $otherRegion->id,
            'title' => 'Other region help',
            'answer' => 'Only another region should see this management topic.',
            'keywords' => ['other region help'],
            'suggestions' => [],
            'roles' => ['regional_admin'],
            'is_active' => true,
            'is_system' => false,
            'sort_order' => 255,
            'created_by' => $regionalAdmin->id,
            'updated_by' => $regionalAdmin->id,
        ]);

        $this->actingAs($regionalAdmin, 'web')
            ->get(route('assistant.topics.index'))
            ->assertOk()
            ->assertSeeText('Regional choir help')
            ->assertSeeText('Showing assistant knowledge, questions, and feedback for your region: Dar es Salaam.')
            ->assertSeeText('Regional scope')
            ->assertDontSeeText('Other region help');
    }

    public function test_regional_admin_can_create_topic_for_their_region_only(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        $regionalAdmin = $this->makeUser('regional_admin', $darRegion, $temekeDistrict, $hqBranch, 'regional.assistant.create@rgc.test');

        $this->actingAs($regionalAdmin, 'web')
            ->post(route('assistant.topics.store'), [
                'title' => 'Regional giving guide',
                'slug' => 'regional-giving-guide',
                'locale' => 'en',
                'region_id' => null,
                'answer' => 'Guide members in your region to the Giving workspace and payment status page.',
                'keywords_text' => "regional giving\ngiving help",
                'suggestions_text' => "How do I open giving?",
                'roles' => ['regional_admin', 'member'],
                'sort_order' => 240,
                'is_active' => '1',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('system_assistant_topics', [
            'slug' => 'regional-giving-guide',
            'region_id' => $regionalAdmin->region_id,
        ]);
    }

    public function test_regional_admin_cannot_edit_topic_from_another_region(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        $regionalAdmin = $this->makeUser('regional_admin', $darRegion, $temekeDistrict, $hqBranch, 'regional.assistant.edit@rgc.test');
        $otherRegion = Region::query()->whereKeyNot($regionalAdmin->region_id)->firstOrFail();

        $topic = SystemAssistantTopic::query()->create([
            'slug' => 'other-region-topic',
            'locale' => 'en',
            'region_id' => $otherRegion->id,
            'title' => 'Other region topic',
            'answer' => 'Locked to another region.',
            'keywords' => ['other region topic'],
            'suggestions' => [],
            'roles' => ['regional_admin'],
            'is_active' => true,
            'is_system' => false,
            'sort_order' => 260,
            'created_by' => $regionalAdmin->id,
            'updated_by' => $regionalAdmin->id,
        ]);

        $this->actingAs($regionalAdmin, 'web')
            ->get(route('assistant.topics.edit', $topic))
            ->assertNotFound();
    }

    public function test_super_admin_dashboard_renders_role_aware_assistant_prompts(): void
    {
        $this->seed(DatabaseSeeder::class);

        $superAdmin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $this->actingAs($superAdmin, 'web')
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('How do I create a regional admin?')
            ->assertSeeText('How do I import branches from CSV or Excel?');
    }

    public function test_super_admin_can_create_a_custom_assistant_topic(): void
    {
        $this->seed(DatabaseSeeder::class);

        $superAdmin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $this->actingAs($superAdmin, 'web')
            ->post(route('assistant.topics.store'), [
                'title' => 'Choir rehearsal help',
                'slug' => 'choir-rehearsal-help',
                'locale' => 'en',
                'answer' => 'Open the Events area, create the rehearsal event, and notify your branch through announcements or branch chat if needed.',
                'keywords_text' => "choir rehearsal\nmusic rehearsal\nevent setup",
                'suggestions_text' => "How do I create an event?\nCan I notify the branch?",
                'roles' => ['branch_admin', 'pastor'],
                'sort_order' => 205,
                'is_active' => '1',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('system_assistant_topics', [
            'slug' => 'choir-rehearsal-help',
            'locale' => 'en',
            'title' => 'Choir rehearsal help',
            'is_active' => true,
            'is_system' => false,
        ]);

        $this->assertDatabaseHas('system_assistant_topic_versions', [
            'slug' => 'choir-rehearsal-help',
            'locale' => 'en',
            'action' => 'created',
        ]);
    }

    public function test_super_admin_can_export_assistant_topics_backup(): void
    {
        $this->seed(DatabaseSeeder::class);

        $superAdmin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $response = $this->actingAs($superAdmin, 'web')->get(route('assistant.topics.export'));

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $response->assertJsonStructure(['exported_at', 'exported_by', 'total', 'topics']);
    }

    public function test_super_admin_can_import_assistant_topics_backup(): void
    {
        $this->seed(DatabaseSeeder::class);

        $superAdmin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $region = Region::query()->firstOrFail();

        $payload = json_encode([
            'topics' => [
                [
                    'slug' => 'branch-prayer-guide',
                    'locale' => 'en',
                    'region_id' => $region->id,
                    'title' => 'Branch prayer guide',
                    'answer' => 'Use announcements and branch chat to coordinate the prayer meeting clearly.',
                    'keywords' => ['prayer guide', 'branch prayer'],
                    'suggestions' => ['How do I send a branch announcement?'],
                    'roles' => ['branch_admin', 'pastor'],
                    'is_active' => true,
                    'is_system' => false,
                    'sort_order' => 230,
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $file = UploadedFile::fake()->createWithContent('assistant-topics.json', $payload);

        $this->actingAs($superAdmin, 'web')
            ->post(route('assistant.topics.import'), [
                'topics_file' => $file,
            ])
            ->assertRedirect(route('assistant.topics.index'));

        $this->assertDatabaseHas('system_assistant_topics', [
            'slug' => 'branch-prayer-guide',
            'locale' => 'en',
            'title' => 'Branch prayer guide',
            'region_id' => $region->id,
        ]);
    }

    public function test_chatbot_prefers_super_admin_topic_for_super_admin_user_question(): void
    {
        $this->seed(DatabaseSeeder::class);

        $superAdmin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $this->actingAs($superAdmin, 'web')
            ->postJson(route('assistant.chat'), [
                'question' => 'How do I create user accounts and reset a user password?',
            ])
            ->assertOk()
            ->assertJsonFragment([
                'topic' => 'users-super-admin',
            ])
            ->assertJsonPath('source', 'database');
    }

    public function test_chatbot_uses_region_specific_topic_before_global_topic(): void
    {
        $this->seed(DatabaseSeeder::class);

        [$darRegion, $temekeDistrict, $hqBranch] = $this->darHeadquartersContext();
        $regionalAdmin = $this->makeUser('regional_admin', $darRegion, $temekeDistrict, $hqBranch, 'regional.assistant.chat@rgc.test');

        SystemAssistantTopic::query()->create([
            'slug' => 'global-regional-caravan-help',
            'locale' => 'en',
            'region_id' => null,
            'title' => 'Global regional caravan help',
            'answer' => 'Use the general regional planning guidance in your dashboard workspace.',
            'keywords' => ['regional caravan help', 'regional caravan'],
            'suggestions' => [],
            'roles' => ['regional_admin'],
            'is_active' => true,
            'is_system' => false,
            'sort_order' => 280,
            'created_by' => $regionalAdmin->id,
            'updated_by' => $regionalAdmin->id,
        ]);

        SystemAssistantTopic::query()->create([
            'slug' => 'region-specific-caravan-help',
            'locale' => 'en',
            'region_id' => $regionalAdmin->region_id,
            'title' => 'Region specific caravan help',
            'answer' => 'For your region, coordinate the caravan plan inside your own region workspace.',
            'keywords' => ['regional caravan help', 'regional caravan'],
            'suggestions' => [],
            'roles' => ['regional_admin'],
            'is_active' => true,
            'is_system' => false,
            'sort_order' => 281,
            'created_by' => $regionalAdmin->id,
            'updated_by' => $regionalAdmin->id,
        ]);

        $this->actingAs($regionalAdmin, 'web')
            ->postJson(route('assistant.chat'), [
                'question' => 'I need regional caravan help',
            ])
            ->assertOk()
            ->assertJsonFragment([
                'topic' => 'region-specific-caravan-help',
            ]);
    }

    public function test_chatbot_uses_custom_topic_and_logs_interaction_history(): void
    {
        $this->seed(DatabaseSeeder::class);

        $superAdmin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $topic = SystemAssistantTopic::query()->create([
            'slug' => 'choir-rehearsal-help',
            'locale' => 'en',
            'title' => 'Choir rehearsal help',
            'answer' => 'Use the Events module to schedule a rehearsal, then notify members from your branch workspace.',
            'keywords' => ['choir rehearsal', 'music rehearsal', 'rehearsal event'],
            'suggestions' => ['How do I create an event?', 'Can I send a branch announcement too?'],
            'roles' => null,
            'is_active' => true,
            'is_system' => false,
            'sort_order' => 210,
            'created_by' => $superAdmin->id,
            'updated_by' => $superAdmin->id,
        ]);

        $response = $this->actingAs($superAdmin, 'web')
            ->postJson(route('assistant.chat'), [
                'question' => 'I need help with a choir rehearsal event',
            ]);

        $response->assertOk()
            ->assertJsonFragment([
                'topic' => 'choir-rehearsal-help',
            ])
            ->assertJsonPath('answer', fn (string $answer) => str_contains($answer, 'Events module'));

        $this->assertDatabaseHas('system_assistant_interactions', [
            'user_id' => $superAdmin->id,
            'matched_topic_id' => $topic->id,
            'matched_slug' => 'choir-rehearsal-help',
            'source' => 'database',
        ]);

        $interaction = SystemAssistantInteraction::query()->latest('id')->firstOrFail();

        $this->assertSame(['super_admin'], $interaction->role_snapshot);
        $this->assertSame('I need help with a choir rehearsal event', $interaction->question);
        $this->assertNotNull($interaction->ip_address);
    }

    public function test_super_admin_can_restore_previous_topic_version(): void
    {
        $this->seed(DatabaseSeeder::class);

        $superAdmin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $this->actingAs($superAdmin, 'web')
            ->post(route('assistant.topics.store'), [
                'title' => 'Member receipts help',
                'slug' => 'member-receipts-help',
                'locale' => 'en',
                'answer' => 'Open Giving and download the completed receipt from the status page.',
                'keywords_text' => "receipt\ndownload receipt\npayment receipt",
                'suggestions_text' => "Where do I find my receipt?",
                'roles' => ['member'],
                'sort_order' => 240,
                'is_active' => '1',
            ])
            ->assertRedirect();

        $topic = SystemAssistantTopic::query()->where('slug', 'member-receipts-help')->firstOrFail();

        $this->actingAs($superAdmin, 'web')
            ->put(route('assistant.topics.update', $topic), [
                'title' => 'Member receipts help',
                'slug' => 'member-receipts-help',
                'locale' => 'en',
                'answer' => 'Open Giving, then check your payment history card before downloading the receipt.',
                'keywords_text' => "receipt\ndownload receipt\npayment receipt",
                'suggestions_text' => "Where do I find my receipt?",
                'roles' => ['member'],
                'sort_order' => 241,
                'is_active' => '1',
            ])
            ->assertRedirect(route('assistant.topics.edit', $topic));

        $createdVersion = SystemAssistantTopicVersion::query()
            ->where('topic_id', $topic->id)
            ->where('action', 'created')
            ->firstOrFail();

        $this->actingAs($superAdmin, 'web')
            ->post(route('assistant.topics.versions.restore', ['topic' => $topic, 'version' => $createdVersion]))
            ->assertRedirect(route('assistant.topics.edit', $topic));

        $topic->refresh();

        $this->assertSame('Open Giving and download the completed receipt from the status page.', $topic->answer);
        $this->assertSame(240, $topic->sort_order);

        $this->assertDatabaseHas('system_assistant_topic_versions', [
            'topic_id' => $topic->id,
            'action' => 'restored_version',
            'restored_from_version_id' => $createdVersion->id,
        ]);
    }

    public function test_super_admin_can_filter_recent_assistant_interactions(): void
    {
        $this->seed(DatabaseSeeder::class);

        $superAdmin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        SystemAssistantInteraction::query()->create([
            'user_id' => $superAdmin->id,
            'locale' => 'en',
            'question' => 'Caravan route guidance for Kurasini',
            'normalized_question' => '',
            'matched_slug' => 'regional-caravan-plan',
            'source' => 'database',
            'confidence' => 18,
            'answer' => 'Use the caravan guide for Kurasini.',
            'role_snapshot' => ['super_admin'],
            'helpful' => false,
            'feedback_note' => 'This one still needs detail.',
            'feedback_submitted_at' => now(),
        ]);

        SystemAssistantInteraction::query()->create([
            'user_id' => $superAdmin->id,
            'locale' => 'en',
            'question' => 'Offering receipt help for members',
            'normalized_question' => '',
            'matched_slug' => 'member-receipts-help',
            'source' => 'database',
            'confidence' => 15,
            'answer' => 'Open Giving and use the receipt download action.',
            'role_snapshot' => ['super_admin'],
            'helpful' => true,
            'feedback_submitted_at' => now(),
        ]);

        $this->actingAs($superAdmin, 'web')
            ->get(route('assistant.topics.index', [
                'interaction_q' => 'Kurasini',
                'interaction_feedback' => 'unhelpful',
            ]))
            ->assertOk()
            ->assertSeeText('Caravan route guidance for Kurasini')
            ->assertDontSeeText('Offering receipt help for members')
            ->assertSee('value="Kurasini"', false)
            ->assertSee('option value="unhelpful" selected', false);
    }

    public function test_super_admin_workspace_shows_usage_and_feedback_sections(): void
    {
        $this->seed(DatabaseSeeder::class);

        $superAdmin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $response = $this->actingAs($superAdmin, 'web')
            ->postJson(route('assistant.chat'), [
                'question' => 'How do I register a new account?',
            ]);

        $interactionId = $response->json('interaction_id');

        $this->actingAs($superAdmin, 'web')
            ->postJson(route('assistant.feedback', $interactionId), [
                'helpful' => false,
                'note' => 'Need step by step help',
            ])
            ->assertOk();

        $this->actingAs($superAdmin, 'web')
            ->get(route('assistant.topics.index'))
            ->assertOk()
            ->assertSeeText(__('Assistant usage in the last 7 days'))
            ->assertSeeText(__('Feedback mix'))
            ->assertSeeText('Need step by step help');
    }

    public function test_edit_page_can_filter_version_history(): void
    {
        $this->seed(DatabaseSeeder::class);

        $superAdmin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $topic = SystemAssistantTopic::query()->create([
            'slug' => 'version-filter-topic',
            'locale' => 'en',
            'title' => 'Version filter topic',
            'answer' => 'Original answer.',
            'keywords' => ['version filter'],
            'suggestions' => [],
            'roles' => null,
            'is_active' => true,
            'is_system' => false,
            'sort_order' => 300,
            'created_by' => $superAdmin->id,
            'updated_by' => $superAdmin->id,
        ]);

        SystemAssistantTopicVersion::query()->create([
            'topic_id' => $topic->id,
            'slug' => $topic->slug,
            'locale' => 'en',
            'title' => 'Version filter topic',
            'answer' => 'Imported answer payload.',
            'keywords' => ['version filter'],
            'suggestions' => [],
            'roles' => null,
            'is_active' => true,
            'is_system' => false,
            'sort_order' => 300,
            'action' => 'imported',
            'created_by' => $superAdmin->id,
        ]);

        $this->actingAs($superAdmin, 'web')
            ->get(route('assistant.topics.edit', ['topic' => $topic, 'version_action' => 'imported']))
            ->assertOk()
            ->assertSeeText('Imported answer payload.');
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
