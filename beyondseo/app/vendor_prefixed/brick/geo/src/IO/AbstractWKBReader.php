<?php

declare(strict_types=1);

namespace BeyondSEODeps\Brick\Geo\IO;

use BeyondSEODeps\Brick\Geo\CircularString;
use BeyondSEODeps\Brick\Geo\CompoundCurve;
use BeyondSEODeps\Brick\Geo\CoordinateSystem;
use BeyondSEODeps\Brick\Geo\Curve;
use BeyondSEODeps\Brick\Geo\CurvePolygon;
use BeyondSEODeps\Brick\Geo\Geometry;
use BeyondSEODeps\Brick\Geo\GeometryCollection;
use BeyondSEODeps\Brick\Geo\LineString;
use BeyondSEODeps\Brick\Geo\MultiLineString;
use BeyondSEODeps\Brick\Geo\MultiPoint;
use BeyondSEODeps\Brick\Geo\MultiPolygon;
use BeyondSEODeps\Brick\Geo\Point;
use BeyondSEODeps\Brick\Geo\Polygon;
use BeyondSEODeps\Brick\Geo\PolyhedralSurface;
use BeyondSEODeps\Brick\Geo\TIN;
use BeyondSEODeps\Brick\Geo\Triangle;

use BeyondSEODeps\Brick\Geo\Exception\GeometryIOException;

/**
 * Base class for WKBReader and EWKBReader.
 */
abstract class AbstractWKBReader
{
    /**
     * @throws GeometryIOException
     */
    abstract protected function readGeometryHeader(WKBBuffer $buffer) : WKBGeometryHeader;

    /**
     * @throws GeometryIOException
     */
    protected function readGeometry(WKBBuffer $buffer, int $srid) : Geometry
    {
        $buffer->readByteOrder();

        $geometryHeader = $this->readGeometryHeader($buffer);

        $cs = new CoordinateSystem(
            $geometryHeader->hasZ,
            $geometryHeader->hasM,
            $geometryHeader->srid ?? $srid
        );

        return match ($geometryHeader->geometryType) {
            Geometry::POINT => $this->readPoint($buffer, $cs),
            Geometry::LINESTRING => $this->readLineString($buffer, $cs),
            Geometry::CIRCULARSTRING => $this->readCircularString($buffer, $cs),
            Geometry::COMPOUNDCURVE => $this->readCompoundCurve($buffer, $cs),
            Geometry::POLYGON => $this->readPolygon($buffer, $cs),
            Geometry::CURVEPOLYGON => $this->readCurvePolygon($buffer, $cs),
            Geometry::MULTIPOINT => $this->readMultiPoint($buffer, $cs),
            Geometry::MULTILINESTRING => $this->readMultiLineString($buffer, $cs),
            Geometry::MULTIPOLYGON => $this->readMultiPolygon($buffer, $cs),
            Geometry::GEOMETRYCOLLECTION => $this->readGeometryCollection($buffer, $cs),
            Geometry::POLYHEDRALSURFACE => $this->readPolyhedralSurface($buffer, $cs),
            Geometry::TIN => $this->readTIN($buffer, $cs),
            Geometry::TRIANGLE => $this->readTriangle($buffer, $cs),
            default => throw GeometryIOException::unsupportedWKBType($geometryHeader->geometryType),
        };
    }

    private function readPoint(WKBBuffer $buffer, CoordinateSystem $cs) : Point
    {
        $coords = $buffer->readDoubles($cs->coordinateDimension());

        return new Point($cs, ...$coords);
    }

    private function readLineString(WKBBuffer $buffer, CoordinateSystem $cs) : LineString
    {
        $numPoints = $buffer->readUnsignedLong();

        $points = [];

        for ($i = 0; $i < $numPoints; $i++) {
            $points[] = $this->readPoint($buffer, $cs);
        }

        return new LineString($cs, ...$points);
    }

    private function readCircularString(WKBBuffer $buffer, CoordinateSystem $cs) : CircularString
    {
        $numPoints = $buffer->readUnsignedLong();

        $points = [];

        for ($i = 0; $i < $numPoints; $i++) {
            $points[] = $this->readPoint($buffer, $cs);
        }

        return new CircularString($cs, ...$points);
    }

    /**
     * @throws GeometryIOException
     */
    private function readCompoundCurve(WKBBuffer $buffer, CoordinateSystem $cs) : CompoundCurve
    {
        $numCurves = $buffer->readUnsignedLong();
        $curves = [];

        for ($i = 0; $i < $numCurves; $i++) {
            $curve = $this->readGeometry($buffer, $cs->SRID());

            if (! $curve instanceof Curve) {
                throw new GeometryIOException('Expected Curve, got ' . $curve->geometryType());
            }

            $curves[] = $curve;
        }

        return new CompoundCurve($cs, ...$curves);
    }

