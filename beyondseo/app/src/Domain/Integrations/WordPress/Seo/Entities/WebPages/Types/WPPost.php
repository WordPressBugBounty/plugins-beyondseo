<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\Types;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\WPWebPage;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Services\WPWebPageService;
use BeyondSEODeps\DDD\Infrastructure\Validation\Constraints\Choice;

/**
 * Represents a WPPost page entity.
 *
 * @method WPWebPageService getService()
 * @method WPPosts getParent()
 */
class WPPost extends WPWebPage
{
    /** @var string Post type */
    #[Choice([], [self::CONTENT_TYPE_PAGE, self::CONTENT_TYPE_POST, self::CONTENT_TYPE_ATTACHMENT, self::CONTENT_TYPE_REVISION, self::CONTENT_TYPE_NAVIGATION_MENU_ITEM, self::CONTENT_TYPE_WP_BLOCK, self::CONTENT_TYPE_CUSTOM])]
    public string $postType = self::CONTENT_TYPE_POST;
}