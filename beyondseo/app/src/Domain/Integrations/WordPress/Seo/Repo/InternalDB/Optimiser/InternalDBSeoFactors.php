<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Repo\InternalDB\Optimiser;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Repo\InternalDB\InternalDBEntitySet;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Factors;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\OptimiserContext;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoad;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use BeyondSEODeps\Doctrine\ORM\Mapping\MappingException;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;

/**
 * Class InternalDBSeoFactors
 *
 * This class is responsible for managing a collection of SEO factors.
 * @method Factors find( ?DoctrineQueryBuilder $queryBuilder = null, $useEntityRegistryCache = false)
 */
class InternalDBSeoFactors extends InternalDBEntitySet
{
    public const BASE_REPO_CLASS = InternalDBSeoFactor::class;
    public const BASE_ENTITY_SET_CLASS = Factors::class;

    /**
     * @throws MappingException
     * @throws BadRequestException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     * @throws InternalErrorException
     */
    public function lazyload(
        OptimiserContext $optimiserContext,
        LazyLoad &$lazyloadPropertyInstance
    ): ?Factors {
        return $this->getByContextId($optimiserContext->id, $lazyloadPropertyInstance->useCache);
    }

    /**
     * Get a factor by its unique key
     * @param int $contextId
     * @param bool $useCache
     * @return Factors|null The factor if found, null otherwise
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws MappingException
     * @throws ReflectionException
     */
    public function getByContextId(int $contextId, bool $useCache = false): ?Factors
    {
        $queryBuilder = self::createQueryBuilder();
        $queryBuilder->where('seo_factor.contextId = :contextId')
            ->setParameter('contextId', $contextId);

        return $this->find($queryBuilder, $useCache);
    }
}