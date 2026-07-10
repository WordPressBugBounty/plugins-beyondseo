<?php

declare(strict_types=1);

namespace BeyondSEODeps\Brick\Geo\Projector;

use BeyondSEODeps\Brick\Geo\CoordinateSystem;
use BeyondSEODeps\Brick\Geo\Point;

/**
 * Removes the Z and/or M coordinates of a geometry.
 */
final class RemoveZMProjector implements Projector
{
    public function __construct(
        private readonly bool $removeZ = false,
        private readonly bool $removeM = false,
    ) {
    }

    public function project(Point $point): Point
    {
        $coordinateSystem = $this->getTargetCoordinateSystem($point->coordinateSystem());

        if ($point->isEmpty()) {
            return new Point($coordinateSystem);
        }

        $x = $point->x();
        $y = $point->y();
        $z = $point->z();
        $m = $point->m();

        assert($x !== null);
        assert($y !== null);

        $coords = [$x, $y];

        if (! $this->removeZ && $z !== null) {
            $coords[] = $z;
        }

        if (! $this->removeM && $m !== null) {
            $coords[] = $m;
        }

        return new Point($coordinateSystem, ...$coords);
    }

    public function getTargetCoordinateSystem(CoordinateSystem $sourceCoordinateSystem): CoordinateSystem
    {
        return $sourceCoordinateSystem
            ->withZ($sourceCoordinateSystem->hasZ() && ! $this->removeZ)
            ->withM($sourceCoordinateSystem->hasM() && ! $this->removeM);
    }
}
