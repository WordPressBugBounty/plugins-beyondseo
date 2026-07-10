<?php

declare(strict_types=1);

namespace BeyondSEODeps\Brick\Geo\Doctrine\Functions;

/**
 * Equals() function.
 */
class EqualsFunction extends AbstractFunction
{
    protected function getSqlFunctionName() : string
    {
        return 'ST_Equals';
    }

    protected function getParameterCount() : int
    {
        return 2;
    }
}
