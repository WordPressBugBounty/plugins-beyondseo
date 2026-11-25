<?php
declare(strict_types=1);

namespace RankingCoach\Inc\Core\Rest\Controllers;

if (!defined('ABSPATH')) {
    exit;
}

use RankingCoach\Inc\Core\Api\Content\ContentApiManager;
use RankingCoach\Inc\Core\Base\Traits\RcLoggerTrait;
use RankingCoach\Inc\Core\DB\DatabaseManager;
use RankingCoach\Inc\Core\DB\DatabaseTablesManager;
use RankingCoach\Inc\Core\Helpers\Capabilities;
use RankingCoach\Inc\Core\Helpers\Traits\RcApiTrait;
use RankingCoach\Inc\Core\Initializers\Hooks;
use RankingCoach\Inc\Core\Seo\Optimiser\Base\Enums\FactorStatus;
use RankingCoach\Inc\Core\Seo\Optimiser\Base\FactorSuggestions;
use RankingCoach\Inc\Core\Seo\Optimiser\Base\Models\Results\OptimiserResult;
use RankingCoach\Inc\Core\Seo\Optimiser\Base\OptimiserContexts;
use RankingCoach\Inc\Core\Seo\Optimiser\SeoOptimiser;
use RankingCoach\Inc\Core\Seo\Services\SeoOptimiserService;
use RankingCoach\Inc\Modules\ModuleManager;
use Throwable;
use WP_REST_Request;
use WP_REST_Response;

class OptimiserController
{
    use RcApiTrait;
    use RcLoggerTrait;

