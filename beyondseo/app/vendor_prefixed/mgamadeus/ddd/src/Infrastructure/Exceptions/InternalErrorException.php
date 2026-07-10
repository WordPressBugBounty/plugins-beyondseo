<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Infrastructure\Exceptions;

use BeyondSEODeps\Symfony\Component\HttpFoundation\Response;

/**
 * InternalError: An internal error has occured
 */
class InternalErrorException extends Exception
{
    protected static int $defaultCode = Response::HTTP_INTERNAL_SERVER_ERROR;
}