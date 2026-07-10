<?php

namespace BeyondSEODeps\Doctrine\Bundle\DoctrineBundle\Repository;

use BeyondSEODeps\Doctrine\ORM\EntityManagerInterface;
use BeyondSEODeps\Doctrine\ORM\EntityRepository;
use BeyondSEODeps\Doctrine\ORM\Repository\RepositoryFactory;
use BeyondSEODeps\Doctrine\Persistence\ObjectRepository;
use ReflectionMethod;

if ((new ReflectionMethod(RepositoryFactory::class, 'getRepository'))->hasReturnType()) {
    // ORM >= 3
    /** @internal */
    trait RepositoryFactoryCompatibility
    {
        /**
         * Gets the repository for an entity class.
         *
         * @param class-string<T> $entityName
         *
         * @return EntityRepository<T>
         *
         * @template T of object
         */
        public function getRepository(EntityManagerInterface $entityManager, string $entityName): EntityRepository
        {
            return $this->doGetRepository($entityManager, $entityName, true);
        }
    }
} else {
    // ORM 2
    /** @internal */
    trait RepositoryFactoryCompatibility
    {
        /** {@inheritDoc} */
        public function getRepository(EntityManagerInterface $entityManager, $entityName): ObjectRepository
        {
            return $this->doGetRepository($entityManager, $entityName, false);
        }
    }
}
