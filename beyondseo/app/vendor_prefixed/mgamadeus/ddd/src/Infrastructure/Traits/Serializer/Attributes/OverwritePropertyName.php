<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Infrastructure\Traits\Serializer\Attributes;

use Attribute;
use BeyondSEODeps\DDD\Domain\Base\Entities\Attributes\BaseAttributeTrait;

/**
 * overwrites property name on serialization
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class OverwritePropertyName extends SerializerAttribute
{
    use BaseAttributeTrait;

    public string $name = '';

    public function __construct(string $name)
    {
        $this->name = $name;
    }

}