<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Setup\Repo\InternalDB\Flows\Collectors;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Repo\InternalDB\InternalDBEntitySet;
use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\Collectors\WPFlowCollectors;
use BeyondSEODeps\DDD\Domain\Base\Entities\DefaultObject;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoad;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use BeyondSEODeps\Doctrine\ORM\Mapping\MappingException;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;

/**
 * Class InternalDBWPFlowCollectors
 * @method WPFlowCollectors find( ?DoctrineQueryBuilder $queryBuilder = null, $useEntityRegistryCache = true)
 */
class InternalDBWPFlowCollectors extends InternalDBEntitySet
{
    public const BASE_REPO_CLASS = InternalDBWPFlowCollector::class;
    public const BASE_ENTITY_SET_CLASS = WPFlowCollectors::class;

    /**
     * @param DefaultObject $initiatingEntity
     * @param LazyLoad $lazyloadPropertyInstance
     * @return WPFlowCollectors|null
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws MappingException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function lazyload(
        DefaultObject &$initiatingEntity,
        LazyLoad &$lazyloadPropertyInstance
    ): ?WPFlowCollectors {
        return $this->getAllCollectors($lazyloadPropertyInstance->useCache);
    }

    /**
     * @param bool $useCache
     * @return WPFlowCollectors
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws MappingException
     * @throws ReflectionException
     */
    public function getAllCollectors(bool $useCache = true): WPFlowCollectors
    {
        $queryBuilder = static::createQueryBuilder();
        $queryBuilder
            ->select('flow_collector')
            ->orderBy(
                'flow_collector.priority',
                'ASC'
            );
        return $this->find($queryBuilder, $useCache);
    }
}