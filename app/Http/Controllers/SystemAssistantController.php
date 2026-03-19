<?php

namespace App\Http\Controllers;

use App\Models\SystemAssistantInteraction;
use App\Services\SystemAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        return response()->json($response);
    }

    public function feedback(Request $request, SystemAssistantInteraction $interaction): JsonResponse
    {
        $validated = $request->validate([
            'helpful' => ['required', 'boolean'],
        ]);

        $interaction->update([
            'helpful' => (bool) $validated['helpful'],
            'feedback_submitted_at' => now(),
        ]);

        return response()->json([
            'message' => __('Thank you for the feedback.'),
            'helpful' => $interaction->helpful,
        ]);
    }
}
