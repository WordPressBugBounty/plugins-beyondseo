<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Presentation\Base\Controller;

use BeyondSEODeps\Symfony\Bundle\FrameworkBundle\Routing\Router;
use BeyondSEODeps\Symfony\Component\Routing\RouteCollection;

class DocumentationController extends HttpController
{
    protected function getRouteCollection():RouteCollection {
        /** @var Router $router */
        $router = $this->container->get('router');
        $routeCollection = $router->getRouteCollection();
        return $routeCollection;
    }
}
