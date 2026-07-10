<?php

declare(strict_types=1);

namespace BeyondSEODeps\Brick\Geo\Proxy;

use BeyondSEODeps\Brick\Geo\Exception\GeometryIOException;
use BeyondSEODeps\Brick\Geo\Exception\CoordinateSystemException;
use BeyondSEODeps\Brick\Geo\Exception\InvalidGeometryException;
use BeyondSEODeps\Brick\Geo\Exception\UnexpectedGeometryException;
use BeyondSEODeps\Brick\Geo\Geometry;
use BeyondSEODeps\Brick\Geo\TIN;

/**
 * Proxy class for TIN.
 *
 * @internal This class is not part of the public API and can change at any time.
 *           Please type-hint against Brick\Geo\TIN in your projects.
 */
class TINProxy extends TIN implements ProxyInterface
{
    /**
     * The WKT or WKB data.
     */
    private readonly string $proxyData;

    /**
     * `true` if WKB, `false` if WKT.
     */
    private readonly bool $isProxyBinary;

    /**
     * The SRID of the underlying geometry.
     */
    private readonly int $proxySRID;

    /**
     * The underlying geometry, or NULL if not yet loaded.
     */
    private ?TIN $proxyGeometry = null;

    /**
     * @param string $data     The WKT or WKB data.
     * @param bool   $isBinary Whether the data is binary (true) or text (false).
     * @param int    $srid     The SRID of the geometry.
     */
    public function __construct(string $data, bool $isBinary, int $srid = 0)
    {
        $this->proxyData     = $data;
        $this->isProxyBinary = $isBinary;
        $this->proxySRID     = $srid;
    }

    /**
     * Loads the underlying geometry.
     *
     * @throws GeometryIOException         If the proxy data is not valid.
     * @throws CoordinateSystemException   If the resulting geometry contains mixed coordinate systems.
     * @throws InvalidGeometryException    If the resulting geometry is not valid.
     * @throws UnexpectedGeometryException If the resulting geometry is not an instance of the proxied class.
     */
    private function load() : void
    {
        $this->proxyGeometry = $this->isProxyBinary
            ? TIN::fromBinary($this->proxyData, $this->proxySRID)
            : TIN::fromText($this->proxyData, $this->proxySRID);
    }

    public function isLoaded() : bool
    {
        return $this->proxyGeometry !== null;
    }

    public function getGeometry() : Geometry
    {
        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry;
    }

    public function isProxyBinary() : bool
    {
        return $this->isProxyBinary;
    }

    public static function fromText(string $wkt, int $srid = 0) : Geometry
    {
        return new self($wkt, false, $srid);
    }

    public static function fromBinary(string $wkb, int $srid = 0) : Geometry
    {
        return new self($wkb, true, $srid);
    }

    public function SRID() : int
    {
        return $this->proxySRID;
    }

    public function asText() : string
    {
        if (! $this->isProxyBinary) {
            return $this->proxyData;
        }

        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry->asText();
    }

    public function asBinary() : string
    {
        if ($this->isProxyBinary) {
            return $this->proxyData;
        }

        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry->asBinary();
    }


    public function project(\BeyondSEODeps\Brick\Geo\Projector\Projector $projector): \BeyondSEODeps\Brick\Geo\TIN
    {
        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry->project($projector);
    }

    public function numPatches(): int
    {
        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry->numPatches();
    }

    public function patchN(int $n): \BeyondSEODeps\Brick\Geo\Polygon
    {
        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry->patchN($n);
    }

    public function patches(): array
    {
        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry->patches();
    }

    public function getBoundingBox(): \BeyondSEODeps\Brick\Geo\BoundingBox
    {
        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry->getBoundingBox();
    }

    public function toArray(): array
    {
        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry->toArray();
    }

    public function count(): int
    {
        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry->count();
    }

    public function getIterator(): \ArrayIterator
    {
        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry->getIterator();
    }

    public function withAddedPatches(\BeyondSEODeps\Brick\Geo\Polygon ...$patches): \BeyondSEODeps\Brick\Geo\PolyhedralSurface
    {
        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry->withAddedPatches($patches);
    }

    public function coordinateDimension(): int
    {
        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry->coordinateDimension();
    }

    public function spatialDimension(): int
    {
        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry->spatialDimension();
    }

    public function isEmpty(): bool
    {
        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry->isEmpty();
    }

    public function is3D(): bool
    {
        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry->is3D();
    }

    public function isMeasured(): bool
    {
        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry->isMeasured();
    }

    public function coordinateSystem(): \BeyondSEODeps\Brick\Geo\CoordinateSystem
    {
        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry->coordinateSystem();
    }

    public function withSRID(int $srid): \BeyondSEODeps\Brick\Geo\Geometry
    {
        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry->withSRID($srid);
    }

    public function toXY(): \BeyondSEODeps\Brick\Geo\Geometry
    {
        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry->toXY();
    }

    public function withoutZ(): \BeyondSEODeps\Brick\Geo\Geometry
    {
        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry->withoutZ();
    }

    public function withoutM(): \BeyondSEODeps\Brick\Geo\Geometry
    {
        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry->withoutM();
    }

    public function swapXY(): \BeyondSEODeps\Brick\Geo\Geometry
    {
        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry->swapXY();
    }

    public function isIdenticalTo(\BeyondSEODeps\Brick\Geo\Geometry $that): bool
    {
        if ($this->proxyGeometry === null) {
            $this->load();
        }

        return $this->proxyGeometry->isIdenticalTo($that);
    }

}
