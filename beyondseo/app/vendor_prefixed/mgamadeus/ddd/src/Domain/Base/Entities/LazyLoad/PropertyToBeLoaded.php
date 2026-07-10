<?php

declare (strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad;

use BeyondSEODeps\DDD\Domain\Base\Entities\ValueObject;

class PropertyToBeLoaded extends ValueObject
{
    public function __construct(public string $className, public string $propertyName)
    {
    }

}