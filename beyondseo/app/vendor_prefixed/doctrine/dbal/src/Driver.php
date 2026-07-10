<?php

namespace BeyondSEODeps\Doctrine\DBAL;

use BeyondSEODeps\Doctrine\DBAL\Driver\API\ExceptionConverter;
use BeyondSEODeps\Doctrine\DBAL\Driver\Connection as DriverConnection;
use BeyondSEODeps\Doctrine\DBAL\Driver\Exception;
use BeyondSEODeps\Doctrine\DBAL\Platforms\AbstractPlatform;
use BeyondSEODeps\Doctrine\DBAL\Schema\AbstractSchemaManager;
use SensitiveParameter;

/**
 * Driver interface.
 * Interface that all DBAL drivers must implement.
 *
 * @phpstan-import-type Params from DriverManager
 */
interface Driver
{
    /**
     * Attempts to create a connection with the database.
     *
     * @param array<string, mixed> $params All connection parameters.
     * @phpstan-param Params $params All connection parameters.
     *
     * @return DriverConnection The database connection.
     *
     * @throws Exception
     */
    public function connect(
        #[SensitiveParameter]
        array $params
    );

    /**
     * Gets the DatabasePlatform instance that provides all the metadata about
     * the platform this driver connects to.
     *
     * @return AbstractPlatform The database platform.
     */
    public function getDatabasePlatform();

    /**
     * Gets the SchemaManager that can be used to inspect and change the underlying
     * database schema of the platform this driver connects to.
     *
     * @deprecated Use {@link AbstractPlatform::createSchemaManager()} instead.
     *
     * @return AbstractSchemaManager
     */
    public function getSchemaManager(Connection $conn, AbstractPlatform $platform);

    /**
     * Gets the ExceptionConverter that can be used to convert driver-level exceptions into DBAL exceptions.
     */
    public function getExceptionConverter(): ExceptionConverter;
}
