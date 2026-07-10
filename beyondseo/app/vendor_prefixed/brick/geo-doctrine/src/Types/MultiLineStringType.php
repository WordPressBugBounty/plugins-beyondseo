<?php

declare(strict_types=1);

namespace BeyondSEODeps\Brick\Geo\Doctrine\Types;

use BeyondSEODeps\Brick\Geo\Proxy\MultiLineStringProxy;

/**
 * Doctrine type for MultiLineString.
 */
class MultiLineStringType extends GeometryType
{
    public function getName() : string
    {
        return 'MultiLineString';
    }

    protected function getProxyClassName() : string
    {
        return MultiLineStringProxy::class;
    }

    protected function hasKnownSubclasses() : bool
    {
        return false;
    }
}
