<?php

namespace BeyondSEODeps\DoctrineExtensions\Types;

use BeyondSEODeps\Doctrine\DBAL\Types\DateTimeType;

class CarbonDateTimeType extends DateTimeType
{
    use CarbonTypeImplementation;

    public const CARBONDATETIME = 'carbondatetime';

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return self::CARBONDATETIME;
    }
}
