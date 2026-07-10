<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Presentation\Base\OpenApi\Exceptions;

use BeyondSEODeps\DDD\Infrastructure\Exceptions\Exception;
use BeyondSEODeps\Symfony\Component\HttpFoundation\Response;

class TypeDefinitionMissingOrWrong extends Exception
{
    protected static int $defaultCode = Response::HTTP_INTERNAL_SERVER_ERROR;
}