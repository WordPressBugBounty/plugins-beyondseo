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
use BeyondSEODeps\Symfony\Component\Security\Csrf\TokenStorage\ClearableTokenStorageInterface;
use BeyondSEODeps\Symfony\Component\Security\Http\Event\LogoutEvent;

/**
 * @author Christian Flothmann <christian.flothmann@sensiolabs.de>
 *
 * @final
 */
class CsrfTokenClearingLogoutListener implements EventSubscriberInterface
{
    private $csrfTokenStorage;

    public function __construct(ClearableTokenStorageInterface $csrfTokenStorage)
    {
        $this->csrfTokenStorage = $csrfTokenStorage;
    }

    public function onLogout(LogoutEvent $event): void
    {
        $this->csrfTokenStorage->clear();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogout',
        ];
    }
}
