<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Infrastructure\Exceptions;

use BeyondSEODeps\Symfony\Component\HttpFoundation\Response;

/**
 * NotFound: The item does either not exist or current Account has no access to it
 */
class NotFoundException extends Exception
{
    protected static int $defaultCode = Response::HTTP_NOT_FOUND;
}