<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Bundle\SecurityBundle\Security;

use Psr\Container\ContainerInterface;
use BeyondSEODeps\Symfony\Component\HttpFoundation\Request;
use BeyondSEODeps\Symfony\Component\HttpFoundation\RequestStack;
use BeyondSEODeps\Symfony\Component\HttpFoundation\Response;
use BeyondSEODeps\Symfony\Component\Security\Core\User\UserInterface;
use BeyondSEODeps\Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use BeyondSEODeps\Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;

/**
 * A decorator that delegates all method calls to the authenticator
 * manager of the current firewall.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 *
 * @final
 */
class UserAuthenticator implements UserAuthenticatorInterface
{
    use FirewallAwareTrait;

    public function __construct(FirewallMap $firewallMap, ContainerInterface $userAuthenticators, RequestStack $requestStack)
    {
        $this->firewallMap = $firewallMap;
        $this->locator = $userAuthenticators;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticateUser(UserInterface $user, AuthenticatorInterface $authenticator, Request $request, array $badges = []): ?Response
    {
        return $this->getForFirewall()->authenticateUser($user, $authenticator, $request, $badges);
    }
}
