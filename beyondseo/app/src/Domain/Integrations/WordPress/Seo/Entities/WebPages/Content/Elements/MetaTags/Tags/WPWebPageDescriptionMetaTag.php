<?php
declare( strict_types=1 );

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\Content\Elements\MetaTags\Tags;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\Content\Elements\MetaTags\WPWebPageMetaTag;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\Content\Elements\MetaTags\WPWebPageMetaTags;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Repo\InternalDB\WebPages\InternalDBWPWebPageMetaTagDescription;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoadRepo;
use RankingCoach\Inc\Core\Base\Traits\RcLoggerTrait;

/**
 * Class WPWebPageDescriptionMetaTag
 * @property WPWebPageMetaTags $parent
 * @method WPWebPageMetaTags getParent()
 */
#[LazyLoadRepo(LazyLoadRepo::INTERNAL_DB, InternalDBWPWebPageMetaTagDescription::class)]
class WPWebPageDescriptionMetaTag extends WPWebPageMetaTag {

    use RcLoggerTrait;

    /** @var string The type of the meta tag */
    public string $type = WPWebPageMetaTag::TAG_TYPE_DESCRIPTION;

    /** @var string|null The parsed content */
    public ?string $parsed = null;

    /**
     * WPWebPageDescriptionMetaTag constructor.
     *
     * @param int $postId
     */
    public function __construct(int $postId = 0) {
        $this->type = WPWebPageMetaTag::TAG_TYPE_DESCRIPTION;
        parent::__construct($postId, $this->type);
    }
}