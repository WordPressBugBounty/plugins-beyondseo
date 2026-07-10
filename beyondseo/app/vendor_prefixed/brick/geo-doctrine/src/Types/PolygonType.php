<?php

declare(strict_types=1);

namespace BeyondSEODeps\Brick\Geo\Doctrine\Types;

use BeyondSEODeps\Brick\Geo\Proxy\PolygonProxy;

/**
 * Doctrine type for Polygon.
 */
class PolygonType extends GeometryType
{
    public function getName() : string
    {
        return 'Polygon';
    }

    protected function getProxyClassName() : string
    {
        return PolygonProxy::class;
    }
}
