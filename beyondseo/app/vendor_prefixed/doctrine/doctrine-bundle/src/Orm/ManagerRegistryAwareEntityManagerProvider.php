<?php

namespace BeyondSEODeps\Doctrine\Bundle\DoctrineBundle\Orm;

use BeyondSEODeps\Doctrine\ORM\EntityManagerInterface;
use BeyondSEODeps\Doctrine\ORM\Tools\Console\EntityManagerProvider;
use BeyondSEODeps\Doctrine\Persistence\ManagerRegistry;
use RuntimeException;

use function get_class;
use function sprintf;

final class ManagerRegistryAwareEntityManagerProvider implements EntityManagerProvider
{
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function getDefaultManager(): EntityManagerInterface
    {
        return $this->getManager($this->managerRegistry->getDefaultManagerName());
    }

    public function getManager(string $name): EntityManagerInterface
    {
        $em = $this->managerRegistry->getManager($name);

        if ($em instanceof EntityManagerInterface) {
            return $em;
        }

        throw new RuntimeException(
            sprintf(
                'Only managers of type "%s" are supported. Instance of "%s given.',
                EntityManagerInterface::class,
                get_class($em),
            ),
        );
    }
}
