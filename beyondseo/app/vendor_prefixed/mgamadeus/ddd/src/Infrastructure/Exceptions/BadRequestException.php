<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Infrastructure\Exceptions;

use BeyondSEODeps\DDD\Infrastructure\Validation\ValidationErrors;
use BeyondSEODeps\Symfony\Component\HttpFoundation\Response;

/**
 * BadRequest: The data provided does not match requirements
 */
class BadRequestException extends Exception
{
    protected static int $defaultCode = Response::HTTP_BAD_REQUEST;

    public ValidationErrors $validationErrors;
}