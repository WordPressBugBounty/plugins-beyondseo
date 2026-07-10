<?php

declare(strict_types=1);

namespace BeyondSEODeps\Brick\Geo\Doctrine\Functions;

/**
 * Simplify() function.
 */
class SimplifyFunction extends AbstractFunction
{
    protected function getSqlFunctionName() : string
    {
        return 'ST_Simplify';
    }

    protected function getParameterCount() : int
    {
        return 2;
    }
}
