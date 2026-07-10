<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Tools\Exception;

use BeyondSEODeps\Doctrine\ORM\Exception\ORMException;
use BeyondSEODeps\Doctrine\ORM\Exception\SchemaToolException;

final class NotSupported extends ORMException implements SchemaToolException
{
    public static function create(): self
    {
        return new self('This behaviour is (currently) not supported by Doctrine 2');
    }
}
