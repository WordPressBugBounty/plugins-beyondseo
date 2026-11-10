<?php
declare(strict_types=1);

namespace RankingCoach\Inc\Core\Seo\Services;

if (!defined('ABSPATH')) { exit; }

use RankingCoach\Inc\Core\Base\BaseConstants;
use RankingCoach\Inc\Core\Base\Traits\RcLoggerTrait;
use RankingCoach\Inc\Core\Seo\Optimiser\Base\Enums\Suggestion;
use RankingCoach\Inc\Core\Seo\Optimiser\Base\FactorSuggestion;
use RankingCoach\Inc\Traits\SingletonTrait;
use Throwable;

/**
 * The Action Plan: every setup wizard check (BaseConstants::SETUP_WIZARD_CHECKS) a published
 * page failed in its latest analysis, as a flat list of page issues ordered by priority.
 * Read by the dashboard widget (getSummary) and the Action Plan admin page (REST /action-plan).
 * Issues come from the stored analysis only; nothing is analysed here.
 */
class ActionPlanService
{
    use RcLoggerTrait;
    use SingletonTrait;

    /**
     * @return array{pagesChecked: int, lastCheckAt: int|null, issues: array<int, array>}
     */
    public function getPlan(): array
    {
        $issues = $this->collectIssues();

        // Links, scores and dates of the affected pages; one round trip for the posts and one for their meta.
        $postIds = array_values(array_unique(array_map(static fn(array $issue): int => (int) $issue['post']['id'], $issues)));
        if ($postIds !== []) {
            _prime_post_caches($postIds, false, false);
            update_meta_cache('post', $postIds);
        }
        $pages = [];
        foreach ($issues as &$issue) {
            $postId = (int) $issue['post']['id'];
            $pages[$postId] ??= $this->describePage($postId, (string) $issue['post']['title'], (string) $issue['post']['type']);
            $issue['post'] = $pages[$postId];
        }
        unset($issue);

        $stats = $this->getCheckStats();

        return [
            'pagesChecked' => $stats['pagesChecked'],
            'lastCheckAt' => $stats['lastCheckAt'],
            'issues' => $issues,
        ];
    }

    /**
     * What the dashboard widget shows: whether a check ran, how many issues and pages are open,
     * and the first issue to work on. Skips the per-page details getPlan() resolves.
     *
     * @return array{checked: bool, issueCount: int, pageCount: int, next: array{title: string, pageTitle: string}|null}
     */
    public function getSummary(): array
    {
        $issues = $this->collectIssues();
        $next = $issues[0] ?? null;
        $pageIds = array_map(static fn(array $issue): int => (int) $issue['post']['id'], $issues);

        return [
            'checked' => $this->getCheckStats()['pagesChecked'] > 0,
            'issueCount' => count($issues),
            'pageCount' => count(array_unique($pageIds)),
            'next' => $next === null ? null : ['title' => $next['title'], 'pageTitle' => $next['post']['title']],
        ];
    }

    /**
     * The open issues of published pages, critical first; the pages of one priority alphabetically,
     * each page's issues in catalogue order. `post` carries only id, title and type here.
     *
     * @return array<int, array{issueType: string, title: string, description: string, priority: int, post: array{id: int, title: string, type: string}}>
     */
    private function collectIssues(): array
    {
        $grouped = SeoOptimiserService::getInstance()->getSeoIssues(BaseConstants::SETUP_WIZARD_CHECKS);
        $issues = [];

        foreach (BaseConstants::SETUP_WIZARD_CHECKS as $order => $issueType) {
            if (empty($grouped[$issueType]['posts'])) {
                continue;
            }
            $priority = $this->getPriority($issueType);

            foreach ($grouped[$issueType]['posts'] as $post) {
                if ($post['post_status'] !== 'publish' || !in_array($post['post_type'], ALLOWED_RANKINGCOACH_CUSTOM_TYPES, true)) {
                    continue;
                }
                $postId = (int) $post['id'];
                $title = (string) $post['post_title'];

                $issues[] = [
                    'issueType' => $issueType,
                    'title' => (string) $grouped[$issueType]['title'],
                    'description' => (string) $grouped[$issueType]['description'],
                    'priority' => $priority,
                    'order' => $order,
                    'post' => ['id' => $postId, 'title' => $title !== '' ? $title : '#' . $postId, 'type' => (string) $post['post_type']],
                ];
            }
        }

        usort($issues, static fn(array $a, array $b): int =>
            [$a['priority'], $a['post']['title'], $a['post']['id'], $a['order']]
            <=> [$b['priority'], $b['post']['title'], $b['post']['id'], $b['order']]
        );
        foreach ($issues as &$issue) {
            unset($issue['order']);
        }
        unset($issue);

        return $issues;
    }

    /**
     * @return array{id: int, title: string, link: string, editLink: string, type: string, score: int|null, analyzedAt: int|null}
     */
    private function describePage(int $postId, string $title, string $type): array
    {
        $score = get_post_meta($postId, BaseConstants::OPTION_ANALYSIS_SEO_SCORE, true);
        $analyzedAt = get_post_meta($postId, BaseConstants::OPTION_ANALYSIS_DATE_TIMESTAMP, true);
        $editLink = get_edit_post_link($postId, 'raw') ?: admin_url('post.php?post=' . $postId . '&action=edit');

        return [
            'id' => $postId,
            'title' => $title,
            'link' => (string) (get_permalink($postId) ?: ''),
            'editLink' => (string) $editLink,
            'type' => $type,
            'score' => is_numeric($score) ? (int) round((float) $score) : null,
            'analyzedAt' => is_numeric($analyzedAt) ? (int) $analyzedAt : null,
        ];
    }

    private function getPriority(string $issueType): int
    {
        try {
            return (int) (Suggestion::getDescription($issueType)['priority'] ?? FactorSuggestion::PRIORITY_LOW);
        } catch (Throwable $e) {
            return FactorSuggestion::PRIORITY_LOW;
        }
    }

    /**
     * Published pages with a stored analysis and the time of the latest one, from the post meta
     * every analysis writes (the same rows the average score on the widget is built from).
     *
     * @return array{pagesChecked: int, lastCheckAt: int|null}
     */
    private function getCheckStats(): array
    {
        global $wpdb;

        $types = array_values(ALLOWED_RANKINGCOACH_CUSTOM_TYPES);
        $placeholders = implode(', ', array_fill(0, count($types), '%s'));
        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT COUNT(DISTINCT pm.post_id) AS pages, MAX(CAST(pm.meta_value AS UNSIGNED)) AS last_check
             FROM {$wpdb->postmeta} pm
             INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
             WHERE pm.meta_key = %s
               AND p.post_status = 'publish'
               AND p.post_type IN ({$placeholders})
               AND pm.meta_value REGEXP '^[0-9]+$'",
            BaseConstants::OPTION_ANALYSIS_DATE_TIMESTAMP,
            ...$types
        ), ARRAY_A);

        return [
            'pagesChecked' => (int) ($row['pages'] ?? 0),
            'lastCheckAt' => !empty($row['last_check']) ? (int) $row['last_check'] : null,
        ];
    }
}
