<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Cache;

use BeyondSEODeps\Doctrine\ORM\Cache\Persister\CachedPersister;
use BeyondSEODeps\Doctrine\ORM\EntityManagerInterface;
use BeyondSEODeps\Doctrine\ORM\Mapping\ClassMetadata;
use BeyondSEODeps\Doctrine\ORM\PersistentCollection;
use BeyondSEODeps\Doctrine\ORM\Query;
use BeyondSEODeps\Doctrine\ORM\UnitOfWork;

use function assert;

/**
 * Default hydrator cache for collections
 */
class DefaultCollectionHydrator implements CollectionHydrator
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var UnitOfWork */
    private $uow;

    /** @var array<string,mixed> */
    private static $hints = [Query::HINT_CACHE_ENABLED => true];

    /** @param EntityManagerInterface $em The entity manager. */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em  = $em;
        $this->uow = $em->getUnitOfWork();
    }

    /**
     * {@inheritDoc}
     */
    public function buildCacheEntry(ClassMetadata $metadata, CollectionCacheKey $key, $collection)
    {
        $data = [];

        foreach ($collection as $index => $entity) {
            $data[$index] = new EntityCacheKey($metadata->rootEntityName, $this->uow->getEntityIdentifier($entity));
        }

        return new CollectionCacheEntry($data);
    }

    /**
     * {@inheritDoc}
     */
    public function loadCacheEntry(ClassMetadata $metadata, CollectionCacheKey $key, CollectionCacheEntry $entry, PersistentCollection $collection)
    {
        $assoc           = $metadata->associationMappings[$key->association];
        $targetPersister = $this->uow->getEntityPersister($assoc['targetEntity']);
        assert($targetPersister instanceof CachedPersister);
        $targetRegion = $targetPersister->getCacheRegion();
        $list         = [];

        /**
         * @var EntityCacheEntry[]|null $entityEntries
         * @phpstan-ignore method.deprecatedInterface
         */
        $entityEntries = $targetRegion->getMultiple($entry);

        if ($entityEntries === null) {
            return null;
        }

        foreach ($entityEntries as $index => $entityEntry) {
            $entity = $this->uow->createEntity(
                $entityEntry->class,
                $entityEntry->resolveAssociationEntries($this->em),
                self::$hints
            );

            $collection->hydrateSet($index, $entity);

            $list[$index] = $entity;
        }

        $this->uow->hydrationComplete();

        return $list;
    }
}
