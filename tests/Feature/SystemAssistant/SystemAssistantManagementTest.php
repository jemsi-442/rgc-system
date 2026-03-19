<?php

namespace Tests\Feature\SystemAssistant;

use App\Models\SystemAssistantInteraction;
use App\Models\SystemAssistantTopic;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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

        $payload = json_encode([
            'topics' => [
                [
                    'slug' => 'branch-prayer-guide',
                    'locale' => 'en',
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
}
