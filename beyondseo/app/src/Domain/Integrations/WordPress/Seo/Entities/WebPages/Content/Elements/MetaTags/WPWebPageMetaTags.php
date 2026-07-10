<?php
declare( strict_types=1 );

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\Content\Elements\MetaTags;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Domain\Base\Entities\EntitySet;

/**
 * Class WPWebPageMetaTags
 *
 * @method WPWebPageMetaTag[] getElements()
 * @method WPWebPageMetaTag|null first()
 * @method WPWebPageMetaTag|null getByUniqueKey(string $uniqueKey)
 * @property WPWebPageMetaTag[] $elements
 */
class WPWebPageMetaTags extends EntitySet
{
	/**
	 * @param int $postId
	 *
	 * @return WPWebPageMetaTags
	 */
	public function getTitleAndDescriptionByPostId(int $postId): WPWebPageMetaTags
	{
		return $this;
	}
}