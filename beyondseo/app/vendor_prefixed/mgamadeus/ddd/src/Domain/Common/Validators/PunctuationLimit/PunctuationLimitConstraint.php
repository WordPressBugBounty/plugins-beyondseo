<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Common\Validators\PunctuationLimit;

use Attribute;
use BeyondSEODeps\DDD\Domain\Base\Entities\Attributes\BaseAttributeTrait;
use BeyondSEODeps\Symfony\Component\Validator\Constraint;

/**
 * Punctuation limit constraint
 *
 * @Annotation
 */
#[Attribute]
class PunctuationLimitConstraint extends Constraint
{
    use BaseAttributeTrait;

    public string $tooManyPunctuationsMessage;
    public int $maxPunctuations = 0;

    public function __construct(int $maxPunctuations, ?array $groups = null, mixed $payload = null)
    {
        $this->maxPunctuations = $maxPunctuations;
        $this->tooManyPunctuationsMessage = 'This field contains too many punctuations. The maximum allowed is ' . $maxPunctuations . '.';
        parent::__construct([], $groups, $payload);
    }
}
