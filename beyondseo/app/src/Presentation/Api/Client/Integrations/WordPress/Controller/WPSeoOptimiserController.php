<?php
declare(strict_types=1);

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Controller;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Adapters\WordPressProvider;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Models\Results\OptimiserResult;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Services\WPSeoOptimiserService;
use BeyondSEO\Infrastructure\Traits\ResponseErrorTrait;
use BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\Seo\SeoAnalysisRequestDto;
use BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\Seo\SeoDataExtractionResponseDto;
use BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\Seo\SeoOptimiserResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\Controller\HttpController;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Summary;
use BeyondSEODeps\DDD\Presentation\Base\Router\Routes\Get;
use BeyondSEODeps\DDD\Presentation\Base\Router\Routes\Post;
use BeyondSEODeps\DDD\Presentation\Base\Router\Routes\Route;
use Exception;
use RankingCoach\Inc\Core\Initializers\Hooks;
use RankingCoach\Inc\Modules\ModuleManager;
use Throwable;

/**
 * Controller for SEO analysis in WordPress
 */
#[Route('optimiser/{postId}')]
class WPSeoOptimiserController extends HttpController
{
    use ResponseErrorTrait;

    /**
     * Extract SEO data
     * @param SeoAnalysisRequestDto $requestDto
     * @param WPSeoOptimiserService $seoOptimiserService
     * @return SeoDataExtractionResponseDto
     * @throws Throwable
     */
    #[Get('/data')]
    #[Summary('Extract SEO Data')]
    public function extractSeoData(
        SeoAnalysisRequestDto $requestDto,
        WPSeoOptimiserService $seoOptimiserService
    ): SeoDataExtractionResponseDto
    {
        $responseDto = new SeoDataExtractionResponseDto();
        $responseDto->format = $requestDto->export;

        if ($requestDto->export === 'csv') {
            $csvData = $seoOptimiserService->extractData(true);
            $responseDto->csv = $csvData;
            return $responseDto;
        }

        $data = $seoOptimiserService->extractData();
        $responseDto->jsonData = $data;
        return $responseDto;
    }

    /**
     * Retrieve the SEO Optimiser
     * @param SeoAnalysisRequestDto $requestDto
     * @param WPSeoOptimiserService $seoOptimiserService
     * @return SeoOptimiserResponseDto
     * @throws Throwable
     */
    #[Get]
    #[Summary('Retrieve SEO Optimiser')]
    public function retrieveSeoOptimiser(
        SeoAnalysisRequestDto $requestDto,
        WPSeoOptimiserService $seoOptimiserService
    ): SeoOptimiserResponseDto
    {
        $response = new SeoOptimiserResponseDto();
        try {
            $optimiser = $seoOptimiserService->analyzeFullOptimiser($requestDto->postId, [], true);
            $response->analyseResult = OptimiserResult::fromOptimiser($optimiser);
        } catch (Exception $e) {
            return $this->processException($e, SeoOptimiserResponseDto::class);
        }

        return $response;
    }

    /**
     * Process the SEO Optimizer
     * @param SeoAnalysisRequestDto $requestDto
     * @param WPSeoOptimiserService $seoOptimiserService
     * @return SeoOptimiserResponseDto
     * @throws Throwable
     */
    #[Post]
    #[Summary('Proceed SEO Optimiser')]
    public function proceedSeoOptimiser(
        SeoAnalysisRequestDto $requestDto,
        WPSeoOptimiserService  $seoOptimiserService
    ): SeoOptimiserResponseDto
    {
        $response = new SeoOptimiserResponseDto();
        $params = $seoOptimiserService->prepareSeoOptimiserQueryParams($requestDto);
        
        try {
            // Check if analysis should be throttled
            $shouldThrottle = apply_filters(
                // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
                Hooks::RANKINGCOACH_FILTER_SHOULD_THROTTLE_SEO_ANALYSIS,
                WordPressProvider::shouldThrottleAnalysis($requestDto->postId),
                $requestDto->postId
            );

            if ($shouldThrottle) {
                throw new Exception('SEO analysis is being throttled. Please try again later.');
            }
            ModuleManager::instance()->initialize()->linkAnalyzer()->hooksComponent->analyzeLinks($requestDto->postId);
            $optimiser = $seoOptimiserService->analyzeFullOptimiser($requestDto->postId, $params);

            $seoOptimiserService->calculateAndSaveAverageScore();

            $response->analyseResult = OptimiserResult::fromOptimiser($optimiser);
        } catch (Exception $e) {
            return $this->processException($e, SeoOptimiserResponseDto::class);
        }

        return $response;
    }
}
