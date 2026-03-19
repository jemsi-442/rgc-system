<?php

namespace App\Services;

use App\Models\SystemAssistantInteraction;
use App\Models\SystemAssistantTopic;
use App\Models\User;
use App\Support\SystemAssistantKnowledge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SystemAssistantService
{
    private const ROLE_PRIORITY = [
        'super_admin',
        'regional_admin',
        'district_admin',
        'branch_admin',
        'bishop',
        'pastor',
        'accountant',
        'member',
    ];

    /**
     * @return array{topic:string, answer:string, suggestions:array<int,string>, confidence:int, source:string, interaction_id:int|null}
     */
    public function respond(string $question, string $locale = 'en', ?User $user = null, ?Request $request = null): array
    {
        $normalizedQuestion = $this->normalize($question);
        $tokens = $this->tokens($normalizedQuestion);
        $roles = $this->orderedRoles($user);
        $topics = $this->topicsFor($locale, $roles);
        $bestMatch = null;
        $bestScore = 0;
        $bestPriority = -1;

        foreach ($topics as $topic) {
            $score = $this->scoreTopic($normalizedQuestion, $tokens, $topic['keywords'] ?? []);
            $priority = $this->topicPriority($topic, $roles);

            if ($score > $bestScore || ($score === $bestScore && $priority > $bestPriority)) {
                $bestScore = $score;
                $bestMatch = $topic;
                $bestPriority = $priority;
            }
        }

        if ($bestMatch === null || $bestScore < 6) {
            $fallback = SystemAssistantKnowledge::fallback($locale);
            $payload = [
                'topic' => $fallback['topic'],
                'answer' => $fallback['answer'],
                'suggestions' => $fallback['suggestions'],
                'confidence' => 0,
                'source' => 'fallback',
                'interaction_id' => null,
            ];

            $interaction = $this->recordInteraction($payload, $question, $normalizedQuestion, $roles, $user, $request, null);
            $payload['interaction_id'] = $interaction?->id;

            return $payload;
        }

        $payload = [
            'topic' => $bestMatch['slug'],
            'answer' => $bestMatch['answer'],
            'suggestions' => $bestMatch['suggestions'] ?? [],
            'confidence' => $bestScore,
            'source' => $bestMatch['source'] ?? 'database',
            'interaction_id' => null,
        ];

        $interaction = $this->recordInteraction($payload, $question, $normalizedQuestion, $roles, $user, $request, $bestMatch['id'] ?? null);
        $payload['interaction_id'] = $interaction?->id;

        return $payload;
    }

    /**
     * @return array<int, string>
     */
    public function starterSuggestions(?User $user, string $locale = 'en'): array
    {
        if (! $user) {
            return $locale === 'sw'
                ? [
                    'Ninawezaje kusajili akaunti?',
                    'Ninawezaje kutoa sadaka au offering?',
                    'Roles za mfumo ni zipi?',
                ]
                : [
                    'How do I register an account?',
                    'How do I make an offering payment?',
                    'What roles exist in the system?',
                ];
        }

        if ($user->hasSystemRole('super_admin')) {
            return $locale === 'sw'
                ? [
                    'Ninawezaje kuongeza regional admin?',
                    'Nawezaje kutuma tangazo kwa branches nilizochagua?',
                    'Ninawezaje ku-import branches kwa CSV au Excel?',
                ]
                : [
                    'How do I create a regional admin?',
                    'How do I send an announcement to selected branches?',
                    'How do I import branches from CSV or Excel?',
                ];
        }

        if ($user->hasSystemRole('regional_admin')) {
            return $locale === 'sw'
                ? [
                    'Nawezaje kutuma tangazo kwa district moja tu?',
                    'Nawezaje kutuma tangazo kwa branch moja ndani ya region yangu?',
                    'Dashboard yangu inaonyesha nini kwa region yangu?',
                ]
                : [
                    'How do I send an announcement to one district only?',
                    'How do I send an announcement to one branch inside my region?',
                    'What does my regional dashboard show?',
                ];
        }

        if ($user->hasSystemRole('district_admin')) {
            return $locale === 'sw'
                ? [
                    'Tangazo la district admin linafika kwa nani?',
                    'Nawezaje kuona branches za district yangu?',
                    'Payment alerts zinafanyaje kazi kwenye scope yangu?',
                ]
                : [
                    'Who receives a district admin announcement?',
                    'How do I see branches in my district?',
                    'How do payment alerts work in my scope?',
                ];
        }

        if ($user->hasAnySystemRole(['branch_admin', 'pastor', 'bishop', 'accountant'])) {
            return $locale === 'sw'
                ? [
                    'Nawezaje kurecord offering au expense ya branch?',
                    'Nawezaje ku-review payment alerts?',
                    'Nawezaje kutuma files kwenye branch chat?',
                ]
                : [
                    'How do I record a branch offering or expense?',
                    'How do I review payment alerts?',
                    'How do I send files in branch chat?',
                ];
        }

        return $locale === 'sw'
            ? [
                'Ninawezaje kutoa sadaka au offering?',
                'Receipt ya malipo inapatikana wapi?',
                'Ninawezaje kubadili lugha?',
            ]
            : [
                'How do I make an offering payment?',
                'Where do I download my receipt?',
                'How do I switch language?',
            ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function topicsFor(string $locale, array $roles): array
    {
        $locale = $locale === 'sw' ? 'sw' : 'en';

        if (Schema::hasTable('system_assistant_topics')) {
            $topics = $this->databaseTopicsFor($locale, $roles);

            if ($topics !== []) {
                return $topics;
            }

            if ($locale !== 'en') {
                $fallbackTopics = $this->databaseTopicsFor('en', $roles);

                if ($fallbackTopics !== []) {
                    return $fallbackTopics;
                }
            }
        }

        $topics = array_values(array_filter(
            SystemAssistantKnowledge::topics($locale),
            fn (array $topic): bool => $this->topicMatchesRoles($topic['roles'] ?? null, $roles)
        ));

        if ($topics === [] && $locale !== 'en') {
            return array_values(array_filter(
                SystemAssistantKnowledge::topics('en'),
                fn (array $topic): bool => $this->topicMatchesRoles($topic['roles'] ?? null, $roles)
            ));
        }

        return array_map(fn (array $topic): array => $topic + ['source' => 'static'], $topics);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function databaseTopicsFor(string $locale, array $roles): array
    {
        return SystemAssistantTopic::query()
            ->where('locale', $locale)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get()
            ->filter(fn (SystemAssistantTopic $topic): bool => $this->topicMatchesRoles($topic->roles, $roles))
            ->map(fn (SystemAssistantTopic $topic): array => [
                'id' => $topic->id,
                'slug' => $topic->slug,
                'title' => $topic->title,
                'answer' => $topic->answer,
                'keywords' => $topic->keywords ?? [],
                'suggestions' => $topic->suggestions ?? [],
                'roles' => $topic->roles,
                'source' => 'database',
            ])
            ->values()
            ->all();
    }

    protected function topicMatchesRoles(?array $topicRoles, array $roles): bool
    {
        if ($topicRoles === null || $topicRoles === []) {
            return true;
        }

        if ($roles === []) {
            return false;
        }

        return count(array_intersect($topicRoles, $roles)) > 0;
    }

    /**
     * @param array<int, string> $keywords
     */
    protected function scoreTopic(string $question, array $tokens, array $keywords): int
    {
        $score = 0;

        foreach ($keywords as $keyword) {
            $normalizedKeyword = $this->normalize($keyword);

            if ($normalizedKeyword === '') {
                continue;
            }

            if (str_contains($question, $normalizedKeyword)) {
                $score += str_contains($normalizedKeyword, ' ') ? 7 : 5;
                continue;
            }

            $keywordTokens = $this->tokens($normalizedKeyword);
            $overlap = count(array_intersect($tokens, $keywordTokens));

            if ($overlap > 0) {
                $score += $overlap * 2;
            }
        }

        return $score;
    }

    protected function topicPriority(array $topic, array $roles): int
    {
        $priority = 0;
        $topicRoles = $topic['roles'] ?? null;

        if (is_array($topicRoles) && $topicRoles !== []) {
            $priority += 10;

            foreach ($roles as $index => $role) {
                if (in_array($role, $topicRoles, true)) {
                    $priority += max(1, 8 - $index);
                    break;
                }
            }
        }

        if (($topic['source'] ?? null) === 'database') {
            $priority += 1;
        }

        return $priority;
    }

    protected function recordInteraction(array $payload, string $question, string $normalizedQuestion, array $roles, ?User $user, ?Request $request, ?int $matchedTopicId): ?SystemAssistantInteraction
    {
        if (! Schema::hasTable('system_assistant_interactions')) {
            return null;
        }

        return SystemAssistantInteraction::query()->create([
            'user_id' => $user?->id,
            'locale' => app()->getLocale(),
            'question' => $question,
            'normalized_question' => $normalizedQuestion,
            'matched_topic_id' => $matchedTopicId,
            'matched_slug' => $payload['topic'],
            'source' => $payload['source'],
            'confidence' => $payload['confidence'],
            'answer' => $payload['answer'],
            'role_snapshot' => $roles,
            'ip_address' => $request?->ip(),
            'user_agent' => Str::limit((string) $request?->userAgent(), 500, ''),
        ]);
    }

    /**
     * @return array<int, string>
     */
    protected function orderedRoles(?User $user): array
    {
        if (! $user) {
            return [];
        }

        return collect(self::ROLE_PRIORITY)
            ->filter(fn (string $role): bool => $user->hasSystemRole($role))
            ->values()
            ->all();
    }

    protected function normalize(string $value): string
    {
        return (string) Str::of($value)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9\s]+/', ' ')
            ->replaceMatches('/\s+/', ' ')
            ->trim();
    }

    /**
     * @return array<int, string>
     */
    protected function tokens(string $value): array
    {
        if ($value === '') {
            return [];
        }

        return array_values(array_filter(explode(' ', $value)));
    }
}
