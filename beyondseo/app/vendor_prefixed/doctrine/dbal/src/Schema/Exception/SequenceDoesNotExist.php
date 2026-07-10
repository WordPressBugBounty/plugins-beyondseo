<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\DBAL\Schema\Exception;

use BeyondSEODeps\Doctrine\DBAL\Schema\SchemaException;

use function sprintf;

final class SequenceDoesNotExist extends SchemaException
{
    public static function new(string $sequenceName): self
    {
        return new self(
            sprintf('There exists no sequence with the name "%s".', $sequenceName),
            self::SEQUENCE_DOENST_EXIST,
        );
    }
}
