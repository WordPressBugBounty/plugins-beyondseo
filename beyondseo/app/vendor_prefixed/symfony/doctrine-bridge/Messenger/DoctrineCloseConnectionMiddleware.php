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
use BeyondSEODeps\Symfony\Component\Messenger\Envelope;
use BeyondSEODeps\Symfony\Component\Messenger\Middleware\StackInterface;
use BeyondSEODeps\Symfony\Component\Messenger\Stamp\ConsumedByWorkerStamp;

/**
 * Closes connection and therefore saves number of connections.
 *
 * @author Fuong <insidestyles@gmail.com>
 */
class DoctrineCloseConnectionMiddleware extends AbstractDoctrineMiddleware
{
    protected function handleForManager(EntityManagerInterface $entityManager, Envelope $envelope, StackInterface $stack): Envelope
    {
        try {
            $connection = $entityManager->getConnection();

            return $stack->next()->handle($envelope, $stack);
        } finally {
            if (null !== $envelope->last(ConsumedByWorkerStamp::class)) {
                $connection->close();
            }
        }
    }
}
