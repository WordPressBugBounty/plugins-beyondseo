<?php

declare (strict_types=1);

namespace BeyondSEODeps\DDD\Symfony\EventListeners;

use BeyondSEODeps\DDD\Infrastructure\Exceptions\ForbiddenException;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;
use BeyondSEODeps\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use BeyondSEODeps\Symfony\Component\HttpFoundation\Request;
use BeyondSEODeps\Symfony\Component\HttpFoundation\Response;
use BeyondSEODeps\Symfony\Component\HttpKernel\Event\ExceptionEvent;
use BeyondSEODeps\Symfony\Component\HttpKernel\Event\RequestEvent;
use BeyondSEODeps\Symfony\Component\HttpKernel\Event\ResponseEvent;
use BeyondSEODeps\Symfony\Component\HttpKernel\KernelEvents;
use BeyondSEODeps\Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class CorsListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 9999],
            KernelEvents::RESPONSE => ['onKernelResponse', 9999],
            KernelEvents::EXCEPTION => ['onKernelException', 9999],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof AccessDeniedException) {
            $exception = new ForbiddenException('Insufficient permissions to access endpoint');
            $response = new RestResponseDto($exception->toJSON(), $exception->getCode(), [], true);
            $event->setResponse($response);
        }
        $response = $event->getResponse();
        if ($response) {
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'GET,POST,PUT,PATCH,DELETE,OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Authorization, Content-Type');
        }
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        // Don't do anything if it's not the master request.
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $method = $request->getRealMethod();

        if (Request::METHOD_OPTIONS === $method) {
            $response = new Response();
            $event->setResponse($response);
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        // Don't do anything if it's not the master request.
        if (!$event->isMainRequest()) {
            return;
        }

        $response = $event->getResponse();
        if ($response) {
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'GET,POST,PUT,PATCH,DELETE,OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Authorization, Content-Type');
        }
    }
}