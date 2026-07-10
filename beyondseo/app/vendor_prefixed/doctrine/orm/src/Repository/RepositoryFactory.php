<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Repository;

use BeyondSEODeps\Doctrine\ORM\EntityManagerInterface;
use BeyondSEODeps\Doctrine\Persistence\ObjectRepository;

/**
 * Interface for entity repository factory.
 */
interface RepositoryFactory
{
    /**
     * Gets the repository for an entity class.
     *
     * @param EntityManagerInterface $entityManager The EntityManager instance.
     * @param class-string<T>        $entityName    The name of the entity.
     *
     * @return ObjectRepository<T> This type will change to {@see EntityRepository} in 3.0.
     *
     * @template T of object
     */
    public function getRepository(EntityManagerInterface $entityManager, $entityName);
}
