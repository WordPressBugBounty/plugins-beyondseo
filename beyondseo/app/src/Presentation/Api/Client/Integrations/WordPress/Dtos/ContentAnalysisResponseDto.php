<?php
declare( strict_types=1 );

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\Content\Elements\ContentAnalysis\WPKeywordsAnalysis;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;

/**
 * Class ContentAnalysisResponseDto
 *
 */
class ContentAnalysisResponseDto extends RestResponseDto {

    /** @var int|null $postId The post-ID */
	public ?int $postId = null;

    /** @var int $seoScore The calculated SEO score */
	public int $seoScore = 0;

	/** @var WPKeywordsAnalysis|null $keywords The featured keywords extracted from content and title */
	public ?WPKeywordsAnalysis $keywords = null;
}