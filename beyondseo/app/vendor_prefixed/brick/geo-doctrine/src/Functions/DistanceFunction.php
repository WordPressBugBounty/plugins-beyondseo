<?php

declare(strict_types=1);

namespace BeyondSEODeps\Brick\Geo\Doctrine\Functions;

/**
 * Distance() function.
 */
class DistanceFunction extends AbstractFunction
{
    protected function getSqlFunctionName() : string
    {
        return 'ST_Distance';
    }

    protected function getParameterCount() : int
    {
        return 2;
    }
}
