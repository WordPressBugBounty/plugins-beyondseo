<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Cache\Persister\Entity;

use BeyondSEODeps\Doctrine\ORM\Cache\EntityCacheKey;
use BeyondSEODeps\Doctrine\ORM\Cache\EntityHydrator;
use BeyondSEODeps\Doctrine\ORM\Cache\Persister\CachedPersister;
use BeyondSEODeps\Doctrine\ORM\Persisters\Entity\EntityPersister;

/**
 * Interface for second level cache entity persisters.
 */
interface CachedEntityPersister extends CachedPersister, EntityPersister
{
    /** @return EntityHydrator */
    public function getEntityHydrator();

    /**
     * @param object $entity
     *
     * @return bool
     */
    public function storeEntityCache($entity, EntityCacheKey $key);
}
