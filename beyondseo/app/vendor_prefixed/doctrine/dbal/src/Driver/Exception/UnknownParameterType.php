<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\DBAL\Driver\Exception;

use BeyondSEODeps\Doctrine\DBAL\Driver\AbstractException;

use function sprintf;

/** @internal */
final class UnknownParameterType extends AbstractException
{
    /** @param mixed $type */
    public static function new($type): self
    {
        return new self(sprintf('Unknown parameter type, %d given.', $type));
    }
}
