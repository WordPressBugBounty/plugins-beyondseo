<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Internal;

use BeyondSEODeps\Doctrine\DBAL\Platforms\AbstractPlatform;
use BeyondSEODeps\Doctrine\DBAL\Platforms\DB2Platform;
use BeyondSEODeps\Doctrine\DBAL\Platforms\OraclePlatform;
use BeyondSEODeps\Doctrine\DBAL\Platforms\PostgreSQLPlatform;

use function get_class;
use function method_exists;
use function strpos;
use function strtolower;
use function strtoupper;

/** @internal */
trait SQLResultCasing
{
    private function getSQLResultCasing(AbstractPlatform $platform, string $column): string
    {
        if ($platform instanceof DB2Platform || $platform instanceof OraclePlatform) {
            return strtoupper($column);
        }

        if ($platform instanceof PostgreSQLPlatform) {
            return strtolower($column);
        }

        if (strpos(get_class($platform), 'BeyondSEODeps\\Doctrine\\DBAL\\Platforms\\') !== 0 && method_exists(AbstractPlatform::class, 'getSQLResultCasing')) {
            return $platform->getSQLResultCasing($column);
        }

        return $column;
    }
}
