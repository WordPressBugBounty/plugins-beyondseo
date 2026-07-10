<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Repository;

use BeyondSEODeps\Doctrine\Deprecations\Deprecation;
use BeyondSEODeps\Doctrine\ORM\EntityManagerInterface;
use BeyondSEODeps\Doctrine\ORM\EntityRepository;
use BeyondSEODeps\Doctrine\Persistence\ObjectRepository;

use function spl_object_id;

/**
 * This factory is used to create default repository objects for entities at runtime.
 */
final class DefaultRepositoryFactory implements RepositoryFactory
{
    /**
     * The list of EntityRepository instances.
     *
     * @var ObjectRepository[]
     * @phpstan-var array<string, ObjectRepository>
     */
    private $repositoryList = [];

    /**
     * {@inheritDoc}
     */
    public function getRepository(EntityManagerInterface $entityManager, $entityName): ObjectRepository
    {
        $repositoryHash = $entityManager->getClassMetadata($entityName)->getName() . spl_object_id($entityManager);

        if (isset($this->repositoryList[$repositoryHash])) {
            return $this->repositoryList[$repositoryHash];
        }

        return $this->repositoryList[$repositoryHash] = $this->createRepository($entityManager, $entityName);
    }

    /**
     * Create a new repository instance for an entity class.
     *
     * @param EntityManagerInterface $entityManager The EntityManager instance.
     * @param string                 $entityName    The name of the entity.
     */
    private function createRepository(
        EntityManagerInterface $entityManager,
        string $entityName
    ): ObjectRepository {
        $metadata            = $entityManager->getClassMetadata($entityName);
        $repositoryClassName = $metadata->customRepositoryClassName
            ?: $entityManager->getConfiguration()->getDefaultRepositoryClassName();

        $repository = new $repositoryClassName($entityManager, $metadata);
        if (! $repository instanceof EntityRepository) {
            Deprecation::trigger(
                'doctrine/orm',
                'https://github.com/doctrine/orm/pull/9533',
                'Configuring %s as repository class is deprecated because it does not extend %s.',
                $repositoryClassName,
                EntityRepository::class
            );
        }

        return $repository;
    }
}
