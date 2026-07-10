<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Infrastructure\Validation;

use BeyondSEODeps\DDD\Domain\Base\Entities\ValueObject;

class CustomValidationInput extends ValueObject
{
    /** @var string The sent value that resulted in an error */
    public string $value;

    /** @var string The error message received in the error */
    public string $errorMessage;

    public function __construct(string|int $value, string $errorMessage)
    {
        $this->errorMessage = $errorMessage;
        $this->value = (string)$value;

        parent::__construct();
    }
}