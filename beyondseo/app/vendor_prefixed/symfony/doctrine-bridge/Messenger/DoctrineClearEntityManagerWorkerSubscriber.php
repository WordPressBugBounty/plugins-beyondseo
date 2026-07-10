<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Bridge\Doctrine\Messenger;

use BeyondSEODeps\Doctrine\Persistence\ManagerRegistry;
use BeyondSEODeps\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use BeyondSEODeps\Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use BeyondSEODeps\Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;

/**
 * Clears entity managers between messages being handled to avoid outdated data.
 *
 * @author Ryan Weaver <ryan@symfonycasts.com>
 */
class DoctrineClearEntityManagerWorkerSubscriber implements EventSubscriberInterface
{
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function onWorkerMessageHandled()
    {
        $this->clearEntityManagers();
    }

    public function onWorkerMessageFailed()
    {
        $this->clearEntityManagers();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageHandledEvent::class => 'onWorkerMessageHandled',
            WorkerMessageFailedEvent::class => 'onWorkerMessageFailed',
        ];
    }

    private function clearEntityManagers()
    {
        foreach ($this->managerRegistry->getManagers() as $manager) {
            $manager->clear();
        }
    }
}
