<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Event;

use BeyondSEODeps\Doctrine\ORM\EntityManagerInterface;
use BeyondSEODeps\Doctrine\ORM\Mapping\ClassMetadata;
use BeyondSEODeps\Doctrine\Persistence\Event\LoadClassMetadataEventArgs as BaseLoadClassMetadataEventArgs;

/**
 * Class that holds event arguments for a loadMetadata event.
 *
 * @extends BaseLoadClassMetadataEventArgs<ClassMetadata<object>, EntityManagerInterface>
 */
class LoadClassMetadataEventArgs extends BaseLoadClassMetadataEventArgs
{
    /**
     * Retrieve associated EntityManager.
     *
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->getObjectManager();
    }
}
