<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Persisters\Exception;

use BeyondSEODeps\Doctrine\ORM\Exception\PersisterException;

class InvalidOrientation extends PersisterException
{
    public static function fromClassNameAndField(string $className, string $field): self
    {
        return new self('Invalid order by orientation specified for ' . $className . '#' . $field);
    }
}
