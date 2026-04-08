<?php

namespace App\Http\Controllers;

use App\Models\SystemAssistantInteraction;
use App\Services\SystemAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SystemAssistantController extends Controller
{
    public function __construct(protected SystemAssistantService $assistant)
    {
    }

    public function reply(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:1000'],
        ]);

        $response = $this->assistant->respond($validated['question'], app()->getLocale(), $request->user(), $request);
        $this->rememberInteraction($request, $response['interaction_id'] ?? null);

        return response()->json($response);
    }

    public function feedback(Request $request, SystemAssistantInteraction $interaction): JsonResponse
    {
        abort_unless($this->canSubmitFeedback($request, $interaction), 403);

        $validated = $request->validate([
            'helpful' => ['required', 'boolean'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $interaction->update([
            'helpful' => (bool) $validated['helpful'],
            'feedback_note' => blank($validated['note'] ?? null) ? null : trim((string) ($validated['note'] ?? '')),
            'feedback_submitted_at' => now(),
        ]);

        return response()->json([
            'message' => __('Thank you for the feedback.'),
            'helpful' => $interaction->helpful,
            'feedback_note' => $interaction->feedback_note,
        ]);
    }

    private function canSubmitFeedback(Request $request, SystemAssistantInteraction $interaction): bool
    {
        $user = $request->user();

        if ($user && (int) $interaction->user_id === (int) $user->id) {
            return true;
        }

        if (! $request->hasSession()) {
            return false;
        }

        $sessionIds = collect($request->session()->get('assistant_interaction_ids', []))
            ->map(fn ($value) => (int) $value);

        return $sessionIds->contains((int) $interaction->id);
    }

    private function rememberInteraction(Request $request, mixed $interactionId): void
    {
        if (! $request->hasSession() || ! is_numeric($interactionId)) {
            return;
        }

        $knownIds = collect($request->session()->get('assistant_interaction_ids', []))
            ->push((int) $interactionId)
            ->filter(fn ($value) => is_numeric($value))
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->take(-200)
            ->values()
            ->all();

        $request->session()->put('assistant_interaction_ids', Arr::wrap($knownIds));
    }
}
