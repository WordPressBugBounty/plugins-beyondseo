<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes;

use Attribute;
use BeyondSEODeps\DDD\Domain\Base\Entities\Attributes\BaseAttributeTrait;

/**
 * If atached, action or entire controller is ignored
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class Ignore extends Base
{
    use BaseAttributeTrait;
}