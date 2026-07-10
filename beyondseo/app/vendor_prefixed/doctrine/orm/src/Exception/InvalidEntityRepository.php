<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Exception;

use BeyondSEODeps\Doctrine\ORM\EntityRepository;

final class InvalidEntityRepository extends ORMException implements ConfigurationException
{
    public static function fromClassName(string $className): self
    {
        return new self(
            "Invalid repository class '" . $className . "'. It must be a " . EntityRepository::class . '.'
        );
    }
}
