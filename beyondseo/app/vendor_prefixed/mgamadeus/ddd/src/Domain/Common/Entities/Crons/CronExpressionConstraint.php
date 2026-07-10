<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Common\Entities\Crons;

use Attribute;
use BeyondSEODeps\DDD\Domain\Base\Entities\Attributes\BaseAttributeTrait;
use BeyondSEODeps\Symfony\Component\Validator\Constraint;

/**
 * Cron constraint validation.
 *
 */
#[Attribute]
class CronExpressionConstraint extends Constraint
{
    use BaseAttributeTrait;

    public $message = 'The value "{{ value }}" is not a valid cron expression.';

    public function __construct(?array $groups = null, mixed $payload = null)
    {
        parent::__construct([], $groups, $payload);
    }
}
