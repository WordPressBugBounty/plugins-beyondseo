<?php
declare( strict_types=1 );

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\Content\Elements\ContentAnalysis\Keywords\WPKeywords;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;


/**
 * Class KeywordsMetaTagsResponseDto
 */
class KeywordsResponseDto extends RestResponseDto {

	/** @var WPKeywords|null $keywords The location keywords */
	public ?WPKeywords $keywords = null;
}