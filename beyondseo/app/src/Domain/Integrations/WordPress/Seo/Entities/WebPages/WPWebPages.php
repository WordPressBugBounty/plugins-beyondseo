<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Seo\Repo\InternalDB\WebPages\InternalDBWPWebPages;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Services\WPWebPageService;
use BeyondSEO\Domain\Seo\Entities\WebPages\WebPages;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoadRepo;

/**
 * Represents a set of WordPress pages.
 *
 * @method WPWebPage[] getElements()
 * @method WPWebPage|null first()
 * @method WPWebPage|null getByUniqueKey(string $uniqueKey)
 * @property WPWebPage[] $elements
 */
#[LazyLoadRepo(LazyLoadRepo::INTERNAL_DB, repoClass: InternalDBWPWebPages::class)]
class WPWebPages extends WebPages
{
    public const SERVICE_NAME = WPWebPageService::class;
}