<?php /** @noinspection PhpExpressionResultUnusedInspection */
declare( strict_types=1 );

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Controller;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Base\Repo\RC\Attributes\RCLoad;
use BeyondSEO\Domain\Base\Repo\RC\Utils\RCApiOperations;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\Content\Elements\ContentAnalysis\WPContentAnalysis;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\Content\Elements\MetaTags\Tags\WPWebPageDescriptionMetaTag;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Services\WPKeywordsService;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Services\WPWebPageService;
use BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\ContentAnalysisPostRequestDto;
use BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\ContentAnalysisResponseDto;
use BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\KeywordsMetaTagsKeywordRequestDto;
use BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\KeywordsResponseDto;
use BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\MetaTagsGetRequestDto;
use BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\MetaTagsGetResponseDto;
use BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\MetaTagsPostRequestDto;
use BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\MetaTagsSeparatorRequestDto;
use BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\MetaTagsSeparatorResponseDto;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\ForbiddenException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use BeyondSEODeps\DDD\Presentation\Base\Controller\HttpController;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Summary;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Tag;
use BeyondSEODeps\DDD\Presentation\Base\Router\Routes\Delete;
use BeyondSEODeps\DDD\Presentation\Base\Router\Routes\Get;
use BeyondSEODeps\DDD\Presentation\Base\Router\Routes\Post;
use BeyondSEODeps\DDD\Presentation\Base\Router\Routes\Put;
use BeyondSEODeps\DDD\Presentation\Base\Router\Routes\Route;
use BeyondSEODeps\Doctrine\DBAL\Exception;
use BeyondSEODeps\Doctrine\ORM\Exception\ORMException;
use BeyondSEODeps\Doctrine\ORM\NonUniqueResultException;
use BeyondSEODeps\Doctrine\ORM\OptimisticLockException;
use BeyondSEODeps\Doctrine\Persistence\Mapping\MappingException;
use JsonException;
use Psr\Cache\InvalidArgumentException;
use RankingCoach\Inc\Core\Base\Traits\RcLoggerTrait;
use ReflectionException;
use Throwable;

/**
 * Class WPMetaTagsController
 */
#[Route('metatags/{postId}')]
#[Tag(name: 'MetaTags', group: 'Modules', description: 'Operations for MetaTags')]
class WPMetaTagsController extends HttpController
{
    use RcLoggerTrait;

    /**
     * Get MetaTags
     *
     * @param MetaTagsGetRequestDto $requestDto
     * @param WPWebPageService $webPageService
     * @return MetaTagsGetResponseDto
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     * @throws \BeyondSEODeps\Doctrine\ORM\Mapping\MappingException
     */
	#[Get]
	#[Summary('Get all MetaTags')]
	public function getAllMetaTags(
        MetaTagsGetRequestDto $requestDto,
        WPWebPageService $webPageService
    ): MetaTagsGetResponseDto
	{
        // Validate request data
        if (!$requestDto->postId) {
            throw new BadRequestException(__('Post ID is required', 'beyondseo')); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
        }

        /** @var array $metaTags */
        $metaTags = $webPageService->getMetaTags($requestDto);

        // Retrieve the meta tags and return them in the response DTO
        return new MetaTagsGetResponseDto(
            title: $metaTags['title'],
            description: $metaTags['description'],
            keywords: $metaTags['keywords']
        );
	}

    /**
     * Save MetaTag Keywords
     *
     * @param MetaTagsPostRequestDto $requestDto
     * @param WPWebPageService $webPageService
     * @return MetaTagsGetResponseDto
     * @throws Throwable
     */
    #[Post]
    #[Summary('Update or create all MetaTags')]
    public function updateAllMetaTags (
        MetaTagsPostRequestDto $requestDto,
        WPWebPageService $webPageService
    ): MetaTagsGetResponseDto {

        // Validate request data
        if (!$requestDto->postId) {
            throw new BadRequestException(__('Post ID is required', 'beyondseo')); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
        }

        // Save the meta tags using the service and return the updated meta tags
        $metaTags = $webPageService->saveMetaTags($requestDto);

        // Return the response with the updated meta tags
        return new MetaTagsGetResponseDto(
            title: $metaTags['title'],
            description: $metaTags['description'],
            keywords: $metaTags['keywords']
        );
    }

    /**
     * Save MetaTag Keywords
     *
     * @param KeywordsMetaTagsKeywordRequestDto $requestDto
     * @param WPWebPageService $webPageService
     * @return MetaTagsGetResponseDto
     * @throws BadRequestException
     * @throws Exception
     * @throws ForbiddenException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws MappingException
     * @throws ReflectionException
     * @throws \BeyondSEODeps\Doctrine\ORM\Mapping\MappingException
     */
    #[Post('/keyword/swap')]
    #[Summary('Update or create all MetaTags')]
    public function swapMetaTagsKeywords (
        KeywordsMetaTagsKeywordRequestDto $requestDto,
        WPWebPageService $webPageService
    ): MetaTagsGetResponseDto {

        // Swap the keyword using the web page service
        $metaTags = $webPageService->swapMetaTagsKeyword($requestDto);

        // Return the response with the updated meta tags
        return new MetaTagsGetResponseDto(
            title: $metaTags['title'],
            description: $metaTags['description'],
            keywords: $metaTags['keywords']
        );
    }

