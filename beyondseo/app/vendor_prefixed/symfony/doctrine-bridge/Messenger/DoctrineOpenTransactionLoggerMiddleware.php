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
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use BeyondSEODeps\Symfony\Component\Messenger\Envelope;
use BeyondSEODeps\Symfony\Component\Messenger\Middleware\StackInterface;

/**
 * Middleware to log when transaction has been left open.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class DoctrineOpenTransactionLoggerMiddleware extends AbstractDoctrineMiddleware
{
    private $logger;

    public function __construct(ManagerRegistry $managerRegistry, ?string $entityManagerName = null, ?LoggerInterface $logger = null)
    {
        parent::__construct($managerRegistry, $entityManagerName);

        $this->logger = $logger ?? new NullLogger();
    }

    protected function handleForManager(EntityManagerInterface $entityManager, Envelope $envelope, StackInterface $stack): Envelope
    {
        try {
            return $stack->next()->handle($envelope, $stack);
        } finally {
            if ($entityManager->getConnection()->isTransactionActive()) {
                $this->logger->error('A handler opened a transaction but did not close it.', [
                    'message' => $envelope->getMessage(),
                ]);
            }
        }
    }
}
