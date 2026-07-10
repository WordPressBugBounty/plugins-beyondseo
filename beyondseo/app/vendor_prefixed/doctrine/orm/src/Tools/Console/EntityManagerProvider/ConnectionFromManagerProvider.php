<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Tools\Console\EntityManagerProvider;

use BeyondSEODeps\Doctrine\DBAL\Connection;
use BeyondSEODeps\Doctrine\DBAL\Tools\Console\ConnectionProvider;
use BeyondSEODeps\Doctrine\ORM\Tools\Console\EntityManagerProvider;

final class ConnectionFromManagerProvider implements ConnectionProvider
{
    /** @var EntityManagerProvider */
    private $entityManagerProvider;

    public function __construct(EntityManagerProvider $entityManagerProvider)
    {
        $this->entityManagerProvider = $entityManagerProvider;
    }

    public function getDefaultConnection(): Connection
    {
        return $this->entityManagerProvider->getDefaultManager()->getConnection();
    }

    public function getConnection(string $name): Connection
    {
        return $this->entityManagerProvider->getManager($name)->getConnection();
    }
}
