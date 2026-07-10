<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Repo\InternalDB\Optimiser;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Repo\InternalDB\InternalDBEntity;
use BeyondSEO\Domain\Common\Repo\InternalDB\Models\InternalDBSeoContextsModel;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Factors;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\OptimiserContext;
use BeyondSEODeps\DDD\Domain\Base\Entities\DefaultObject;
use BeyondSEODeps\DDD\Domain\Base\Entities\Entity;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoad;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Attributes\EntityCache;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Infrastructure\Cache\Cache;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;
use Throwable;

/**
 * @method OptimiserContext find(DoctrineQueryBuilder|string|int $idOrQueryBuilder, bool $useEntityRegistryCache = false, ?InternalDBSeoContextsModel &$loadedOrmInstance = null, bool $deferredCaching = false)
 * @property InternalDBSeoContextsModel $ormInstance
 */
#[EntityCache(useExtendedRegistryCache: false, ttl: 300, cacheGroup: Cache::CACHE_GROUP_PHPFILES, cacheScopes: [])]
class InternalDBSeoContext extends InternalDBEntity
{
    public const BASE_ENTITY_CLASS = OptimiserContext::class;
    public const BASE_ORM_MODEL = InternalDBSeoContextsModel::class;

    /**
     * @param DefaultObject $initiatingEntity
     * @param LazyLoad $lazyloadAttributeInstance
     *
     * @return OptimiserContext
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function lazyload(
        DefaultObject &$initiatingEntity,
        LazyLoad &$lazyloadAttributeInstance
    ): OptimiserContext {

        parent::lazyload($initiatingEntity, $lazyloadAttributeInstance);
        return $this->mapToEntity($lazyloadAttributeInstance->useCache);
    }

    /**
     * @param bool $useEntityRegistryCache
     * @return OptimiserContext
     * @throws ReflectionException
     */
    public function mapToEntity(bool $useEntityRegistryCache = true): OptimiserContext
    {
        /** @var OptimiserContext $context */
        $context = parent::mapToEntity($useEntityRegistryCache);
        $ormInstance = $this->ormInstance;

        $context->id = $ormInstance->id;
        $context->analysisId = $ormInstance->analysisId;
        $context->contextName = $ormInstance->contextName;
        $context->contextKey = $ormInstance->contextKey;
        $context->weight = (float) $ormInstance->weight;
        $context->score = (float) $ormInstance->score;

        return $context;
    }

    /**
     * @param Entity $entity
     * @return bool
     * @throws ReflectionException
     */
    protected function mapToRepository(Entity &$entity): bool
    {
        /** @var OptimiserContext $entity */
        parent::mapToRepository($entity);
        if(isset($this->ormInstance->id)) {
            $this->ormInstance->id = (int)$entity->id;
        }
        $this->ormInstance->analysisId = (int)$entity->analysisId;
        $this->ormInstance->contextKey = $entity->contextKey;
        $this->ormInstance->contextName = $entity->contextName;
        $this->ormInstance->weight = (string)$entity->weight;
        $this->ormInstance->score = (string)$entity->score;

        return true;
    }

    /**
     * Save the entity to the database.
     *
     * @param OptimiserContext $optimiserContext
     * @return Entity|null True on success, false on failure.
     * @throws Throwable
     */
    public function save(OptimiserContext $optimiserContext): ?Entity
    {
        $factors = $optimiserContext->factors;
        /** @var Factors $factors */
        foreach ($factors->elements as $factor) {
            $factor->contextId = $optimiserContext->id;
            /** @var InternalDBSeoFactor $repo */
            $repo = $factor->getService()->getFactorRepository();
            $repo->update($factor);
            $repo->save($factor);
        }

        return $optimiserContext;
    }
}