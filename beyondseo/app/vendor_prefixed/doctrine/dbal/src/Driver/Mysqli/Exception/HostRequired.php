<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\DBAL\Driver\Mysqli\Exception;

use BeyondSEODeps\Doctrine\DBAL\Driver\AbstractException;

/** @internal */
final class HostRequired extends AbstractException
{
    public static function forPersistentConnection(): self
    {
        return new self('The "host" parameter is required for a persistent connection');
    }
}
