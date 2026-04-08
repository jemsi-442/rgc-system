<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSystemAssistantTopicRequest;
use App\Http\Requests\UpdateSystemAssistantTopicRequest;
use App\Models\Region;
use App\Models\SystemAssistantInteraction;
use App\Models\SystemAssistantTopic;
use App\Models\SystemAssistantTopicVersion;
use App\Models\User;
use App\Support\SystemAssistantKnowledge;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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

    private const VERSION_ACTIONS = [
        'baseline',
        'created',
        'updated',
        'imported',
        'deleted',
        'restored_defaults',
        'restored_version',
    ];

    public function index(Request $request): View
    {
        $manager = $request->user();
        abort_unless($manager, 403);
        $this->authorize('viewAny', SystemAssistantTopic::class);

        [$search, $locale, $scopeFilter, $topics] = $this->filteredTopics($request, $manager);
        $interactionSearch = trim((string) $request->string('interaction_q'));
        $interactionFeedback = trim((string) $request->string('interaction_feedback'));
        $interactionQuery = $this->scopedInteractionsQuery($manager);
        $manageableTopicsQuery = $this->manageableTopicsQuery($manager);
        $regionOptions = $this->regionOptions($manager);
        $managedRegion = $regionOptions->firstWhere('id', $manager->region_id);

        $stats = [
            'topics_total' => (clone $manageableTopicsQuery)->count(),
            'topics_active' => (clone $manageableTopicsQuery)->where('is_active', true)->count(),
            'questions_total' => (clone $interactionQuery)->count(),
            'questions_today' => (clone $interactionQuery)->whereDate('created_at', today())->count(),
            'fallback_count' => (clone $interactionQuery)->where('source', 'fallback')->count(),
            'helpful_count' => (clone $interactionQuery)->where('helpful', true)->count(),
            'unhelpful_count' => (clone $interactionQuery)->where('helpful', false)->count(),
        ];

        $recentInteractionsQuery = (clone $interactionQuery)
            ->when($interactionSearch !== '', function (Builder $query) use ($interactionSearch): void {
                $query->where(function (Builder $inner) use ($interactionSearch): void {
                    $inner->where('question', 'like', '%' . $interactionSearch . '%')
                        ->orWhere('answer', 'like', '%' . $interactionSearch . '%')
                        ->orWhere('matched_slug', 'like', '%' . $interactionSearch . '%');
                });
            })
            ->when($interactionFeedback !== '', function (Builder $query) use ($interactionFeedback): void {
                if ($interactionFeedback === 'helpful') {
                    $query->where('helpful', true);
                    return;
                }

                if ($interactionFeedback === 'unhelpful') {
                    $query->where('helpful', false);
                    return;
                }

                if ($interactionFeedback === 'pending') {
                    $query->whereNull('helpful');
                }
            });

        $recentInteractions = $recentInteractionsQuery
            ->with(['user:id,name,email,region_id', 'topic:id,title,slug,locale,region_id'])
            ->latest()
            ->limit(12)
            ->get();

        $topQuestions = (clone $interactionQuery)
            ->select('normalized_question')
            ->selectRaw('COUNT(*) as total')
            ->where('normalized_question', '!=', '')
            ->groupBy('normalized_question')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $usageByDay = collect(range(6, 0))->map(function (int $daysAgo) use ($interactionQuery): array {
            $date = now()->subDays($daysAgo)->startOfDay();

            return [
                'label' => $date->format('D'),
                'date' => $date->toDateString(),
                'count' => (clone $interactionQuery)->whereDate('created_at', $date->toDateString())->count(),
            ];
        })->values();

        return view('panel.assistant.index', [
            'topics' => $topics,
            'search' => $search,
            'localeFilter' => $locale,
            'scopeFilter' => $scopeFilter,
            'interactionSearch' => $interactionSearch,
            'interactionFeedback' => $interactionFeedback,
            'interactionFeedbackOptions' => [
                'helpful' => __('Helpful'),
                'unhelpful' => __('Not helpful'),
                'pending' => __('No feedback'),
            ],
            'scopeSummary' => $manager->hasSystemRole('super_admin')
                ? __('Showing assistant knowledge, questions, and feedback across the whole platform.')
                : __('Showing assistant knowledge, questions, and feedback for your region: :region.', ['region' => $managedRegion?->name ?? __('your region')]),
            'stats' => $stats,
            'recentInteractions' => $recentInteractions,
            'topQuestions' => $topQuestions,
            'usageByDay' => $usageByDay,
            'usagePeak' => max(1, (int) $usageByDay->max('count')),
            'localeOptions' => config('app.supported_locales', ['en', 'sw']),
            'regionOptions' => $regionOptions,
            'manager' => $manager,
        ]);
    }

    public function create(Request $request): View
    {
        $manager = $request->user();
        abort_unless($manager, 403);
        $this->authorize('create', SystemAssistantTopic::class);

        return view('panel.assistant.create', $this->formData(new SystemAssistantTopic(), $manager));
    }

    public function store(StoreSystemAssistantTopicRequest $request): RedirectResponse
    {
        $manager = $request->user();
        abort_unless($manager, 403);
        $this->authorize('create', SystemAssistantTopic::class);

        $topic = SystemAssistantTopic::query()->create($this->payloadFromRequest($request, auth()->id(), $manager));
        $this->snapshotTopic($topic, 'created');

        return redirect()
            ->route('assistant.topics.edit', $topic)
            ->with('status', __('Assistant topic created successfully.'));
    }

    public function edit(Request $request, SystemAssistantTopic $topic): View
    {
        $manager = $request->user();
        abort_unless($manager, 403);
        $this->authorizeTopicVisibility('view', $manager, $topic);

        $versionSearch = trim((string) $request->string('version_q'));
        $versionAction = trim((string) $request->string('version_action'));

        $versions = $topic->versions()
            ->with(['creator:id,name,email', 'restoredFrom:id,title', 'region:id,name'])
            ->when($versionSearch !== '', function (Builder $query) use ($versionSearch) {
                $query->where(function (Builder $inner) use ($versionSearch): void {
                    $inner->where('title', 'like', '%' . $versionSearch . '%')
                        ->orWhere('answer', 'like', '%' . $versionSearch . '%')
                        ->orWhere('action', 'like', '%' . $versionSearch . '%');
                });
            })
            ->when($versionAction !== '', fn (Builder $query) => $query->where('action', $versionAction))
            ->limit(20)
            ->get();

        return view('panel.assistant.edit', $this->formData($topic, $manager) + [
            'versions' => $versions,
            'versionSearch' => $versionSearch,
            'versionAction' => $versionAction,
            'versionActionOptions' => self::VERSION_ACTIONS,
        ]);
    }

    public function update(UpdateSystemAssistantTopicRequest $request, SystemAssistantTopic $topic): RedirectResponse
    {
        $manager = $request->user();
        abort_unless($manager, 403);
        $this->authorizeTopicVisibility('update', $manager, $topic);

        $topic->update($this->payloadFromRequest($request, auth()->id(), $manager, $topic));
        $this->snapshotTopic($topic, 'updated');

        return redirect()
            ->route('assistant.topics.edit', $topic)
            ->with('status', __('Assistant topic updated successfully.'));
    }

    public function destroy(Request $request, SystemAssistantTopic $topic): RedirectResponse
    {
        $manager = $request->user();
        abort_unless($manager, 403);
        $this->authorizeTopicVisibility('delete', $manager, $topic);

        $this->snapshotTopic($topic, 'deleted');
        $topic->delete();

        return redirect()
            ->route('assistant.topics.index')
            ->with('status', __('Assistant topic deleted successfully.'));
    }

    public function restoreDefaults(Request $request): RedirectResponse
    {
        $this->authorize('restoreDefaults', SystemAssistantTopic::class);

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
                    'region_id' => null,
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

                $this->snapshotTopic($topic, 'restored_defaults');
            }
        });

        return redirect()
            ->route('assistant.topics.index')
            ->with('status', __('Assistant default topics restored successfully.'));
    }

    public function export(Request $request)
    {
        $manager = $request->user();
        abort_unless($manager, 403);
        $this->authorize('export', SystemAssistantTopic::class);

        [, , , $topics] = $this->filteredTopics($request, $manager, false);

        $payload = [
            'exported_at' => now()->toIso8601String(),
            'exported_by' => auth()->user()?->email,
            'total' => $topics->count(),
            'topics' => $topics->map(fn (SystemAssistantTopic $topic): array => [
                'slug' => $topic->slug,
                'locale' => $topic->locale,
                'region_id' => $topic->region_id,
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
        $this->authorize('import', SystemAssistantTopic::class);

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
                $regionId = blank($row['region_id'] ?? null) ? null : (int) $row['region_id'];

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

                if ($regionId !== null && ! Region::query()->whereKey($regionId)->exists()) {
                    throw ValidationException::withMessages([
                        'topics_file' => __('Topic row :row contains an unknown region.', ['row' => $index + 1]),
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
                    'region_id' => $regionId,
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

                $this->snapshotTopic($topic, 'imported');
            }
        });

        return redirect()
            ->route('assistant.topics.index')
            ->with('status', __('Assistant topics imported successfully.'));
    }

    public function restoreVersion(Request $request, SystemAssistantTopic $topic, SystemAssistantTopicVersion $version): RedirectResponse
    {
        $manager = $request->user();
        abort_unless($manager, 403);
        $this->authorizeTopicVisibility('restoreVersion', $manager, $topic);
        abort_unless($version->topic_id === $topic->id, 404);

        $hasConflict = SystemAssistantTopic::query()
            ->where('slug', $version->slug)
            ->where('locale', $version->locale)
            ->whereKeyNot($topic->id)
            ->exists();

        if ($hasConflict) {
            throw ValidationException::withMessages([
                'version' => __('This version cannot be restored because another topic already uses the same slug and locale.'),
            ]);
        }

        $topic->update([
            'slug' => $version->slug,
            'locale' => $version->locale,
            'region_id' => $version->region_id,
            'title' => $version->title,
            'answer' => $version->answer,
            'keywords' => $version->keywords ?? [],
            'suggestions' => $version->suggestions ?? [],
            'roles' => $version->roles,
            'is_active' => $version->is_active,
            'is_system' => $topic->is_system,
            'sort_order' => $version->sort_order,
            'updated_by' => auth()->id(),
        ]);

        $this->snapshotTopic($topic, 'restored_version', $version->id);

        return redirect()
            ->route('assistant.topics.edit', $topic)
            ->with('status', __('Assistant topic restored from version history.'));
    }

    private function formData(SystemAssistantTopic $topic, User $manager): array
    {
        return [
            'topic' => $topic,
            'roleOptions' => self::ROLE_OPTIONS,
            'localeOptions' => config('app.supported_locales', ['en', 'sw']),
            'regionOptions' => $this->regionOptions($manager),
            'manager' => $manager,
        ];
    }

    private function payloadFromRequest(Request $request, int $userId, User $manager, ?SystemAssistantTopic $topic = null): array
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

        $regionId = $this->resolvedRegionId($request, $manager);

        return [
            'slug' => $slug,
            'locale' => $request->input('locale'),
            'region_id' => $regionId,
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
     * @return array{0:string,1:string,2:string,3:mixed}
     */
    private function filteredTopics(Request $request, User $manager, bool $paginate = true): array
    {
        $search = trim((string) $request->string('q'));
        $locale = trim((string) $request->string('locale'));
        $scopeFilter = trim((string) $request->string('scope'));

        $query = $this->manageableTopicsQuery($manager)
            ->with('region:id,name')
            ->when($search !== '', function (Builder $builder) use ($search): void {
                $builder->where(function (Builder $inner) use ($search): void {
                    $inner->where('title', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%')
                        ->orWhere('answer', 'like', '%' . $search . '%');
                });
            })
            ->when($locale !== '', fn (Builder $builder) => $builder->where('locale', $locale))
            ->when($manager->hasSystemRole('super_admin') && $scopeFilter !== '', function (Builder $builder) use ($scopeFilter): void {
                if ($scopeFilter === 'global') {
                    $builder->whereNull('region_id');
                    return;
                }

                if (ctype_digit($scopeFilter)) {
                    $builder->where('region_id', (int) $scopeFilter);
                }
            })
            ->orderByRaw('region_id is null desc')
            ->orderBy('region_id')
            ->orderBy('locale')
            ->orderBy('sort_order')
            ->orderBy('title');

        $topics = $paginate ? $query->paginate(18)->withQueryString() : $query->get();

        return [$search, $locale, $scopeFilter, $topics];
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

    private function snapshotTopic(SystemAssistantTopic $topic, string $action, ?int $restoredFromVersionId = null): void
    {
        SystemAssistantTopicVersion::query()->create([
            'topic_id' => $topic->id,
            'slug' => $topic->slug,
            'locale' => $topic->locale,
            'region_id' => $topic->region_id,
            'title' => $topic->title,
            'answer' => $topic->answer,
            'keywords' => $topic->keywords ?? [],
            'suggestions' => $topic->suggestions ?? [],
            'roles' => $topic->roles,
            'is_active' => $topic->is_active,
            'is_system' => $topic->is_system,
            'sort_order' => $topic->sort_order,
            'action' => $action,
            'created_by' => auth()->id(),
            'restored_from_version_id' => $restoredFromVersionId,
        ]);
    }

    private function authorizeTopicVisibility(string $ability, User $user, SystemAssistantTopic $topic): void
    {
        abort_unless(Gate::forUser($user)->allows($ability, $topic), 404);
    }

    private function resolvedRegionId(Request $request, User $manager): ?int
    {
        if ($manager->hasSystemRole('regional_admin') && ! $manager->hasSystemRole('super_admin')) {
            return $manager->region_id ? (int) $manager->region_id : null;
        }

        return blank($request->input('region_id')) ? null : (int) $request->input('region_id');
    }

    private function manageableTopicsQuery(User $manager): Builder
    {
        $query = SystemAssistantTopic::query();

        if ($manager->hasSystemRole('super_admin')) {
            return $query;
        }

        return $query->where('region_id', $manager->region_id);
    }

    private function scopedInteractionsQuery(User $manager): Builder
    {
        $query = SystemAssistantInteraction::query();

        if ($manager->hasSystemRole('super_admin')) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($manager): void {
            $builder->whereHas('topic', fn (Builder $topicQuery) => $topicQuery->where('region_id', $manager->region_id))
                ->orWhereHas('user', fn (Builder $userQuery) => $userQuery->where('region_id', $manager->region_id));
        });
    }

    private function regionOptions(User $manager)
    {
        if ($manager->hasSystemRole('super_admin')) {
            return Region::query()->orderBy('name')->get(['id', 'name']);
        }

        return Region::query()->whereKey($manager->region_id)->get(['id', 'name']);
    }
}
