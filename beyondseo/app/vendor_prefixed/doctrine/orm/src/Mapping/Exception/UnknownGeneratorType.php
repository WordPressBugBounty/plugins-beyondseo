<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Mapping\Exception;

use BeyondSEODeps\Doctrine\ORM\Exception\ORMException;

final class UnknownGeneratorType extends ORMException
{
    public static function create(int $generatorType): self
    {
        return new self('Unknown generator type: ' . $generatorType);
    }
}
