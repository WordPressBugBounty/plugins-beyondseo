<?php

namespace BeyondSEO\Domain\Integrations\WordPress\Common\Entities\Categories;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Repo\InternalDB\Categories\InternalDBWPCategories;
use BeyondSEO\Domain\Common\Services\WPCategoriesService;
use BeyondSEODeps\DDD\Domain\Base\Entities\EntitySet;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoadRepo;

/**
 * Class WPCategories
 * @method WPCategory[] getElements()
 * @method WPCategory|null first()
 * @method WPCategory|null getByUniqueKey(string $uniqueKey)
 * @property WPCategory[] $elements
 */


#[LazyLoadRepo(repoType: LazyLoadRepo::INTERNAL_DB, repoClass: InternalDBWPCategories::class)]
class WPCategories extends EntitySet
{
    public const ENTITY_CLASS = WPCategory::class;
    public const SERVICE_NAME = WPCategoriesService::class;
}