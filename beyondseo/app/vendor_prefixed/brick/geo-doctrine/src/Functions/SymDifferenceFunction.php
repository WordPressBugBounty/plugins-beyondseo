<?php

declare(strict_types=1);

namespace BeyondSEODeps\Brick\Geo\Doctrine\Functions;

/**
 * SymDifference() function.
 */
class SymDifferenceFunction extends AbstractFunction
{
    protected function getSqlFunctionName() : string
    {
        return 'ST_SymDifference';
    }

    protected function getParameterCount() : int
    {
        return 2;
    }
}
