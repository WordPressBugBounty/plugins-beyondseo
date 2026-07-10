<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Websites;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Plugin\Entities\WPPlugin;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Repo\InternalDB\Websites\InternalDBWPWebsite;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Repo\InternalDB\Websites\InternalDBWPWebsiteDatabase;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Repo\InternalDB\Websites\InternalDBWPWebsiteSettings;
use BeyondSEO\Domain\Seo\Entities\Website;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoad;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoadRepo;

/**
 * @method WPPlugin getParent()
 * @property WPPlugin $parent;
 */
#[LazyLoadRepo(repoType:LazyLoadRepo::INTERNAL_DB, repoClass:InternalDBWPWebsite::class)]
class WPWebsite extends Website
{

    #============================================
    # region Properties
    #============================================
    /**
     * @var WPWebsiteSetting|null $options The site options
     */
    #[LazyLoad(repoType: LazyLoadRepo::INTERNAL_DB, repoClass: InternalDBWPWebsiteSettings::class)]
    public ?WPWebsiteSetting $settings = null;

    /**
     * @var WPWebsiteDatabase|null $database The site database
     */
    #[LazyLoad(repoType: LazyLoadRepo::INTERNAL_DB, repoClass: InternalDBWPWebsiteDatabase::class)]
    public ?WPWebsiteDatabase $database = null;

    # endregion
    #============================================
}