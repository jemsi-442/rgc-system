<?php

namespace Tests\Feature\SystemAssistant;

use App\Models\SystemAssistantInteraction;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemAssistantTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_the_chatbot_launcher_and_generic_prompts(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('data-assistant-launcher', false)
            ->assertSee('data-assistant-panel', false)
            ->assertSeeText('How do I register an account?');
    }

    public function test_chatbot_answers_registration_questions_in_english(): void
    {
        $this->postJson(route('assistant.chat'), [
            'question' => 'How do I register a new account?',
        ])
            ->assertOk()
            ->assertJsonFragment([
                'topic' => 'registration',
            ])
            ->assertJsonPath('answer', fn (string $answer) => str_contains($answer, 'Register page'));
    }

    public function test_chatbot_answers_giving_questions_in_swahili(): void
    {
        $this->withSession(['locale' => 'sw'])
            ->postJson(route('assistant.chat'), [
                'question' => 'Ninawezaje kutoa sadaka kwenye mfumo?',
            ])
            ->assertOk()
            ->assertJsonFragment([
                'topic' => 'giving',
            ])
            ->assertJsonPath('answer', fn (string $answer) => str_contains($answer, 'Snippe checkout link'));
    }

    public function test_chatbot_returns_safe_fallback_for_unknown_questions(): void
    {
        $this->postJson(route('assistant.chat'), [
            'question' => 'Who won the world cup in 1982?',
        ])
            ->assertOk()
            ->assertJsonFragment([
                'topic' => 'system-help',
            ])
            ->assertJsonCount(3, 'suggestions');
    }

    public function test_assistant_feedback_can_be_saved_for_an_interaction(): void
    {
        $response = $this->postJson(route('assistant.chat'), [
            'question' => 'How do I register a new account?',
        ]);

        $interactionId = $response->json('interaction_id');

        $this->assertNotNull($interactionId);

        $this->postJson(route('assistant.feedback', $interactionId), [
            'helpful' => true,
        ])
            ->assertOk()
            ->assertJsonFragment([
                'helpful' => true,
            ]);

        $interaction = SystemAssistantInteraction::query()->findOrFail($interactionId);

        $this->assertTrue((bool) $interaction->helpful);
        $this->assertNotNull($interaction->feedback_submitted_at);
    }

    public function test_assistant_feedback_note_can_be_saved_for_unhelpful_answer(): void
    {
        $response = $this->postJson(route('assistant.chat'), [
            'question' => 'How do I register a new account?',
        ]);

        $interactionId = $response->json('interaction_id');

        $this->postJson(route('assistant.feedback', $interactionId), [
            'helpful' => false,
            'note' => 'Need step by step guidance for church members.',
        ])
            ->assertOk()
            ->assertJsonFragment([
                'helpful' => false,
                'feedback_note' => 'Need step by step guidance for church members.',
            ]);

        $this->assertDatabaseHas('system_assistant_interactions', [
            'id' => $interactionId,
            'helpful' => false,
            'feedback_note' => 'Need step by step guidance for church members.',
        ]);
    }

    public function test_assistant_feedback_cannot_be_submitted_from_another_session(): void
    {
        $response = $this->postJson(route('assistant.chat'), [
            'question' => 'How do I register a new account?',
        ]);

        $interactionId = $response->json('interaction_id');

        $this->flushSession();

        $this->postJson(route('assistant.feedback', $interactionId), [
            'helpful' => true,
        ])->assertForbidden();

        $interaction = SystemAssistantInteraction::query()->findOrFail($interactionId);

        $this->assertNull($interaction->helpful);
        $this->assertNull($interaction->feedback_submitted_at);
    }

    public function test_assistant_prune_command_removes_old_anonymous_and_expired_history(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $oldAnonymous = SystemAssistantInteraction::query()->create([
            'user_id' => null,
            'locale' => 'en',
            'question' => 'Anonymous old question',
            'normalized_question' => 'anonymous old question',
            'matched_slug' => 'system-help',
            'source' => 'fallback',
            'confidence' => 0,
            'answer' => 'Fallback answer',
            'created_at' => now()->subDays(45),
            'updated_at' => now()->subDays(45),
        ]);

        $oldAuthenticated = SystemAssistantInteraction::query()->create([
            'user_id' => $user->id,
            'locale' => 'en',
            'question' => 'Authenticated old question',
            'normalized_question' => 'authenticated old question',
            'matched_slug' => 'system-help',
            'source' => 'fallback',
            'confidence' => 0,
            'answer' => 'Fallback answer',
            'created_at' => now()->subDays(400),
            'updated_at' => now()->subDays(400),
        ]);

        $recentAnonymous = SystemAssistantInteraction::query()->create([
            'user_id' => null,
            'locale' => 'en',
            'question' => 'Anonymous recent question',
            'normalized_question' => 'anonymous recent question',
            'matched_slug' => 'system-help',
            'source' => 'fallback',
            'confidence' => 0,
            'answer' => 'Fallback answer',
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ]);

        $this->artisan('assistant:prune-interactions', ['--days' => 365, '--guest-days' => 30])
            ->assertExitCode(0);

        $this->assertDatabaseMissing('system_assistant_interactions', ['id' => $oldAnonymous->id]);
        $this->assertDatabaseMissing('system_assistant_interactions', ['id' => $oldAuthenticated->id]);
        $this->assertDatabaseHas('system_assistant_interactions', ['id' => $recentAnonymous->id]);
    }
}
