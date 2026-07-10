<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Id;

use BeyondSEODeps\Doctrine\DBAL\Connections\PrimaryReadReplicaConnection;
use BeyondSEODeps\Doctrine\DBAL\Platforms\AbstractPlatform;
use BeyondSEODeps\Doctrine\Deprecations\Deprecation;
use BeyondSEODeps\Doctrine\ORM\EntityManagerInterface;
use BeyondSEODeps\Doctrine\ORM\Exception\NotSupported;

use function method_exists;
use function sprintf;

/**
 * Represents an ID generator that uses the database UUID expression
 *
 * @deprecated use an application-side generator instead
 */
class UuidGenerator extends AbstractIdGenerator
{
    public function __construct()
    {
        Deprecation::trigger(
            'doctrine/orm',
            'https://github.com/doctrine/orm/issues/7312',
            '%s is deprecated with no replacement, use an application-side generator instead',
            self::class
        );

        if (! method_exists(AbstractPlatform::class, 'getGuidExpression')) {
            throw NotSupported::createForDbal3(sprintf(
                'Using the database to generate a UUID through %s',
                self::class
            ));
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws NotSupported
     */
    public function generateId(EntityManagerInterface $em, $entity)
    {
        $connection = $em->getConnection();
        $sql        = 'SELECT ' . $connection->getDatabasePlatform()->getGuidExpression();

        if ($connection instanceof PrimaryReadReplicaConnection) {
            $connection->ensureConnectedToPrimary();
        }

        return $connection->executeQuery($sql)->fetchOne();
    }
}