    private function readPolygon(WKBBuffer $buffer, CoordinateSystem $cs) : Polygon
    {
        $numRings = $buffer->readUnsignedLong();

        $rings = [];

        for ($i = 0; $i < $numRings; $i++) {
            $rings[] = $this->readLineString($buffer, $cs);
        }

        return new Polygon($cs, ...$rings);
    }

    /**
     * @throws GeometryIOException
     */
    private function readCurvePolygon(WKBBuffer $buffer, CoordinateSystem $cs) : CurvePolygon
    {
        $numRings = $buffer->readUnsignedLong();

        $rings = [];

        for ($i = 0; $i < $numRings; $i++) {
            $ring = $this->readGeometry($buffer, $cs->SRID());

            if (! $ring instanceof Curve) {
                throw new GeometryIOException('Expected Curve, got ' . $ring->geometryType());
            }

            $rings[] = $ring;
        }

        return new CurvePolygon($cs, ...$rings);
    }

    /**
     * @throws GeometryIOException
     */
    private function readMultiPoint(WKBBuffer $buffer, CoordinateSystem $cs) : MultiPoint
    {
        $numPoints = $buffer->readUnsignedLong();
        $points = [];

        for ($i = 0; $i < $numPoints; $i++) {
            $point = $this->readGeometry($buffer, $cs->SRID());

            if (! $point instanceof Point) {
                throw new GeometryIOException('Expected Point, got ' . $point->geometryType());
            }

            $points[] = $point;
        }

        return new MultiPoint($cs, ...$points);
    }

    /**
     * @throws GeometryIOException
     */
    private function readMultiLineString(WKBBuffer $buffer, CoordinateSystem $cs) : MultiLineString
    {
        $numLineStrings = $buffer->readUnsignedLong();
        $lineStrings = [];

        for ($i = 0; $i < $numLineStrings; $i++) {
            $lineString = $this->readGeometry($buffer, $cs->SRID());

            if (! $lineString instanceof LineString) {
                throw new GeometryIOException('Expected LineString, got ' . $lineString->geometryType());
            }

            $lineStrings[] = $lineString;
        }

        return new MultiLineString($cs, ...$lineStrings);
    }

    /**
     * @throws GeometryIOException
     */
    private function readMultiPolygon(WKBBuffer $buffer, CoordinateSystem $cs) : MultiPolygon
    {
        $numPolygons = $buffer->readUnsignedLong();
        $polygons = [];

        for ($i = 0; $i < $numPolygons; $i++) {
            $polygon = $this->readGeometry($buffer, $cs->SRID());

            if (! $polygon instanceof Polygon) {
                throw new GeometryIOException('Expected Polygon, got ' . $polygon->geometryType());
            }

            $polygons[] = $polygon;
        }

        return new MultiPolygon($cs, ...$polygons);
    }

    private function readGeometryCollection(WKBBuffer $buffer, CoordinateSystem $cs) : GeometryCollection
    {
        $numGeometries = $buffer->readUnsignedLong();
        $geometries = [];

        for ($i = 0; $i < $numGeometries; $i++) {
            $geometries[] = $this->readGeometry($buffer, $cs->SRID());
        }

        return new GeometryCollection($cs, ...$geometries);
    }

    /**
     * @throws GeometryIOException
     */
    private function readPolyhedralSurface(WKBBuffer $buffer, CoordinateSystem $cs) : PolyhedralSurface
    {
        $numPatches = $buffer->readUnsignedLong();
        $patches = [];

        for ($i = 0; $i < $numPatches; $i++) {
            $patch = $this->readGeometry($buffer, $cs->SRID());

            if (! $patch instanceof Polygon) {
                throw new GeometryIOException('Expected Polygon, got ' . $patch->geometryType());
            }

            $patches[] = $patch;
        }

        return new PolyhedralSurface($cs, ...$patches);
    }

    /**
     * @throws GeometryIOException
     */
    private function readTIN(WKBBuffer $buffer, CoordinateSystem $cs) : TIN
    {
        $numPatches = $buffer->readUnsignedLong();
        $patches = [];

        for ($i = 0; $i < $numPatches; $i++) {
            $patch = $this->readGeometry($buffer, $cs->SRID());

            if (! $patch instanceof Polygon) {
                throw new GeometryIOException('Expected Polygon, got ' . $patch->geometryType());
            }

            $patches[] = $patch;
        }

        return new TIN($cs, ...$patches);
    }

    private function readTriangle(WKBBuffer $buffer, CoordinateSystem $cs) : Triangle
    {
        $numRings = $buffer->readUnsignedLong();

        $rings = [];

        for ($i = 0; $i < $numRings; $i++) {
            $rings[] = $this->readLineString($buffer, $cs);
        }

        return new Triangle($cs, ...$rings);
    }
}
