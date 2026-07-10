<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Component\Workflow;

use BeyondSEODeps\Symfony\Component\Workflow\Event\AnnounceEvent;
use BeyondSEODeps\Symfony\Component\Workflow\Event\CompletedEvent;
use BeyondSEODeps\Symfony\Component\Workflow\Event\EnteredEvent;
use BeyondSEODeps\Symfony\Component\Workflow\Event\EnterEvent;
use BeyondSEODeps\Symfony\Component\Workflow\Event\GuardEvent;
use BeyondSEODeps\Symfony\Component\Workflow\Event\LeaveEvent;
use BeyondSEODeps\Symfony\Component\Workflow\Event\TransitionEvent;

/**
 * To learn more about how workflow events work, check the documentation
 * entry at {@link https://symfony.com/doc/current/workflow/usage.html#using-events}.
 */
final class WorkflowEvents
{
    /**
     * @Event("BeyondSEODeps\Symfony\Component\Workflow\Event\GuardEvent")
     */
    public const GUARD = 'workflow.guard';

    /**
     * @Event("BeyondSEODeps\Symfony\Component\Workflow\Event\LeaveEvent")
     */
    public const LEAVE = 'workflow.leave';

    /**
     * @Event("BeyondSEODeps\Symfony\Component\Workflow\Event\TransitionEvent")
     */
    public const TRANSITION = 'workflow.transition';

    /**
     * @Event("BeyondSEODeps\Symfony\Component\Workflow\Event\EnterEvent")
     */
    public const ENTER = 'workflow.enter';

    /**
     * @Event("BeyondSEODeps\Symfony\Component\Workflow\Event\EnteredEvent")
     */
    public const ENTERED = 'workflow.entered';

    /**
     * @Event("BeyondSEODeps\Symfony\Component\Workflow\Event\CompletedEvent")
     */
    public const COMPLETED = 'workflow.completed';

    /**
     * @Event("BeyondSEODeps\Symfony\Component\Workflow\Event\AnnounceEvent")
     */
    public const ANNOUNCE = 'workflow.announce';

    /**
     * Event aliases.
     *
     * These aliases can be consumed by RegisterListenersPass.
     */
    public const ALIASES = [
        GuardEvent::class => self::GUARD,
        LeaveEvent::class => self::LEAVE,
        TransitionEvent::class => self::TRANSITION,
        EnterEvent::class => self::ENTER,
        EnteredEvent::class => self::ENTERED,
        CompletedEvent::class => self::COMPLETED,
        AnnounceEvent::class => self::ANNOUNCE,
    ];

    private function __construct()
    {
    }
}
