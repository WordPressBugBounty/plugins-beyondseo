<?php

declare(strict_types=1);


namespace BeyondSEODeps\DDD\Domain\Base\Repo\Virtual;

use BeyondSEODeps\DDD\Domain\Base\Entities\DefaultObject;
use BeyondSEODeps\DDD\Domain\Base\Entities\Entity;
use BeyondSEODeps\DDD\Domain\Base\Entities\EntitySet;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoad;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Attributes\EntityCache;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Infrastructure\Cache\Cache;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use BeyondSEODeps\DDD\Infrastructure\Reflection\ReflectionClass;
use BeyondSEODeps\DDD\Infrastructure\Traits\SingletonTrait;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;

/**
 * A global Registry for Entities loaded through Virtual repos which serves as a static Cache for them in order to assure that we are working with the identical instances
 * whenever uniqueKey is equal
 * @method static VirtualEntityRegistry getInstance
 */
class VirtualEntityRegistry
{
    use SingletonTrait;

    /** @var bool if true, cache will be cleared on execution */
    public static bool $clearCache = false;

    /** @var string the default prefix used for cache keys */
    static string $defaultCachePrefix = 'VirtualEntityRegistry_';

    /** @var  Entity|EntitySet|null */
    protected static array $entityRegistry = [];

    /** @var string[] Cache Groups that contain deferred cache elements */
    protected static array $deferredCacheGroups = [];

    /**
     * Adds entity to registry by caller uniquekey, repoclass and method name
     * If extended registry cache is defined to be used, stores the entity as well in extended entity registry cache
     * @param DefaultObject|null $entity
     * @param string $repoClass
     * @param string|int|DoctrineQueryBuilder $idOrQuery
     * @param bool $deferredCaching
     * @return void
     * @throws InvalidArgumentException
     */
    public function add(
        ?DefaultObject &$caller,
        ?DefaultObject &$result,
        string $repoClass,
        string $method
    ) {
        $entityCacheAttribute = null;
        $cacheIndex = $caller->uniqueKey() . '_' . $repoClass . '_' . $method;

        /** @var EntityCache $entityCacheAttribute */
        $reflectionClass = ReflectionClass::instance($repoClass);
        $entityCacheAttribute = $reflectionClass->getAttributeInstance(EntityCache::class);

        if ($entityCacheAttribute && $entityCacheAttribute->useExtendedRegistryCache && $entityCacheAttribute->ttl) {
            $cache = Cache::instance($entityCacheAttribute->cacheGroup);
            //$cacheValue = $result?->toJSON(true);
            $cache->set(
                self::getCacheKey($cacheIndex),
                $result,
                $entityCacheAttribute->ttl
            );
        }
        // by cloning we want to avoid issues when reference is returned and the reference is manipulated somewhere else
        self::$entityRegistry[$cacheIndex] = $result;
    }

    /**
     * Get an Entity from cache by caller uniquekey, repoclass and method name
     * If extended registry cache is defined to be used, tries to find it in the cache.
     * If no result is found, returns false
     * @param string $repoClass
     * @param string|int|DoctrineQueryBuilder $idOrQuery
     * @return Entity|EntitySet|false|null
     * @throws InternalErrorException
     * @throws BadRequestException
     * @throws ReflectionException
     */
    public function get(
        ?DefaultObject &$initiatingEntity,
        string $repoClass,
        string $lazyloadMethod,
        LazyLoad &$lazyloadAttributeInstance
    ): DefaultObject|null|false {
        if (self::$clearCache) {
            return null;
        }
        $cacheIndex = $initiatingEntity->uniqueKey() . '_' . $repoClass . '_' . $lazyloadMethod;

        $return = isset(self::$entityRegistry[$cacheIndex]) ? self::$entityRegistry[$cacheIndex] : false;
        if ($return) {
            return $return;
        }

        if ($return === false) {
            /** @var EntityCache $entityCacheAttribute */
            $reflectionClass = ReflectionClass::instance($repoClass);
            $entityCacheAttribute = $reflectionClass->getAttributeInstance(EntityCache::class);
            if ($lazyloadAttributeInstance->useCache && $entityCacheAttribute && $entityCacheAttribute->useExtendedRegistryCache && $entityCacheAttribute->ttl) {
                $cache = Cache::instance($entityCacheAttribute->cacheGroup);
                $return = $cache->get(self::getCacheKey($cacheIndex));
                if ($return) {
                    // we store value retrieved from cache also in static registry
                    self::$entityRegistry[$cacheIndex] = $return;
                }
            }
        }
        return $return;
    }

    /**
     * Returns cache key combined with prefix and BEYONDSEO_APP_ROOT_DIR in order to avoid workspaces influencing each other
     * @param string $cacheKey
     * @return string
     */
    public static function getCacheKey(string $cacheKey): string
    {
        return self::$defaultCachePrefix . '_' . md5(BEYONDSEO_APP_ROOT_DIR . '_' . $cacheKey);
    }
}