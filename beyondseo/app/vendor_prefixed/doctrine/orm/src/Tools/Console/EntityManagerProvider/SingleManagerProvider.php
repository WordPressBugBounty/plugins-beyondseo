<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Tools\Console\EntityManagerProvider;

use BeyondSEODeps\Doctrine\ORM\EntityManagerInterface;
use BeyondSEODeps\Doctrine\ORM\Tools\Console\EntityManagerProvider;

final class SingleManagerProvider implements EntityManagerProvider
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var string */
    private $defaultManagerName;

    public function __construct(EntityManagerInterface $entityManager, string $defaultManagerName = 'default')
    {
        $this->entityManager      = $entityManager;
        $this->defaultManagerName = $defaultManagerName;
    }

    public function getDefaultManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    public function getManager(string $name): EntityManagerInterface
    {
        if ($name !== $this->defaultManagerName) {
            throw UnknownManagerException::unknownManager($name, [$this->defaultManagerName]);
        }

        return $this->entityManager;
    }
}
