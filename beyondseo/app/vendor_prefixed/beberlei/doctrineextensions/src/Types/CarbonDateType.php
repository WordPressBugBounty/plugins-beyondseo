<?php

namespace BeyondSEODeps\DoctrineExtensions\Types;

use BeyondSEODeps\Doctrine\DBAL\Types\DateType;

class CarbonDateType extends DateType
{
    use CarbonTypeImplementation;

    public const CARBONDATE = 'carbondate';

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return self::CARBONDATE;
    }
}
