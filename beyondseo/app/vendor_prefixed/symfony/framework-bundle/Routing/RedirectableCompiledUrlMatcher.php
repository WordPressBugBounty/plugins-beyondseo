<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Bundle\FrameworkBundle\Routing;

use BeyondSEODeps\Symfony\Component\Routing\Matcher\CompiledUrlMatcher;
use BeyondSEODeps\Symfony\Component\Routing\Matcher\RedirectableUrlMatcherInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @internal
 */
class RedirectableCompiledUrlMatcher extends CompiledUrlMatcher implements RedirectableUrlMatcherInterface
{
    /**
     * {@inheritdoc}
     */
    public function redirect(string $path, string $route, ?string $scheme = null): array
    {
        return [
            '_controller' => 'BeyondSEODeps\\Symfony\\Bundle\\FrameworkBundle\\Controller\\RedirectController::urlRedirectAction',
            'path' => $path,
            'permanent' => true,
            'scheme' => $scheme,
            'httpPort' => $this->context->getHttpPort(),
            'httpsPort' => $this->context->getHttpsPort(),
            '_route' => $route,
        ];
    }
}
