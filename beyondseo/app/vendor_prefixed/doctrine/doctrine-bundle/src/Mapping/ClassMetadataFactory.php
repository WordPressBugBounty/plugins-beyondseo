<?php

namespace BeyondSEODeps\Doctrine\Bundle\DoctrineBundle\Mapping;

use BeyondSEODeps\Doctrine\ORM\Id\AbstractIdGenerator;
use BeyondSEODeps\Doctrine\ORM\Mapping\ClassMetadata;
use BeyondSEODeps\Doctrine\ORM\Mapping\ClassMetadataFactory as BaseClassMetadataFactory;

use function assert;

class ClassMetadataFactory extends BaseClassMetadataFactory
{
    /**
     * {@inheritDoc}
     */
    protected function doLoadMetadata($class, $parent, $rootEntityFound, array $nonSuperclassParents): void
    {
        parent::doLoadMetadata($class, $parent, $rootEntityFound, $nonSuperclassParents);

        $customGeneratorDefinition = $class->customGeneratorDefinition;

        if (! isset($customGeneratorDefinition['instance'])) {
            return;
        }

        assert($customGeneratorDefinition['instance'] instanceof AbstractIdGenerator);

        $class->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_CUSTOM);
        $class->setIdGenerator($customGeneratorDefinition['instance']);
        unset($customGeneratorDefinition['instance']);
        $class->setCustomGeneratorDefinition($customGeneratorDefinition);
    }
}
