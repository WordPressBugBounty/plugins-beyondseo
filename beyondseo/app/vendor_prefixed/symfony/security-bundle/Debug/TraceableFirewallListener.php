<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Bundle\SecurityBundle\Debug;

use BeyondSEODeps\Symfony\Bundle\SecurityBundle\EventListener\FirewallListener;
use BeyondSEODeps\Symfony\Bundle\SecurityBundle\Security\FirewallContext;
use BeyondSEODeps\Symfony\Bundle\SecurityBundle\Security\LazyFirewallContext;
use BeyondSEODeps\Symfony\Component\HttpKernel\Event\RequestEvent;
use BeyondSEODeps\Symfony\Component\Security\Http\Authenticator\Debug\TraceableAuthenticatorManagerListener;
use BeyondSEODeps\Symfony\Component\Security\Http\Firewall\FirewallListenerInterface;

/**
 * Firewall collecting called security listeners and authenticators.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class TraceableFirewallListener extends FirewallListener
{
    private array $wrappedListeners = [];
    private array $authenticatorsInfo = [];

    public function getWrappedListeners()
    {
        return $this->wrappedListeners;
    }

    public function getAuthenticatorsInfo(): array
    {
        return $this->authenticatorsInfo;
    }

    protected function callListeners(RequestEvent $event, iterable $listeners)
    {
        $wrappedListeners = [];
        $wrappedLazyListeners = [];
        $authenticatorManagerListener = null;

        foreach ($listeners as $listener) {
            if ($listener instanceof LazyFirewallContext) {
                \Closure::bind(function () use (&$wrappedLazyListeners, &$wrappedListeners, &$authenticatorManagerListener) {
                    $listeners = [];
                    foreach ($this->listeners as $listener) {
                        if (!$authenticatorManagerListener && $listener instanceof TraceableAuthenticatorManagerListener) {
                            $authenticatorManagerListener = $listener;
                        }
                        if ($listener instanceof FirewallListenerInterface) {
                            $listener = new WrappedLazyListener($listener);
                            $listeners[] = $listener;
                            $wrappedLazyListeners[] = $listener;
                        } else {
                            $listeners[] = function (RequestEvent $event) use ($listener, &$wrappedListeners) {
                                $wrappedListener = new WrappedListener($listener);
                                $wrappedListener($event);
                                $wrappedListeners[] = $wrappedListener->getInfo();
                            };
                        }
                    }
                    $this->listeners = $listeners;
                }, $listener, FirewallContext::class)();

                $listener($event);
            } else {
                $wrappedListener = $listener instanceof FirewallListenerInterface ? new WrappedLazyListener($listener) : new WrappedListener($listener);
                $wrappedListener($event);
                $wrappedListeners[] = $wrappedListener->getInfo();
                if (!$authenticatorManagerListener && $listener instanceof TraceableAuthenticatorManagerListener) {
                    $authenticatorManagerListener = $listener;
                }
            }

            if ($event->hasResponse()) {
                break;
            }
        }

        if ($wrappedLazyListeners) {
            foreach ($wrappedLazyListeners as $lazyListener) {
                $this->wrappedListeners[] = $lazyListener->getInfo();
            }
        }

        $this->wrappedListeners = array_merge($this->wrappedListeners, $wrappedListeners);

        if ($authenticatorManagerListener) {
            $this->authenticatorsInfo = $authenticatorManagerListener->getAuthenticatorsInfo();
        }
    }
}
