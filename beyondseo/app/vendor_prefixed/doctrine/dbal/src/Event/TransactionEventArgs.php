<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\DBAL\Event;

use BeyondSEODeps\Doctrine\Common\EventArgs;
use BeyondSEODeps\Doctrine\DBAL\Connection;

/** @deprecated */
abstract class TransactionEventArgs extends EventArgs
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }
}
