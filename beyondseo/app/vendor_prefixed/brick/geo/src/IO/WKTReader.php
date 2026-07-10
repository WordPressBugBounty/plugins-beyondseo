<?php

declare(strict_types=1);

namespace BeyondSEODeps\Brick\Geo\IO;

use BeyondSEODeps\Brick\Geo\Exception\GeometryIOException;
use BeyondSEODeps\Brick\Geo\Geometry;

/**
 * Builds geometries out of Well-Known Text strings.
 */
class WKTReader extends AbstractWKTReader
{
    /**
     * @param string $wkt  The WKT to read.
     * @param int    $srid The optional SRID of the geometry.
     *
     * @throws GeometryIOException
     */
    public function read(string $wkt, int $srid = 0) : Geometry
    {
        $parser = new WKTParser(strtoupper($wkt));
        $geometry = $this->readGeometry($parser, $srid);

        if (! $parser->isEndOfStream()) {
            throw GeometryIOException::invalidWKT();
        }

        return $geometry;
    }
}
