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
use BeyondSEODeps\Symfony\Component\HttpFoundation\Response;
use BeyondSEODeps\Symfony\Component\Security\Core\Exception\AuthenticationException;
use BeyondSEODeps\Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use BeyondSEODeps\Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use BeyondSEODeps\Symfony\Contracts\EventDispatcher\Event;

/**
 * This event is dispatched after an error during authentication.
 *
 * Listeners to this event can change state based on authentication
 * failure (e.g. to implement login throttling).
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class LoginFailureEvent extends Event
{
    private $exception;
    private $authenticator;
    private $request;
    private $response;
    private string $firewallName;
    private $passport;

    public function __construct(AuthenticationException $exception, AuthenticatorInterface $authenticator, Request $request, ?Response $response, string $firewallName, ?Passport $passport = null)
    {
        $this->exception = $exception;
        $this->authenticator = $authenticator;
        $this->request = $request;
        $this->response = $response;
        $this->firewallName = $firewallName;
        $this->passport = $passport;
    }

    public function getException(): AuthenticationException
    {
        return $this->exception;
    }

    public function getAuthenticator(): AuthenticatorInterface
    {
        return $this->authenticator;
    }

    public function getFirewallName(): string
    {
        return $this->firewallName;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function setResponse(?Response $response)
    {
        $this->response = $response;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function getPassport(): ?Passport
    {
        return $this->passport;
    }
}
