<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Component\Security\Http\Authentication;

use BeyondSEODeps\Symfony\Component\HttpFoundation\Request;
use BeyondSEODeps\Symfony\Component\HttpFoundation\Response;
use BeyondSEODeps\Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class CustomAuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    private $handler;

    /**
     * @param array $options Options for processing a successful authentication attempt
     */
    public function __construct(AuthenticationFailureHandlerInterface $handler, array $options)
    {
        $this->handler = $handler;
        if (method_exists($handler, 'setOptions')) {
            $this->handler->setOptions($options);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return $this->handler->onAuthenticationFailure($request, $exception);
    }
}
