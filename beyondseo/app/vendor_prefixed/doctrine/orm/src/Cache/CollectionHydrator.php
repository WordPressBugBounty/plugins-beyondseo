<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Cache;

use BeyondSEODeps\Doctrine\Common\Collections\Collection;
use BeyondSEODeps\Doctrine\ORM\Mapping\ClassMetadata;
use BeyondSEODeps\Doctrine\ORM\PersistentCollection;

/**
 * Hydrator cache entry for collections
 */
interface CollectionHydrator
{
    /**
     * @param array|mixed[]|Collection $collection The collection.
     *
     * @return CollectionCacheEntry
     */
    public function buildCacheEntry(ClassMetadata $metadata, CollectionCacheKey $key, $collection);

    /** @return mixed[]|null */
    public function loadCacheEntry(ClassMetadata $metadata, CollectionCacheKey $key, CollectionCacheEntry $entry, PersistentCollection $collection);
}
