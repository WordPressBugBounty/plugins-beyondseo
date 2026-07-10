<?php

declare(strict_types=1);

namespace BeyondSEODeps\Brick\Geo\Doctrine\Functions;

/**
 * LocateBetween() function.
 */
class LocateBetweenFunction extends AbstractFunction
{
    protected function getSqlFunctionName() : string
    {
        return 'ST_LocateBetween';
    }

    protected function getParameterCount() : int
    {
        return 3;
    }
}
