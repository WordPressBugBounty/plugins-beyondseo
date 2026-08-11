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
use RankingCoach\Inc\Core\Helpers\Traits\RcApiTrait;
use RankingCoach\Inc\Core\Initializers\Hooks;
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

    public function run(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $postId = (int) $request->get_param('postId');

            $shouldThrottle = apply_filters(Hooks::RANKINGCOACH_FILTER_SHOULD_THROTTLE_SEO_ANALYSIS, false, $postId);

            if (!$shouldThrottle) {
                ModuleManager::instance()->initialize()->linkAnalyzer()->hooksComponent->analyzeLinks($postId);
                $analyseResult = $this->performAnalysis($postId);
            } else {
                $analyseResult = $this->getAnalyseResult($postId);
            }

            return new WP_REST_Response(['analyseResult' => $analyseResult], 200);
        } catch (Throwable $e) {
            $this->log('Error in OptimiserController::run: ' . $e->getMessage(), 'ERROR');
            return $this->generateErrorResponse($e, null, 500);
        }
    }

    private function retrieveAnalysis(int $postId): array|OptimiserResult
    {
        $result = $this->getAnalyseResult($postId);

        if ($result instanceof OptimiserResult && empty($result->analyzedAt)) {
            return $this->performAnalysis($postId);
        }

        return $result;
    }

    private function performAnalysis(int $postId): OptimiserResult
    {
        $seoOptimiser = SeoOptimiserService::getInstance()->analyzeFullOptimiser($postId, [], false);
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
