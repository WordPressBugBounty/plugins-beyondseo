<?php

namespace BeyondSEODeps\Doctrine\DBAL\Driver;

use BeyondSEODeps\Doctrine\DBAL\Connection;
use BeyondSEODeps\Doctrine\DBAL\Driver\API\ExceptionConverter;
use BeyondSEODeps\Doctrine\DBAL\Driver\API\PostgreSQL;
use BeyondSEODeps\Doctrine\DBAL\Exception;
use BeyondSEODeps\Doctrine\DBAL\Platforms\AbstractPlatform;
use BeyondSEODeps\Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use BeyondSEODeps\Doctrine\DBAL\Platforms\PostgreSQL120Platform;
use BeyondSEODeps\Doctrine\DBAL\Platforms\PostgreSQL94Platform;
use BeyondSEODeps\Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use BeyondSEODeps\Doctrine\DBAL\Schema\PostgreSQLSchemaManager;
use BeyondSEODeps\Doctrine\DBAL\VersionAwarePlatformDriver;
use BeyondSEODeps\Doctrine\Deprecations\Deprecation;

use function assert;
use function preg_match;
use function version_compare;

/**
 * Abstract base implementation of the {@see Driver} interface for PostgreSQL based drivers.
 */
abstract class AbstractPostgreSQLDriver implements VersionAwarePlatformDriver
{
    /**
     * {@inheritDoc}
     */
    public function createDatabasePlatformForVersion($version)
    {
        if (preg_match('/^(?P<major>\d+)(?:\.(?P<minor>\d+)(?:\.(?P<patch>\d+))?)?/', $version, $versionParts) !== 1) {
            throw Exception::invalidPlatformVersionSpecified(
                $version,
                '<major_version>.<minor_version>.<patch_version>',
            );
        }

        $majorVersion = $versionParts['major'];
        $minorVersion = $versionParts['minor'] ?? 0;
        $patchVersion = $versionParts['patch'] ?? 0;
        $version      = $majorVersion . '.' . $minorVersion . '.' . $patchVersion;

        if (version_compare($version, '12.0', '>=')) {
            return new PostgreSQL120Platform();
        }

        if (version_compare($version, '10.0', '>=')) {
            return new PostgreSQL100Platform();
        }

        Deprecation::trigger(
            'doctrine/dbal',
            'https://github.com/doctrine/dbal/pull/5060',
            'PostgreSQL 9 support is deprecated and will be removed in DBAL 4.'
                . ' Consider upgrading to Postgres 10 or later.',
        );

        return new PostgreSQL94Platform();
    }

    /**
     * {@inheritDoc}
     */
    public function getDatabasePlatform()
    {
        return new PostgreSQL94Platform();
    }

    /**
     * {@inheritDoc}
     *
     * @deprecated Use {@link PostgreSQLPlatform::createSchemaManager()} instead.
     */
    public function getSchemaManager(Connection $conn, AbstractPlatform $platform)
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https://github.com/doctrine/dbal/pull/5458',
            'AbstractPostgreSQLDriver::getSchemaManager() is deprecated.'
                . ' Use PostgreSQLPlatform::createSchemaManager() instead.',
        );

        assert($platform instanceof PostgreSQLPlatform);

        return new PostgreSQLSchemaManager($conn, $platform);
    }

    public function getExceptionConverter(): ExceptionConverter
    {
        return new PostgreSQL\ExceptionConverter();
    }
}
