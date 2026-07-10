<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Symfony\EventListeners;

use BeyondSEODeps\DDD\Infrastructure\Exceptions\Exception;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\UnauthorizedException;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;
use BeyondSEODeps\Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use BeyondSEODeps\Symfony\Component\HttpKernel\Event\ExceptionEvent;
use BeyondSEODeps\Symfony\Component\Security\Core\Exception\AccessDeniedException;
use BeyondSEODeps\Symfony\Component\Security\Core\Exception\AuthenticationException;

#[AsEventListener(event: 'kernel.exception', method: 'onKernelException')]
class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        // Custom Exceptions are returned as JSON
        $exception = $event->getThrowable();
        if ($exception instanceof Exception) {
            $response = new RestResponseDto($exception->toJSON(), $exception->getCode(), [], true);
            $event->setResponse($response);
            return;
        }
        if (($exception->getPrevious() instanceof AuthenticationException || $exception instanceof AccessDeniedException)
            && str_starts_with(
                $event->getRequest()->getRequestUri(),
                '/api'
            )) {
            $exception = new UnauthorizedException('Unauthorized');
            $response = new RestResponseDto($exception->toJSON(), $exception->getCode(), [], true);
            $event->setResponse($response);
            return;
        }
    }
}