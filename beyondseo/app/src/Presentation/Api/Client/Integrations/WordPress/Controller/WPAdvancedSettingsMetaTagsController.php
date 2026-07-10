<?php
declare( strict_types=1 );

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Controller;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Seo\Services\WPWebPageService;
use BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\AdvancedSettingsMetaTagsGetResponseDto;
use BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\AdvancedSettingsMetaTagsPostRequestDto;
use BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\MetaTagsGetRequestDto;
use BeyondSEODeps\DDD\Presentation\Base\Controller\HttpController;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Summary;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Tag;
use BeyondSEODeps\DDD\Presentation\Base\Router\Routes\Get;
use BeyondSEODeps\DDD\Presentation\Base\Router\Routes\Post;
use BeyondSEODeps\DDD\Presentation\Base\Router\Routes\Route;
use Throwable;

/**
 * Class WPSocialMetaTagsController
 */
#[Route('advancedSettings/{postId}')]
#[Tag(name: 'AdvancedSettings', group: 'Modules', description: 'Operations for advanced settings metaTags')]
class WPAdvancedSettingsMetaTagsController extends HttpController {

    /**
     * Get MetaTags
     *
     * @param MetaTagsGetRequestDto $requestDto
     * @param WPWebPageService $webPageService
     * @return AdvancedSettingsMetaTagsGetResponseDto
     */
	#[Get]
	#[Summary('Get all advanced settings metaTags')]
	public function getAllAdvancedSettingsMetaTags(
        MetaTagsGetRequestDto $requestDto,
        WPWebPageService $webPageService
    ): AdvancedSettingsMetaTagsGetResponseDto
	{
        $response = new AdvancedSettingsMetaTagsGetResponseDto();

        /** @var array $metaTags */
        $advancedSettings = $webPageService->getAdvancedSettingsMetaTags($requestDto);

        $response->noindexForPage = (bool)$advancedSettings['noindexForPage'];
        $response->excludeSitemapForPage = (bool)$advancedSettings['excludeSitemapForPage'];
        $response->disableAutoLinks = (bool)$advancedSettings['disableAutoLinks'] ?? false;
        $response->canonicalUrl = $advancedSettings['canonicalUrl'];
        $response->viewportForPage = (bool)$advancedSettings['viewportForPage'] ?? false;

        return $response;
	}

    /**
     * Save MetaTag Keywords
     *
     * @param AdvancedSettingsMetaTagsPostRequestDto $requestDto
     * @param WPWebPageService $webPageService
     * @return AdvancedSettingsMetaTagsGetResponseDto
     * @throws Throwable
     */
    #[Post]
    #[Summary('Update all advanced settings metaTags')]
    public function updateAllAdvancedSettingsMetaTag (
        AdvancedSettingsMetaTagsPostRequestDto $requestDto,
        WPWebPageService $webPageService
    ): AdvancedSettingsMetaTagsGetResponseDto {

        $webPageService->throwErrors = true;
        $response = new AdvancedSettingsMetaTagsGetResponseDto();

        $webPageService->saveAdvancedSettingsMetaTags($requestDto);

        /** @var array $advanceSettings */
        $advanceSettings = $webPageService->getAdvancedSettingsMetaTags($requestDto);

        $response->noindexForPage = (bool)$advanceSettings['noindexForPage'];
        $response->excludeSitemapForPage = (bool)$advanceSettings['excludeSitemapForPage'];
        $response->disableAutoLinks = (bool)$advanceSettings['disableAutoLinks'] ?? false;
        $response->canonicalUrl = $advanceSettings['canonicalUrl'];
        $response->viewportForPage = (bool)$advanceSettings['viewportForPage'] ?? false;

        return $response;
    }
}