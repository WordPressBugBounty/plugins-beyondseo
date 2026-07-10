<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\Persistence\Event;

use BeyondSEODeps\Doctrine\Common\EventArgs;
use BeyondSEODeps\Doctrine\Persistence\Mapping\ClassMetadata;
use BeyondSEODeps\Doctrine\Persistence\ObjectManager;

/**
 * Class that holds event arguments for a loadMetadata event.
 *
 * @template-covariant TClassMetadata of ClassMetadata<object>
 * @template-covariant TObjectManager of ObjectManager
 */
class LoadClassMetadataEventArgs extends EventArgs
{
    /**
     * @var ClassMetadata
     * @phpstan-var TClassMetadata
     */
    private $classMetadata;

    /**
     * @var ObjectManager
     * @phpstan-var TObjectManager
     */
    private $objectManager;

    /**
     * @phpstan-param TClassMetadata $classMetadata
     * @phpstan-param TObjectManager $objectManager
     */
    public function __construct(ClassMetadata $classMetadata, ObjectManager $objectManager)
    {
        $this->classMetadata = $classMetadata;
        $this->objectManager = $objectManager;
    }

    /**
     * Retrieves the associated ClassMetadata.
     *
     * @return ClassMetadata
     * @phpstan-return TClassMetadata
     */
    public function getClassMetadata()
    {
        return $this->classMetadata;
    }

    /**
     * Retrieves the associated ObjectManager.
     *
     * @return TObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }
}
