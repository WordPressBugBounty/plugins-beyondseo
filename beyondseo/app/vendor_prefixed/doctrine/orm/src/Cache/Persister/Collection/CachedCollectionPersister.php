<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Cache\Persister\Collection;

use BeyondSEODeps\Doctrine\Common\Collections\Collection;
use BeyondSEODeps\Doctrine\ORM\Cache\CollectionCacheKey;
use BeyondSEODeps\Doctrine\ORM\Cache\Persister\CachedPersister;
use BeyondSEODeps\Doctrine\ORM\Mapping\ClassMetadata;
use BeyondSEODeps\Doctrine\ORM\PersistentCollection;
use BeyondSEODeps\Doctrine\ORM\Persisters\Collection\CollectionPersister;

/**
 * Interface for second level cache collection persisters.
 */
interface CachedCollectionPersister extends CachedPersister, CollectionPersister
{
    /** @return ClassMetadata */
    public function getSourceEntityMetadata();

    /** @return ClassMetadata */
    public function getTargetEntityMetadata();

    /**
     * Loads a collection from cache
     *
     * @return mixed[]|null
     */
    public function loadCollectionCache(PersistentCollection $collection, CollectionCacheKey $key);

    /**
     * Stores a collection into cache
     *
     * @param mixed[]|Collection $elements
     *
     * @return void
     */
    public function storeCollectionCache(CollectionCacheKey $key, $elements);
}
