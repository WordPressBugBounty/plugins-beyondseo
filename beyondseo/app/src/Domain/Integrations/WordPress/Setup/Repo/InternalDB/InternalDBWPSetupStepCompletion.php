<?php

declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Setup\Repo\InternalDB;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Repo\InternalDB\InternalDBEntity;
use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\SetupSteps\SetupStepCompletions\WPSetupStepCompletion;
use BeyondSEODeps\DDD\Domain\Base\Entities\DefaultObject;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoad;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineModel;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;

#use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Attributes\EntityCache;
#use BeyondSEODeps\DDD\Infrastructure\Cache\Cache;

/**
 * Class InternalDBWPSetupStepCompletion
 * @method WPSetupStepCompletion find(DoctrineQueryBuilder|string|int $idOrQueryBuilder, bool $useEntityRegistryCache = false, ?DoctrineModel &$loadedOrmInstance = null, bool $deferredCaching = true, array $initiatorClasses = [])
 */
//#[EntityCache(useExtendedRegistryCache: false, ttl: 300, cacheGroup: Cache::CACHE_GROUP_PHPFILES, cacheScopes: [])]
class InternalDBWPSetupStepCompletion extends InternalDBEntity
{
    public const BASE_ENTITY_CLASS = WPSetupStepCompletion::class;

    /**
     * @param DefaultObject $initiatingEntity
     * @param LazyLoad $lazyloadAttributeInstance
     *
     * @return WPSetupStepCompletion
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function lazyload(
        DefaultObject &$initiatingEntity,
        LazyLoad &$lazyloadAttributeInstance
    ): WPSetupStepCompletion {

        parent::lazyload($initiatingEntity, $lazyloadAttributeInstance);
        return $this->mapToEntity($lazyloadAttributeInstance->useCache);
    }

    /**
     * @param bool $useEntityRegistryCache
     * @return WPSetupStepCompletion
     * @throws ReflectionException
     */
    public function mapToEntity(bool $useEntityRegistryCache = true): WPSetupStepCompletion
    {
        /** @var WPSetupStepCompletion $setupStepCompletion */
        $setupStepCompletion = parent::mapToEntity($useEntityRegistryCache);

        return $setupStepCompletion;
    }
}