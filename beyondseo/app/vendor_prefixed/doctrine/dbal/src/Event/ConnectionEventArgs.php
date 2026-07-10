<?php

namespace BeyondSEODeps\Doctrine\DBAL\Event;

use BeyondSEODeps\Doctrine\Common\EventArgs;
use BeyondSEODeps\Doctrine\DBAL\Connection;

/**
 * Event Arguments used when a Driver connection is established inside Doctrine\DBAL\Connection.
 *
 * @deprecated
 */
class ConnectionEventArgs extends EventArgs
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /** @return Connection */
    public function getConnection()
    {
        return $this->connection;
    }
}
