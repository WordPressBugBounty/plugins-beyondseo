<?php
declare( strict_types=1 );

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Controller;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Seo\Services\WPWebPageService;
use BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\MetaTagsGetRequestDto;
use BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\SocialImageSourcesResponseDto;
use BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\SocialMetaTagsGetResponseDto;
use BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\SocialMetaTagsPostRequestDto;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use BeyondSEODeps\DDD\Presentation\Base\Controller\HttpController;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Summary;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Tag;
use BeyondSEODeps\DDD\Presentation\Base\Router\Routes\Get;
use BeyondSEODeps\DDD\Presentation\Base\Router\Routes\Post;
use BeyondSEODeps\DDD\Presentation\Base\Router\Routes\Route;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;
use Throwable;

/**
 * Class WPSocialMetaTagsController
 */
#[Route('social/{postId}')]
#[Tag(name: 'Social', group: 'Modules', description: 'Operations for Social MetaTags')]
class WPSocialMetaTagsController extends HttpController {

    /**
     * Get MetaTags
     *
     * @param MetaTagsGetRequestDto $requestDto
     * @param WPWebPageService $webPageService
     * @return SocialMetaTagsGetResponseDto
     * @throws InvalidArgumentException
     */
	#[Get]
	#[Summary('Get all Social MetaTags')]
	public function getAllSocialMetaTags(
        MetaTagsGetRequestDto $requestDto,
        WPWebPageService $webPageService
    ): SocialMetaTagsGetResponseDto
	{
        $response = new SocialMetaTagsGetResponseDto();

        /** @var array $metaTags */
        $metaTags = $webPageService->getSocialMetaTags($requestDto);
        $response->social_title = $metaTags['social_title'];
        $response->social_description = $metaTags['social_description'];
        
        // Add the selected image source information
        $response->selected_image_source = $webPageService->getSelectedSocialImageSource($requestDto->postId);
        $response->selected_image_url = $webPageService->getSelectedSocialImageUrl($requestDto->postId);

        return $response;
	}

    /**
     * Save MetaTag Keywords
     *
     * @param SocialMetaTagsPostRequestDto $requestDto
     * @param WPWebPageService $webPageService
     * @return SocialMetaTagsGetResponseDto
     * @throws Throwable
     */
    #[Post]
    #[Summary('Update all Social MetaTags')]
    public function updateAllSocialMetaTag (
        SocialMetaTagsPostRequestDto $requestDto,
        WPWebPageService $webPageService
    ): SocialMetaTagsGetResponseDto {

        $webPageService->throwErrors = true;
        $response = new SocialMetaTagsGetResponseDto();

        $webPageService->saveSocialMetaTags($requestDto);

        /** @var array $metaTags */
        $metaTags = $webPageService->getSocialMetaTags($requestDto);
        $response->social_title = $metaTags['social_title'];
        $response->social_description = $metaTags['social_description'];
        
        // Add the selected image source information in response
        $response->selected_image_source = $webPageService->getSelectedSocialImageSource($requestDto->postId);
        $response->selected_image_url = $webPageService->getSelectedSocialImageUrl($requestDto->postId);

        return $response;
    }

    /**
     * Get Social Image Sources
     *
     * @param MetaTagsGetRequestDto $requestDto
     * @param WPWebPageService $webPageService
     * @return SocialImageSourcesResponseDto
     */
    #[Get('/image_sources')]
    #[Summary('Get all Social Image Sources')]
    public function getSocialImageSources(
        MetaTagsGetRequestDto $requestDto,
        WPWebPageService $webPageService
    ): SocialImageSourcesResponseDto {
        $response = new SocialImageSourcesResponseDto();
        $response->image_sources = $webPageService->getSocialImageSources($requestDto->postId);
        return $response;
    }
}