    /**
     * Save MetaTags Keyword
     *
     * @param KeywordsMetaTagsKeywordRequestDto $requestDto
     * @param WPWebPageService $webPageService
     * @return MetaTagsGetResponseDto
     * @throws Throwable
     */
    #[Put('/keyword')]
    #[Summary('Save MetaTags Keyword')]
    public function addMetaTagsKeyword (
        KeywordsMetaTagsKeywordRequestDto $requestDto,
        WPWebPageService $webPageService
    ): MetaTagsGetResponseDto
    {
        // Save the keyword using the web page service
        $metaTags = $webPageService->saveMetaTagsKeyword($requestDto);

        // Get the meta tags after saving the keyword
        return new MetaTagsGetResponseDto(
            title: $metaTags['title'],
            description: $metaTags['description'],
            keywords: $metaTags['keywords']
        );
    }

    /**
     * Save MetaTags Keyword
     *
     * @param KeywordsMetaTagsKeywordRequestDto $requestDto
     * @param WPWebPageService $webPageService
     * @return MetaTagsGetResponseDto
     * @throws BadRequestException
     * @throws Exception
     * @throws ForbiddenException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws MappingException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ReflectionException
     * @throws \BeyondSEODeps\Doctrine\ORM\Mapping\MappingException
     */
    #[Delete('/keyword')]
    #[Summary('Remove MetaTags Keyword')]
    public function removeMetaTagsKeyword (
        KeywordsMetaTagsKeywordRequestDto $requestDto,
        WPWebPageService $webPageService
    ): MetaTagsGetResponseDto
    {

        // Remove the keyword using the web page service
        $metaTags = $webPageService->removeMetaTagsKeyword($requestDto);

        // Retrieve the meta tags after removing the keyword
        return new MetaTagsGetResponseDto(
            title: $metaTags['title'],
            description: $metaTags['description'],
            keywords: $metaTags['keywords']
        );
    }

    /**
     * Retrieve Location Keywords
     *
     * @param MetaTagsGetRequestDto $requestDto
     * @param WPKeywordsService $keywordService
     * @return KeywordsResponseDto
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     * @throws \BeyondSEODeps\Doctrine\ORM\Mapping\MappingException
     */
    #[Get('/keywords')]
    #[Summary('Retrieve Location Keywords')]
    public function getKeywords(
        MetaTagsGetRequestDto $requestDto,
        WPKeywordsService $keywordService
    ): KeywordsResponseDto
    {
        $keywords = $keywordService->getAllKeywords();
        $response = new KeywordsResponseDto();
        $response->keywords = $keywords;

        return $response;
    }

    /**
     * Retrieve Keywords After Analyzing The Content
     *
     * @param ContentAnalysisPostRequestDto $requestDto
     * @return ContentAnalysisResponseDto
     */
    #[Post('/content/keywords')]
    #[Tag(name: 'Content Analysis', description: 'Proceed The Content Analysis')]
    #[Summary('Retrieve Content Keywords')]
    public function getContentKeywords(
        ContentAnalysisPostRequestDto $requestDto
    ): ContentAnalysisResponseDto
    {
        RCLoad::$logRCCalls = true;

        $response = new ContentAnalysisResponseDto();
        $response->postId = $requestDto->postId;

        $analysis = new WPContentAnalysis($requestDto->postId);
        $analysis->post;
        $response->keywords = $analysis->keywordsAnalysis;

        $this->log_json([
            'operation_type' => 'content_analysis',
            'operation_status' => 'success',
            'api_calls' => RCApiOperations::getExecutedRCCalls(),
            'context_entity' => 'post',
            'context_id' => $requestDto->postId,
            'content_type' => get_post_type($requestDto->postId) ?: 'unknown',
            'execution_time' => null,
            'error_details' => null,
            'metadata' => [
                'analysis_type' => 'keywords_detection',
                'keywords_found' => $response->keywords ?? null
            ]
        ], 'api');
        RCLoad::$logRCCalls = false;

        return $response;
    }

    /**
     * Get Post Separator
     *
     * @param MetaTagsSeparatorRequestDto $requestDto
     * @return MetaTagsSeparatorResponseDto
     */
    #[Get('/separator')]
    #[Summary('Get Post Separator')]
    public function getPostSeparators(
        MetaTagsSeparatorRequestDto $requestDto
    ): MetaTagsSeparatorResponseDto
    {
        // Retrieve and return the raw UTF-8 character as well (useful for UI preview)
        $stored = get_post_meta($requestDto->postId, 'rankingcoach_title_separator', true);

        $response = new MetaTagsSeparatorResponseDto();
        $response->separator = $stored ?: '-'; // fallback to dash if somehow empty

        return $response;
    }


    /**
     * Update Post Separator
     *
     * @param MetaTagsSeparatorRequestDto $requestDto
     * @return MetaTagsSeparatorResponseDto
     */
    #[Put('/separator')]
    #[Summary('Update Post Separator')]
    public function updatePostSeparator(
        MetaTagsSeparatorRequestDto $requestDto,
    ): MetaTagsSeparatorResponseDto
    {
        // Normalize separator: decode any entity (e.g. &#58;) into its UTF-8 character
        $separator = html_entity_decode($requestDto->separator, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Store the raw UTF-8 character in post meta
        update_post_meta($requestDto->postId, 'rankingcoach_title_separator', $separator);

        // Retrieve and return the raw UTF-8 character as well (useful for UI preview)
        $stored = get_post_meta($requestDto->postId, 'rankingcoach_title_separator', true);

        $response = new MetaTagsSeparatorResponseDto();
        $response->separator = $stored ?: '-'; // fallback to dash if somehow empty

        return $response;
    }
}