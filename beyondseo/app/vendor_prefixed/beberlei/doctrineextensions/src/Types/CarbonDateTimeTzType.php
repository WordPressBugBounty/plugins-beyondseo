<?php

namespace BeyondSEODeps\DoctrineExtensions\Types;

use BeyondSEODeps\Doctrine\DBAL\Types\DateTimeTzType;

class CarbonDateTimeTzType extends DateTimeTzType
{
    use CarbonTypeImplementation;

    public const CARBONDATETIMETZ = 'carbondatetimetz';

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return self::CARBONDATETIMETZ;
    }
}
