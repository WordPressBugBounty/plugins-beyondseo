<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Infrastructure\Exceptions;

use BeyondSEODeps\Symfony\Component\HttpFoundation\Response;

/**
 * Forbidden: The Account has insufficient permissions to access endpoint
 */
class ForbiddenException extends Exception
{
    protected static int $defaultCode = Response::HTTP_FORBIDDEN;
}