<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Common\Validators\NotContainingUrl;

use Attribute;
use BeyondSEODeps\DDD\Domain\Base\Entities\Attributes\BaseAttributeTrait;
use BeyondSEODeps\Symfony\Component\Validator\Constraint;

/**
 * Not containing URL constraint
 *
 * @Annotation
 */
#[Attribute]
class NotContainingUrlConstraint extends Constraint
{
    use BaseAttributeTrait;

    public string $containsUrlMessage = 'This field is not allowed to contain an URL.';

    public function __construct(?array $groups = null, mixed $payload = null)
    {
        parent::__construct([], $groups, $payload);
    }
}
