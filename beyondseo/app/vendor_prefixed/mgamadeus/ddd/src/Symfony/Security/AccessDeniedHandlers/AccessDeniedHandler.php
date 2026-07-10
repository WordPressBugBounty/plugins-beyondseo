<?php

declare(strict_types=1);
// src/Security/AccessDeniedHandler.php
namespace BeyondSEODeps\DDD\Symfony\Security\AccessDeniedHandlers;

use BeyondSEODeps\DDD\Infrastructure\Exceptions\UnauthorizedException;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;
use BeyondSEODeps\Symfony\Component\HttpFoundation\Request;
use BeyondSEODeps\Symfony\Component\HttpFoundation\Response;
use BeyondSEODeps\Symfony\Component\Security\Core\Exception\AccessDeniedException;
use BeyondSEODeps\Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        $exception = new UnauthorizedException('Unauthorized');
        return new RestResponseDto($exception->toJSON(), $exception->getCode(), [], true);
    }
}