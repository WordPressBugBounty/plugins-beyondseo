<?php

namespace BeyondSEODeps\Doctrine\Bundle\DoctrineBundle\Dbal;

use BeyondSEODeps\Doctrine\DBAL\Connection;
use BeyondSEODeps\Doctrine\DBAL\Tools\Console\ConnectionProvider;
use BeyondSEODeps\Doctrine\Persistence\AbstractManagerRegistry;

class ManagerRegistryAwareConnectionProvider implements ConnectionProvider
{
    private AbstractManagerRegistry $managerRegistry;

    public function __construct(AbstractManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function getDefaultConnection(): Connection
    {
        return $this->managerRegistry->getConnection();
    }

    public function getConnection(string $name): Connection
    {
        return $this->managerRegistry->getConnection($name);
    }
}
