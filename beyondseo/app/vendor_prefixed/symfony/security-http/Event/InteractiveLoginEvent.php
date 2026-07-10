<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Component\Security\Http\Event;

use BeyondSEODeps\Symfony\Component\HttpFoundation\Request;
use BeyondSEODeps\Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use BeyondSEODeps\Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class InteractiveLoginEvent extends Event
{
    private $request;
    private $authenticationToken;

    public function __construct(Request $request, TokenInterface $authenticationToken)
    {
        $this->request = $request;
        $this->authenticationToken = $authenticationToken;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getAuthenticationToken(): TokenInterface
    {
        return $this->authenticationToken;
    }
}
