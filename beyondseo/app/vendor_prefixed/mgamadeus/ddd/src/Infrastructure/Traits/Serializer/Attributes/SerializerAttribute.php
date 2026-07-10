<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Infrastructure\Traits\Serializer\Attributes;

use Attribute;
use BeyondSEODeps\DDD\Domain\Base\Entities\Attributes\BaseAttributeTrait;

#[Attribute(Attribute::TARGET_PROPERTY)]
class SerializerAttribute
{
    use BaseAttributeTrait;
}