    public function get(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $postId = (int) $request->get_param('postId');
            $analyseResult = $this->retrieveAnalysis($postId);

            return new WP_REST_Response(['analyseResult' => $analyseResult], 200);
        } catch (Throwable $e) {
            $this->log('Error in OptimiserController::get: ' . $e->getMessage(), 'ERROR');
            return $this->generateErrorResponse($e, null, 500);
        }
    }

    public function getBatch(WP_REST_Request $request): WP_REST_Response
    {
        $results = [];

        foreach ($request->get_param('postIds') as $postId) {
            $postId = (int) $postId;

            if (!Capabilities::canEditPostSeo($postId)) {
                $results[(string) $postId] = null;
                continue;
            }

            try {
                $analyseResult = $this->retrieveAnalysis($postId);
                $score = is_array($analyseResult)
                    ? (float) ($analyseResult['score'] ?? 0)
                    : (float) ($analyseResult->score ?? 0);
                $score = $score > 1 ? round($score) : round($score * 100);

                if ($score === 0.0 && !$this->isUnsavedNewPost($postId)) {
                    $requestForPost = new WP_REST_Request('POST');
                    $requestForPost->set_param('postId', $postId);
                    $analysisResponse = $this->run($requestForPost);
                    $analysisData = $analysisResponse->get_data();

                    if ($analysisResponse->get_status() === 200 && isset($analysisData['analyseResult'])) {
                        $analyseResult = $analysisData['analyseResult'];
                    }
                }

                $results[(string) $postId] = $this->formatPostListResult($analyseResult);
            } catch (Throwable $e) {
                $this->log('Error in OptimiserController::getBatch for post ' . $postId . ': ' . $e->getMessage(), 'ERROR');
                $results[(string) $postId] = null;
            }
        }

        return new WP_REST_Response(['results' => $results], 200);
    }

    private function formatPostListResult(array|OptimiserResult $analyseResult): array
    {
        $rawScore = is_array($analyseResult)
            ? (float) ($analyseResult['score'] ?? 0)
            : (float) ($analyseResult->score ?? 0);
        $score = (int) max(0, min(100, round($rawScore > 1 ? $rawScore : $rawScore * 100)));
        $topSuggestions = is_array($analyseResult)
            ? ($analyseResult['topSuggestions']['elements'] ?? [])
            : ($analyseResult->topSuggestions->elements ?? []);
        $recommendationCount = is_array($analyseResult)
            ? (int) ($analyseResult['totalSuggestionsCount'] ?? count($topSuggestions))
            : (int) ($analyseResult->totalSuggestionsCount ?? count($topSuggestions));
        $contexts = is_array($analyseResult)
            ? ($analyseResult['contexts']['elements'] ?? [])
            : ($analyseResult->contexts->elements ?? []);
        $checks = [];

        foreach ($contexts as $context) {
            $factors = is_array($context)
                ? ($context['factors']['elements'] ?? [])
                : ($context->factors->elements ?? []);

            foreach ($factors as $factor) {
                $title = is_array($factor) ? ($factor['factorName'] ?? '') : ($factor->factorName ?? '');
                if ($title === '') {
                    continue;
                }

                $status = is_array($factor) ? ($factor['status'] ?? '') : ($factor->status ?? '');
                $checks[] = [
                    'title' => (string) $title,
                    'isGood' => $status === FactorStatus::COMPLETED,
                ];

                if (count($checks) === 4) {
                    break 2;
                }
            }
        }

        return [
            'score' => $score,
            'recommendationCount' => max(0, $recommendationCount),
            'checks' => $checks,
        ];
    }

    /** @see SeoOptimiserService::getSeoIssues() */
    public function getSeoIssues(array $suggestions = [], ?int $postId = null): array
    {
        return SeoOptimiserService::getInstance()->getSeoIssues($suggestions, $postId);
    }

    public function getSeoIssuesRest(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $suggestionsParam = $request->get_param('suggestions');
            $suggestions = $suggestionsParam === null ? [] : json_decode($suggestionsParam);
            $postId = $request->get_param('post_id');
            $issues = $this->getSeoIssues($suggestions, $postId === null ? null : (int) $postId);

            return new WP_REST_Response((object) $issues, 200);
        } catch (Throwable $e) {
            $this->log('Error in OptimiserController::getSeoIssuesRest: ' . $e->getMessage(), 'ERROR');
            return $this->generateErrorResponse($e, null, 500);
        }
    }

    public function run(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $postId = (int) $request->get_param('postId');

            $shouldThrottle = apply_filters(Hooks::RANKINGCOACH_FILTER_SHOULD_THROTTLE_SEO_ANALYSIS, false, $postId);
            $review = ['postId' => $postId, 'completed' => false, 'score' => null, 'milestone' => false];

            if ($this->isUnsavedNewPost($postId)) {
                $analyseResult = $this->getAnalyseResult($postId);
            } elseif (!$shouldThrottle) {
                ModuleManager::instance()->initialize()->linkAnalyzer()->hooksComponent->analyzeLinks($postId);
                $reviewInteractive = $request->get_param('reviewContext') === 'editor';
                $analyseResult = $this->performAnalysis($postId, $reviewInteractive, $review);
            } else {
                $analyseResult = $this->getAnalyseResult($postId);
            }

            return new WP_REST_Response(['analyseResult' => $analyseResult, 'review' => $review], 200);
        } catch (Throwable $e) {
            $this->log('Error in OptimiserController::run: ' . $e->getMessage(), 'ERROR');
            return $this->generateErrorResponse($e, null, 500);
        }
    }

    private function retrieveAnalysis(int $postId): array|OptimiserResult
    {
        $result = $this->getAnalyseResult($postId);

        // A read of a post that was never analyzed runs the first analysis on the spot.
        if ($result instanceof OptimiserResult && empty($result->analyzedAt) && !$this->isUnsavedNewPost($postId)) {
            return $this->performAnalysis($postId);
        }

        return $result;
    }

    /**
     * Whether the post is still the empty `auto-draft` WordPress inserts on every "Add New"
     * page view. The editor meta box mounts on it, but it must not be analyzed: every abandoned
     * auto-draft would leave orphaned optimiser rows behind. The first save turns the post into
     * a draft, and that save triggers the first analysis.
     */
    private function isUnsavedNewPost(int $postId): bool
    {
        return get_post_status($postId) === 'auto-draft';
    }

    private function performAnalysis(int $postId, bool $reviewInteractive = false, ?array &$review = null): OptimiserResult
    {
        $seoOptimiser = SeoOptimiserService::getInstance()->analyzeFullOptimiser($postId, [], false, $reviewInteractive, $analysisCompleted);
        $review = ['postId' => $postId, 'completed' => false, 'score' => null, 'milestone' => false];
        if ($reviewInteractive && $analysisCompleted) {
            try {
                $review = apply_filters('rankingcoach_review_analysis_result', $review, 'post', $postId);
            } catch (Throwable $e) {
            }
        }
        return OptimiserResult::fromOptimiser($seoOptimiser);
    }

    private function getAnalyseResult(int $postId): array|OptimiserResult
    {
        $seoOptimiser = SeoOptimiserService::getInstance()->getOptimiserByPostId($postId);

        if ($seoOptimiser === null) {
            return new OptimiserResult($postId, 0, new OptimiserContexts(), new FactorSuggestions(), '');
        }

        return OptimiserResult::fromOptimiser($seoOptimiser);
    }

    public function exportData(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $postId = (int) $request->get_param('postId');
            $export = sanitize_text_field($request->get_param('export') ?? 'json');

            $row = DatabaseManager::getInstance()
                ->table(DatabaseTablesManager::DATABASE_SEO_OPTIMISERS)
                ->select(['*'])
                ->where('postId', $postId)
                ->first();

            $row = is_object($row) ? (array) $row : ($row ?: []);

            if ($export === 'csv') {
                $csvLines = [];
                if (!empty($row)) {
                    $csvLines[] = implode(',', array_keys($row));
                    $csvLines[] = implode(',', array_map(fn($v) => '"' . str_replace('"', '""', (string) $v) . '"', array_values($row)));
                }
                $csv = implode("\n", $csvLines);

                $response = new WP_REST_Response($csv, 200);
                $response->header('Content-Type', 'text/csv');
                return $response;
            }

            return new WP_REST_Response(['format' => 'json', 'jsonData' => $row, 'csv' => ''], 200);
        } catch (Throwable $e) {
            $this->log('Error in OptimiserController::exportData: ' . $e->getMessage(), 'ERROR');
            return $this->generateErrorResponse($e, null, 500);
        }
    }
}
