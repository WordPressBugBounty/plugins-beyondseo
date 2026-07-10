<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Seo\Repo\InternalDB\Optimiser\InternalDBSeoOptimisers;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Services\WPSeoOptimiserService;
use BeyondSEODeps\DDD\Domain\Base\Entities\EntitySet;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoadRepo;

/**
 * Class SeoOptimisers
 *
 * This class is responsible for managing a collection of SEO optimizers.
 * @method WPSeoOptimiserService getService()
 * @method SeoOptimiser[] getElements()
 * @method SeoOptimiser|null first()
 * @method SeoOptimiser|null getByUniqueKey(string $uniqueKey)
 * @property SeoOptimiser[] $elements
 */
#[LazyLoadRepo(repoType: LazyLoadRepo::INTERNAL_DB, repoClass: InternalDBSeoOptimisers::class)]
class SeoOptimisers extends EntitySet
{
    public const SERVICE_NAME = WPSeoOptimiserService::class;
}