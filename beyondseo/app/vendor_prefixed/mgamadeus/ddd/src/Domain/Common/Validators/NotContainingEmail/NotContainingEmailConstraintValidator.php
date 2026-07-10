<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Common\Validators\NotContainingEmail;

use BeyondSEODeps\Symfony\Component\Validator\Constraint;
use BeyondSEODeps\Symfony\Component\Validator\ConstraintValidator;
use BeyondSEODeps\Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NotContainingEmailConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof NotContainingEmailConstraint) {
            throw new UnexpectedTypeException($constraint, NotContainingEmailConstraint::class);
        }

        if (!isset($value)) {
            return;
        }

        if ($value && preg_match('~([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})~', $value)) {
            $this->context->buildViolation($constraint->containsEmailMessage)->addViolation();
        }
    }
}