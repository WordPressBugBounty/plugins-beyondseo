<?php

namespace BeyondSEODeps\Doctrine\DBAL\Driver\Middleware;

use BeyondSEODeps\Doctrine\DBAL\Connection;
use BeyondSEODeps\Doctrine\DBAL\Driver;
use BeyondSEODeps\Doctrine\DBAL\Driver\API\ExceptionConverter;
use BeyondSEODeps\Doctrine\DBAL\Platforms\AbstractPlatform;
use BeyondSEODeps\Doctrine\DBAL\VersionAwarePlatformDriver;
use BeyondSEODeps\Doctrine\Deprecations\Deprecation;
use SensitiveParameter;

abstract class AbstractDriverMiddleware implements VersionAwarePlatformDriver
{
    private Driver $wrappedDriver;

    public function __construct(Driver $wrappedDriver)
    {
        $this->wrappedDriver = $wrappedDriver;
    }

    /**
     * {@inheritDoc}
     */
    public function connect(
        #[SensitiveParameter]
        array $params
    ) {
        return $this->wrappedDriver->connect($params);
    }

    /**
     * {@inheritDoc}
     */
    public function getDatabasePlatform()
    {
        return $this->wrappedDriver->getDatabasePlatform();
    }

    /**
     * {@inheritDoc}
     *
     * @deprecated Use {@link AbstractPlatform::createSchemaManager()} instead.
     */
    public function getSchemaManager(Connection $conn, AbstractPlatform $platform)
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https://github.com/doctrine/dbal/pull/5458',
            'AbstractDriverMiddleware::getSchemaManager() is deprecated.'
                . ' Use AbstractPlatform::createSchemaManager() instead.',
        );

        return $this->wrappedDriver->getSchemaManager($conn, $platform);
    }

    public function getExceptionConverter(): ExceptionConverter
    {
        return $this->wrappedDriver->getExceptionConverter();
    }

    /**
     * {@inheritDoc}
     */
    public function createDatabasePlatformForVersion($version)
    {
        if ($this->wrappedDriver instanceof VersionAwarePlatformDriver) {
            return $this->wrappedDriver->createDatabasePlatformForVersion($version);
        }

        return $this->wrappedDriver->getDatabasePlatform();
    }
}
