<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\DBAL\Schema;

use BeyondSEODeps\Doctrine\DBAL\Connection;

/**
 * Creates a schema manager for the given connection.
 *
 * This interface is an extension point for applications that need to override schema managers.
 */
interface SchemaManagerFactory
{
    public function createSchemaManager(Connection $connection): AbstractSchemaManager;
}
