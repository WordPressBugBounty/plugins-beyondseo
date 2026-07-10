<?php

namespace BeyondSEO\Domain\Common\Repo\InternalDB\Categories;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Repo\InternalDB\InternalDBEntity;
use BeyondSEO\Domain\Common\Repo\InternalDB\Models\InternalDBWPCategoriesModel;
use BeyondSEO\Domain\Integrations\WordPress\Common\Entities\Categories\WPCategory;
use BeyondSEODeps\DDD\Domain\Base\Entities\DefaultObject;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoad;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineModel;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;

/**
 * Repository for Categories
 * Class InternalDBCategory
 *
 * @method WPCategory find(DoctrineQueryBuilder|string|int $idOrQueryBuilder, bool $useEntityRegistryCache = false, ?DoctrineModel &$loadedOrmInstance = null, bool $deferredCaching = true, array $initiatorClasses = [])
 * @property InternalDBWPCategoriesModel $ormInstance
 */

class InternalDBWPCategory extends InternalDBEntity {
    public const BASE_ENTITY_CLASS = WPCategory::class;
    public const BASE_ORM_MODEL = InternalDBWPCategoriesModel::class;

    /**
     * Lazy loads a question entity
     *
     * @param DefaultObject $initiatingEntity The entity requesting the lazy load
     * @param LazyLoad $lazyloadAttributeInstance The LazyLoad attribute
     * @return WPCategory The loaded question entity
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function lazyload(
        DefaultObject &$initiatingEntity,
        LazyLoad &$lazyloadAttributeInstance
    ): WPCategory {
        parent::lazyload($initiatingEntity, $lazyloadAttributeInstance);
        return $this->mapToEntity($lazyloadAttributeInstance->useCache);
    }

    /**
     * Maps the ORM instance to an entity
     *
     * @param bool $useEntityRegistryCache Whether to use the registry cache
     * @return WPCategory The mapped entity
     * @throws ReflectionException
     */
    public function mapToEntity(bool $useEntityRegistryCache = true): WPCategory
    {
        /** @var WPCategory $category */
        $category = parent::mapToEntity($useEntityRegistryCache);
        $category->id = $this->ormInstance->id;
        $category->categoryId = $this->ormInstance->categoryId;
        $category->name = $this->ormInstance->name;
        $category->externalId = $this->ormInstance->externalId;
        return $category;
    }
}