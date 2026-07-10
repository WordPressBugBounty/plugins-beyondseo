<?php
declare( strict_types=1 );

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\Content\Elements\MetaTags\Tags\WPWebPageKeywordsMetaTag;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;


/**
 * Class KeywordsMetaTagsResponseDto
 */
class KeywordsMetaTagsResponseDto extends RestResponseDto {

	/** @var WPWebPageKeywordsMetaTag|null $keywords The keywords */
	public ?WPWebPageKeywordsMetaTag $keywords = null;
}