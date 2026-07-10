<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Cache\Persister\Entity;

use BeyondSEODeps\Doctrine\ORM\Cache\Exception\CannotUpdateReadOnlyEntity;
use BeyondSEODeps\Doctrine\ORM\Proxy\DefaultProxyClassNameResolver;

/**
 * Specific read-only region entity persister
 */
class ReadOnlyCachedEntityPersister extends NonStrictReadWriteCachedEntityPersister
{
    /**
     * {@inheritDoc}
     */
    public function update($entity)
    {
        throw CannotUpdateReadOnlyEntity::fromEntity(DefaultProxyClassNameResolver::getClass($entity));
    }
}
