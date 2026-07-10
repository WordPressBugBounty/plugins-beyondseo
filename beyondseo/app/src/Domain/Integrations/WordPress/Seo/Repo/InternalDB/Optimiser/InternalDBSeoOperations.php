<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Repo\InternalDB\Optimiser;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Repo\InternalDB\InternalDBEntitySet;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Factor;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Operations;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoad;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use BeyondSEODeps\Doctrine\ORM\Mapping\MappingException;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;

/**
 * Class InternalDBSeoOperations
 *
 * This class is responsible for managing a collection of SEO operations.
 * @method Operations find( ?DoctrineQueryBuilder $queryBuilder = null, $useEntityRegistryCache = false)
 */
class InternalDBSeoOperations extends InternalDBEntitySet
{
    public const BASE_REPO_CLASS = InternalDBSeoOperation::class;
    public const BASE_ENTITY_SET_CLASS = Operations::class;

    /**
     * @throws MappingException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     * @throws ReflectionException
     * @throws InternalErrorException
     */
    public function lazyload(
        Factor   $factor,
        LazyLoad $lazyloadPropertyInstance
    ): ?Operations {
        return $this->getByFactorId($factor->id, $lazyloadPropertyInstance->useCache);
    }

    /**
     * Get an operation by its unique key
     * @param int $factorId
     * @param bool $useCache
     * @return Operations|null The operation if found, null otherwise
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws MappingException
     * @throws ReflectionException
     */
    public function getByFactorId(int $factorId, bool $useCache = true): ?Operations
    {
        $queryBuilder = self::createQueryBuilder();
        $queryBuilder->where('seo_operation.factorId = :factorId')
            ->setParameter('factorId', $factorId);

        return $this->find($queryBuilder, $useCache);
    }
}