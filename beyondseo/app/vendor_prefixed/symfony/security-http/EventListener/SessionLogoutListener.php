<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Component\Security\Http\EventListener;

use BeyondSEODeps\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use BeyondSEODeps\Symfony\Component\Security\Http\Event\LogoutEvent;

/**
 * Handler for clearing invalidating the current session.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * @final
 */
class SessionLogoutListener implements EventSubscriberInterface
{
    public function onLogout(LogoutEvent $event): void
    {
        if ($event->getRequest()->hasSession()) {
            $event->getRequest()->getSession()->invalidate();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogout',
        ];
    }
}
