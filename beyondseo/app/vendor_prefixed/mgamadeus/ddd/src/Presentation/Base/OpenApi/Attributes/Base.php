<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes;

use Attribute;
use BeyondSEODeps\DDD\Domain\Base\Entities\Attributes\BaseAttributeTrait;
use BeyondSEODeps\DDD\Domain\Base\Entities\ValueObject;

#[Attribute(Attribute::TARGET_CLASS)]
class Base extends ValueObject
{
    use BaseAttributeTrait;
}