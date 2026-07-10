<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Presentation\Services;

use BeyondSEODeps\DDD\Infrastructure\Services\Service;
use BeyondSEODeps\Symfony\Component\HttpFoundation\RequestStack;
use BeyondSEODeps\Symfony\Component\Routing\Router;
use BeyondSEODeps\Symfony\Component\Routing\RouterInterface;

class RequestService extends Service
{
    public function __construct(protected RequestStack $requestStack, protected RouterInterface $router)
    {
    }

    /**
     * @return RequestStack
     */
    public function getRequestStack(): RequestStack
    {
        return $this->requestStack;
    }

    /**
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }
}