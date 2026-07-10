<?php

namespace BeyondSEO\Domain\Common\Repo\InternalDB\Categories;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Repo\InternalDB\InternalDBEntitySet;
use BeyondSEO\Domain\Integrations\WordPress\Common\Entities\Categories\WPCategories;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineModel;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use BeyondSEODeps\Doctrine\ORM\Mapping\MappingException;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;

/**
 * Repository for Categories
 * Class InternalDBWPCategories
 *
 * @method WPCategories find(DoctrineQueryBuilder|string|int $idOrQueryBuilder, bool $useEntityRegistryCache = false, ?DoctrineModel &$loadedOrmInstance = null, bool $deferredCaching = true, array $initiatorClasses = [])
 */

class InternalDBWPCategories extends InternalDBEntitySet {
    public const BASE_REPO_CLASS = InternalDBWPCategory::class;
    public const BASE_ENTITY_SET_CLASS = WPCategories::class;

    /**
     * @param string $searchString
     * @return WPCategories|null
     * @throws MappingException
     * @throws BadRequestException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     * @throws InternalErrorException
     */
    public function getCategories(string $searchString): ?WPCategories
    {
        $queryBuilder = static::createQueryBuilder();
        $queryBuilder->where('rankingcoach_setup_categories.name LIKE :search_string')
            ->setParameter('search_string', '%' . $searchString . '%');
        return $this->find($queryBuilder, true);
    }

    /**
     * @param ...$name
     * @return WPCategories|null
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws MappingException
     * @throws ReflectionException
     */

    public function getCategoriesByName(...$name): ?WPCategories
    {
        $queryBuilder = static::createQueryBuilder();
        $queryBuilder->where('rankingcoach_setup_categories.name IN (:name)')
            ->setParameter('name', $name);
        return $this->find($queryBuilder, true);
    }

    /**
     * @param ...$id
     * @return WPCategories|null
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws MappingException
     * @throws ReflectionException
     */

    public function getCategoriesByCategoryId(...$id): ?WPCategories
    {
        $queryBuilder = static::createQueryBuilder();
        $queryBuilder->where('rankingcoach_setup_categories.categoryId IN (:id)')
            ->setParameter('id', $id);
        return $this->find($queryBuilder, true);
    }
}