<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Infrastructure\Traits\Serializer\Attributes;

use Attribute;
use BeyondSEODeps\DDD\Domain\Base\Entities\Attributes\BaseAttributeTrait;

/**
 * when calling serialize() of class, this property will be excluded, e.g. usefull when caching an object and one property
 * is not intended to be cached
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_PROPERTY)]
class HidePropertyOnSystemSerialization extends SerializerAttribute
{
    use BaseAttributeTrait;
}