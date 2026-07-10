<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Bundle\SecurityBundle\RememberMe;

use Psr\Container\ContainerInterface;
use BeyondSEODeps\Symfony\Bundle\SecurityBundle\Security\FirewallAwareTrait;
use BeyondSEODeps\Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use BeyondSEODeps\Symfony\Component\HttpFoundation\RequestStack;
use BeyondSEODeps\Symfony\Component\Security\Core\User\UserInterface;
use BeyondSEODeps\Symfony\Component\Security\Http\RememberMe\RememberMeDetails;
use BeyondSEODeps\Symfony\Component\Security\Http\RememberMe\RememberMeHandlerInterface;

/**
 * Decorates {@see RememberMeHandlerInterface} for the current firewall.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
final class FirewallAwareRememberMeHandler implements RememberMeHandlerInterface
{
    use FirewallAwareTrait;

    private const FIREWALL_OPTION = 'remember_me';

    public function __construct(FirewallMap $firewallMap, ContainerInterface $rememberMeHandlerLocator, RequestStack $requestStack)
    {
        $this->firewallMap = $firewallMap;
        $this->locator = $rememberMeHandlerLocator;
        $this->requestStack = $requestStack;
    }

    public function createRememberMeCookie(UserInterface $user): void
    {
        $this->getForFirewall()->createRememberMeCookie($user);
    }

    public function consumeRememberMeCookie(RememberMeDetails $rememberMeDetails): UserInterface
    {
        return $this->getForFirewall()->consumeRememberMeCookie($rememberMeDetails);
    }

    public function clearRememberMeCookie(): void
    {
        $this->getForFirewall()->clearRememberMeCookie();
    }
}
