<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Bundle\SecurityBundle\EventListener;

use BeyondSEODeps\Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use BeyondSEODeps\Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use BeyondSEODeps\Symfony\Component\HttpKernel\Event\RequestEvent;
use BeyondSEODeps\Symfony\Component\HttpKernel\KernelEvents;
use BeyondSEODeps\Symfony\Component\Security\Http\Firewall;
use BeyondSEODeps\Symfony\Component\Security\Http\FirewallMapInterface;
use BeyondSEODeps\Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;
use BeyondSEODeps\Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 */
class FirewallListener extends Firewall
{
    private $map;
    private $logoutUrlGenerator;

    public function __construct(FirewallMapInterface $map, EventDispatcherInterface $dispatcher, LogoutUrlGenerator $logoutUrlGenerator)
    {
        $this->map = $map;
        $this->logoutUrlGenerator = $logoutUrlGenerator;

        parent::__construct($map, $dispatcher);
    }

    public function configureLogoutUrlGenerator(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if ($this->map instanceof FirewallMap && $config = $this->map->getFirewallConfig($event->getRequest())) {
            $this->logoutUrlGenerator->setCurrentFirewall($config->getName(), $config->getContext());
        }
    }

    public function onKernelFinishRequest(FinishRequestEvent $event)
    {
        if ($event->isMainRequest()) {
            $this->logoutUrlGenerator->setCurrentFirewall(null);
        }

        parent::onKernelFinishRequest($event);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['configureLogoutUrlGenerator', 8],
                ['onKernelRequest', 8],
            ],
            KernelEvents::FINISH_REQUEST => 'onKernelFinishRequest',
        ];
    }
}
