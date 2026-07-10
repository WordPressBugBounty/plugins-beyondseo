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

use BeyondSEODeps\Doctrine\ORM\EntityManagerInterface;
use BeyondSEODeps\Doctrine\Persistence\ManagerRegistry;
use BeyondSEODeps\Symfony\Component\Messenger\Envelope;
use BeyondSEODeps\Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use BeyondSEODeps\Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use BeyondSEODeps\Symfony\Component\Messenger\Middleware\StackInterface;

/**
 * @author Konstantin Myakshin <molodchick@gmail.com>
 *
 * @internal
 */
abstract class AbstractDoctrineMiddleware implements MiddlewareInterface
{
    protected $managerRegistry;
    protected $entityManagerName;

    public function __construct(ManagerRegistry $managerRegistry, ?string $entityManagerName = null)
    {
        $this->managerRegistry = $managerRegistry;
        $this->entityManagerName = $entityManagerName;
    }

    final public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        try {
            $entityManager = $this->managerRegistry->getManager($this->entityManagerName);
        } catch (\InvalidArgumentException $e) {
            throw new UnrecoverableMessageHandlingException($e->getMessage(), 0, $e);
        }

        return $this->handleForManager($entityManager, $envelope, $stack);
    }

    abstract protected function handleForManager(EntityManagerInterface $entityManager, Envelope $envelope, StackInterface $stack): Envelope;
}
