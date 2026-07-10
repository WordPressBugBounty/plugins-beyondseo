<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Presentation\Base\Controller\Filters;

use Attribute;
use BeyondSEODeps\DDD\Domain\Base\Entities\Attributes\BaseAttributeTrait;

#[Attribute]
class Before
{
    use BaseAttributeTrait;
}