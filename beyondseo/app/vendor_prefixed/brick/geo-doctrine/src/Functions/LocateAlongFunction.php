<?php

declare(strict_types=1);

namespace BeyondSEODeps\Brick\Geo\Doctrine\Functions;

/**
 * LocateAlong() function.
 */
class LocateAlongFunction extends AbstractFunction
{
    protected function getSqlFunctionName() : string
    {
        return 'ST_LocateAlong';
    }

    protected function getParameterCount() : int
    {
        return 2;
    }
}
