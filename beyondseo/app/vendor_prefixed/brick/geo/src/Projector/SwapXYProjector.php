<?php

declare(strict_types=1);

namespace BeyondSEODeps\Brick\Geo\Projector;

use BeyondSEODeps\Brick\Geo\CoordinateSystem;
use BeyondSEODeps\Brick\Geo\Point;

/**
 * Swaps the X and Y coordinates of a Geometry, while keeping the same SRID.
 */
final class SwapXYProjector implements Projector
{
    public function project(Point $point): Point
    {
        if ($point->isEmpty()) {
            return $point;
        }

        $coordinates = $point->toArray();

        [$x, $y] = $coordinates;

        $coordinates[0] = $y;
        $coordinates[1] = $x;

        return new Point($point->coordinateSystem(), ...$coordinates);
    }

    public function getTargetCoordinateSystem(CoordinateSystem $sourceCoordinateSystem): CoordinateSystem
    {
        return $sourceCoordinateSystem;
    }
}
