<?php

namespace Tests\Feature\SystemAssistant;

use App\Models\SystemAssistantInteraction;
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
}
