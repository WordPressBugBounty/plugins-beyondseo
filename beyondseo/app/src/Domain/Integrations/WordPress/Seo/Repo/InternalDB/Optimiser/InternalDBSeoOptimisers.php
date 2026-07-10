<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Repo\InternalDB\Optimiser;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Repo\InternalDB\InternalDBEntitySet;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\SeoOptimisers;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;

/**
 * Class InternalDBSeoOptimisers
 *
 * This class is responsible for managing a collection of SEO optimisers.
 * @method SeoOptimisers find( ?DoctrineQueryBuilder $queryBuilder = null, $useEntityRegistryCache = false)
 */
class InternalDBSeoOptimisers extends InternalDBEntitySet
{
    public const BASE_REPO_CLASS = InternalDBSeoOptimiser::class;
    public const BASE_ENTITY_SET_CLASS = SeoOptimisers::class;
}