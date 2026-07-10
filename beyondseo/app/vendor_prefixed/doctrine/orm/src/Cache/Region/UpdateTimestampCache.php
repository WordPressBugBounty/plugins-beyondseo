<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Cache\Region;

use BeyondSEODeps\Doctrine\ORM\Cache\CacheKey;
use BeyondSEODeps\Doctrine\ORM\Cache\TimestampCacheEntry;
use BeyondSEODeps\Doctrine\ORM\Cache\TimestampRegion;

/**
 * Tracks the timestamps of the most recent updates to particular keys.
 */
class UpdateTimestampCache extends DefaultRegion implements TimestampRegion
{
    /**
     * {@inheritDoc}
     */
    public function update(CacheKey $key)
    {
        $this->put($key, new TimestampCacheEntry());
    }
}
