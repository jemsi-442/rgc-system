<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSystemAssistantTopicRequest;
use App\Http\Requests\UpdateSystemAssistantTopicRequest;
use App\Models\SystemAssistantInteraction;
use App\Models\SystemAssistantTopic;
use App\Support\SystemAssistantKnowledge;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SystemAssistantTopicController extends Controller
{
    private const ROLE_OPTIONS = [
        'super_admin',
        'regional_admin',
        'district_admin',
        'branch_admin',
        'bishop',
        'pastor',
        'accountant',
        'member',
    ];

    public function index(Request $request): View
    {
        [$search, $locale, $topics] = $this->filteredTopics($request);

        $stats = [
            'topics_total' => SystemAssistantTopic::query()->count(),
            'topics_active' => SystemAssistantTopic::query()->where('is_active', true)->count(),
            'questions_total' => SystemAssistantInteraction::query()->count(),
            'questions_today' => SystemAssistantInteraction::query()->whereDate('created_at', today())->count(),
            'fallback_count' => SystemAssistantInteraction::query()->where('source', 'fallback')->count(),
            'helpful_count' => SystemAssistantInteraction::query()->where('helpful', true)->count(),
            'unhelpful_count' => SystemAssistantInteraction::query()->where('helpful', false)->count(),
        ];

        $recentInteractions = SystemAssistantInteraction::query()
            ->with(['user:id,name,email', 'topic:id,title,slug,locale'])
            ->latest()
            ->limit(12)
            ->get();

        $topQuestions = SystemAssistantInteraction::query()
            ->select('normalized_question')
            ->selectRaw('COUNT(*) as total')
            ->where('normalized_question', '!=', '')
            ->groupBy('normalized_question')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        return view('panel.assistant.index', [
            'topics' => $topics,
            'search' => $search,
            'localeFilter' => $locale,
            'stats' => $stats,
            'recentInteractions' => $recentInteractions,
            'topQuestions' => $topQuestions,
            'localeOptions' => config('app.supported_locales', ['en', 'sw']),
        ]);
    }

    public function create(): View
    {
        return view('panel.assistant.create', $this->formData(new SystemAssistantTopic()));
    }

    public function store(StoreSystemAssistantTopicRequest $request): RedirectResponse
    {
        $topic = SystemAssistantTopic::query()->create($this->payloadFromRequest($request, auth()->id()));

        return redirect()
            ->route('assistant.topics.edit', $topic)
            ->with('status', __('Assistant topic created successfully.'));
    }

    public function edit(SystemAssistantTopic $topic): View
    {
        return view('panel.assistant.edit', $this->formData($topic));
    }

    public function update(UpdateSystemAssistantTopicRequest $request, SystemAssistantTopic $topic): RedirectResponse
    {
        $topic->update($this->payloadFromRequest($request, auth()->id(), $topic));

        return redirect()
            ->route('assistant.topics.edit', $topic)
            ->with('status', __('Assistant topic updated successfully.'));
    }

    public function destroy(SystemAssistantTopic $topic): RedirectResponse
    {
        $topic->delete();

        return redirect()
            ->route('assistant.topics.index')
            ->with('status', __('Assistant topic deleted successfully.'));
    }

    public function restoreDefaults(): RedirectResponse
    {
        DB::transaction(function (): void {
            foreach (SystemAssistantKnowledge::defaultRows() as $row) {
                $topic = SystemAssistantTopic::query()->firstOrNew([
                    'slug' => $row['slug'],
                    'locale' => $row['locale'],
                ]);

                if (! $topic->exists) {
                    $topic->created_by = auth()->id();
                }

                $topic->fill([
                    'title' => $row['title'],
                    'answer' => $row['answer'],
                    'keywords' => $row['keywords'],
                    'suggestions' => $row['suggestions'],
                    'roles' => $row['roles'],
                    'is_active' => true,
                    'is_system' => true,
                    'sort_order' => $row['sort_order'],
                    'updated_by' => auth()->id(),
                ])->save();
            }
        });

        return redirect()
            ->route('assistant.topics.index')
            ->with('status', __('Assistant default topics restored successfully.'));
    }

    public function export(Request $request)
    {
        [, , $topics] = $this->filteredTopics($request, false);

        $payload = [
            'exported_at' => now()->toIso8601String(),
            'exported_by' => auth()->user()?->email,
            'total' => $topics->count(),
            'topics' => $topics->map(fn (SystemAssistantTopic $topic): array => [
                'slug' => $topic->slug,
                'locale' => $topic->locale,
                'title' => $topic->title,
                'answer' => $topic->answer,
                'keywords' => $topic->keywords ?? [],
                'suggestions' => $topic->suggestions ?? [],
                'roles' => $topic->roles,
                'is_active' => $topic->is_active,
                'is_system' => $topic->is_system,
                'sort_order' => $topic->sort_order,
            ])->values()->all(),
        ];

        $filename = 'assistant-topics-backup-' . now()->format('Ymd-His') . '.json';

        return response()->json($payload)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function import(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'topics_file' => ['required', 'file', 'mimes:json,txt', 'max:2048'],
        ]);

        $raw = file_get_contents($validated['topics_file']->getRealPath());

        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw ValidationException::withMessages([
                'topics_file' => __('The uploaded assistant backup file is not valid JSON.'),
            ]);
        }

        $topics = $decoded['topics'] ?? null;

        if (! is_array($topics) || $topics === []) {
            throw ValidationException::withMessages([
                'topics_file' => __('The uploaded assistant backup does not contain any topics.'),
            ]);
        }

        DB::transaction(function () use ($topics): void {
            foreach ($topics as $index => $row) {
                if (! is_array($row)) {
                    throw ValidationException::withMessages([
                        'topics_file' => __('Topic row :row is invalid.', ['row' => $index + 1]),
                    ]);
                }

                $locale = ($row['locale'] ?? 'en') === 'sw' ? 'sw' : 'en';
                $title = trim((string) ($row['title'] ?? ''));
                $answer = trim((string) ($row['answer'] ?? ''));
                $keywords = collect($row['keywords'] ?? [])->map(fn ($item) => trim((string) $item))->filter()->values()->all();
                $suggestions = collect($row['suggestions'] ?? [])->map(fn ($item) => trim((string) $item))->filter()->values()->all();
                $roles = collect($row['roles'] ?? [])->map(fn ($item) => trim((string) $item))->filter()->values()->all();
                $slug = Str::slug((string) ($row['slug'] ?? $title));

                if ($slug === '' || $title === '' || $answer === '' || $keywords === []) {
                    throw ValidationException::withMessages([
                        'topics_file' => __('Topic row :row is missing a title, answer, slug, or keywords.', ['row' => $index + 1]),
                    ]);
                }

                if ($roles !== [] && array_diff($roles, self::ROLE_OPTIONS) !== []) {
                    throw ValidationException::withMessages([
                        'topics_file' => __('Topic row :row contains an unknown role.', ['row' => $index + 1]),
                    ]);
                }

                $topic = SystemAssistantTopic::query()->firstOrNew([
                    'slug' => $slug,
                    'locale' => $locale,
                ]);

                if (! $topic->exists) {
                    $topic->created_by = auth()->id();
                }

                $topic->fill([
                    'title' => $title,
                    'answer' => $answer,
                    'keywords' => $keywords,
                    'suggestions' => $suggestions,
                    'roles' => $roles === [] ? null : $roles,
                    'is_active' => (bool) ($row['is_active'] ?? true),
                    'is_system' => (bool) ($row['is_system'] ?? false),
                    'sort_order' => (int) ($row['sort_order'] ?? 0),
                    'updated_by' => auth()->id(),
                ])->save();
            }
        });

        return redirect()
            ->route('assistant.topics.index')
            ->with('status', __('Assistant topics imported successfully.'));
    }

    private function formData(SystemAssistantTopic $topic): array
    {
        return [
            'topic' => $topic,
            'roleOptions' => self::ROLE_OPTIONS,
            'localeOptions' => config('app.supported_locales', ['en', 'sw']),
        ];
    }

    private function payloadFromRequest(Request $request, int $userId, ?SystemAssistantTopic $topic = null): array
    {
        $keywords = $this->parseLines((string) $request->input('keywords_text'));
        $suggestions = $this->parseLines((string) $request->input('suggestions_text'));

        if ($keywords === []) {
            throw ValidationException::withMessages([
                'keywords_text' => __('Add at least one keyword or phrase for this topic.'),
            ]);
        }

        $slug = Str::slug((string) $request->input('slug') ?: (string) $request->input('title'));

        if ($slug === '') {
            throw ValidationException::withMessages([
                'slug' => __('A valid slug is required for this topic.'),
            ]);
        }

        return [
            'slug' => $slug,
            'locale' => $request->input('locale'),
            'title' => $request->input('title'),
            'answer' => $request->input('answer'),
            'keywords' => $keywords,
            'suggestions' => $suggestions,
            'roles' => $request->filled('roles') ? array_values($request->input('roles', [])) : null,
            'is_active' => $request->boolean('is_active'),
            'is_system' => $topic?->is_system ?? false,
            'sort_order' => (int) $request->input('sort_order', 0),
            'created_by' => $topic?->created_by ?? $userId,
            'updated_by' => $userId,
        ];
    }

    /**
     * @return array{0:string,1:string,2:mixed}
     */
    private function filteredTopics(Request $request, bool $paginate = true): array
    {
        $search = trim((string) $request->string('q'));
        $locale = trim((string) $request->string('locale'));

        $query = SystemAssistantTopic::query()
            ->when($search !== '', function ($builder) use ($search) {
                $builder->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%')
                        ->orWhere('answer', 'like', '%' . $search . '%');
                });
            })
            ->when($locale !== '', fn ($builder) => $builder->where('locale', $locale))
            ->orderBy('locale')
            ->orderBy('sort_order')
            ->orderBy('title');

        $topics = $paginate
            ? $query->paginate(18)->withQueryString()
            : $query->get();

        return [$search, $locale, $topics];
    }

    /**
     * @return array<int, string>
     */
    private function parseLines(string $value): array
    {
        return collect(preg_split('/[\r\n,]+/', $value) ?: [])
            ->map(fn (string $line): string => trim($line))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
