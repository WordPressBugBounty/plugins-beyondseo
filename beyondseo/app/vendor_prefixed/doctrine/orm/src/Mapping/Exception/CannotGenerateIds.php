<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Mapping\Exception;

use BeyondSEODeps\Doctrine\DBAL\Platforms\AbstractPlatform;
use BeyondSEODeps\Doctrine\ORM\Exception\ORMException;

use function get_debug_type;
use function sprintf;

final class CannotGenerateIds extends ORMException
{
    public static function withPlatform(AbstractPlatform $platform): self
    {
        return new self(sprintf(
            'Platform %s does not support generating identifiers',
            get_debug_type($platform)
        ));
    }
}
