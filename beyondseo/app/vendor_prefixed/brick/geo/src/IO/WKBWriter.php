<?php

declare(strict_types=1);

namespace BeyondSEODeps\Brick\Geo\IO;

use BeyondSEODeps\Brick\Geo\Geometry;

/**
 * Writes geometries in the WKB format.
 */
class WKBWriter extends AbstractWKBWriter
{
    protected function packHeader(Geometry $geometry, bool $outer) : string
    {
        $geometryType = $geometry->geometryTypeBinary();

        $cs = $geometry->coordinateSystem();

        if ($cs->hasZ()) {
            $geometryType += 1000;
        }

        if ($cs->hasM()) {
            $geometryType += 2000;
        }

        return $this->packUnsignedInteger($geometryType);
    }
}
