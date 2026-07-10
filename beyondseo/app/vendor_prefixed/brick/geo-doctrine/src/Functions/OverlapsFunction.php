<?php

declare(strict_types=1);

namespace BeyondSEODeps\Brick\Geo\Doctrine\Functions;

/**
 * Overlaps() function.
 */
class OverlapsFunction extends AbstractFunction
{
    protected function getSqlFunctionName() : string
    {
        return 'ST_Overlaps';
    }

    protected function getParameterCount() : int
    {
        return 2;
    }
}
