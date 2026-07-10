<?php

declare(strict_types=1);

namespace BeyondSEODeps\Brick\Geo\Doctrine\Types;

use BeyondSEODeps\Brick\Geo\Proxy\MultiPointProxy;

/**
 * Doctrine type for MultiPoint.
 */
class MultiPointType extends GeometryType
{
    public function getName() : string
    {
        return 'MultiPoint';
    }

    protected function getProxyClassName() : string
    {
        return MultiPointProxy::class;
    }

    protected function hasKnownSubclasses() : bool
    {
        return false;
    }
}
