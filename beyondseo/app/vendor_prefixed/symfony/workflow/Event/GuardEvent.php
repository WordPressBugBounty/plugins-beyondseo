<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Component\Workflow\Event;

use BeyondSEODeps\Symfony\Component\Workflow\Marking;
use BeyondSEODeps\Symfony\Component\Workflow\Transition;
use BeyondSEODeps\Symfony\Component\Workflow\TransitionBlocker;
use BeyondSEODeps\Symfony\Component\Workflow\TransitionBlockerList;
use BeyondSEODeps\Symfony\Component\Workflow\WorkflowInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
final class GuardEvent extends Event
{
    private $transitionBlockerList;

    /**
     * {@inheritdoc}
     */
    public function __construct(object $subject, Marking $marking, Transition $transition, ?WorkflowInterface $workflow = null)
    {
        parent::__construct($subject, $marking, $transition, $workflow);

        $this->transitionBlockerList = new TransitionBlockerList();
    }

    public function getTransition(): Transition
    {
        return parent::getTransition();
    }

    public function isBlocked(): bool
    {
        return !$this->transitionBlockerList->isEmpty();
    }

    public function setBlocked(bool $blocked, ?string $message = null): void
    {
        if (!$blocked) {
            $this->transitionBlockerList->clear();

            return;
        }

        $this->transitionBlockerList->add(TransitionBlocker::createUnknown($message));
    }

    public function getTransitionBlockerList(): TransitionBlockerList
    {
        return $this->transitionBlockerList;
    }

    public function addTransitionBlocker(TransitionBlocker $transitionBlocker): void
    {
        $this->transitionBlockerList->add($transitionBlocker);
    }
}
