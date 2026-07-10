<?php

namespace BeyondSEODeps\DoctrineExtensions\Types;

use BeyondSEODeps\Doctrine\DBAL\Types\TimeType;

class CarbonTimeType extends TimeType
{
    use CarbonTypeImplementation;

    public const CARBONTIME = 'carbontime';

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return self::CARBONTIME;
    }
}
