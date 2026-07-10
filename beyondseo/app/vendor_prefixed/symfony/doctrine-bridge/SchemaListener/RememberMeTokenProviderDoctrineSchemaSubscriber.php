<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Bridge\Doctrine\SchemaListener;

use BeyondSEODeps\Doctrine\Common\EventSubscriber;
use BeyondSEODeps\Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use BeyondSEODeps\Doctrine\ORM\Tools\ToolEvents;
use BeyondSEODeps\Symfony\Bridge\Doctrine\Security\RememberMe\DoctrineTokenProvider;
use BeyondSEODeps\Symfony\Component\Security\Http\RememberMe\PersistentRememberMeHandler;
use BeyondSEODeps\Symfony\Component\Security\Http\RememberMe\RememberMeHandlerInterface;

/**
 * Automatically adds the rememberme table needed for the {@see DoctrineTokenProvider}.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
final class RememberMeTokenProviderDoctrineSchemaSubscriber implements EventSubscriber
{
    private iterable $rememberMeHandlers;

    /**
     * @param iterable<mixed, RememberMeHandlerInterface> $rememberMeHandlers
     */
    public function __construct(iterable $rememberMeHandlers)
    {
        $this->rememberMeHandlers = $rememberMeHandlers;
    }

    public function postGenerateSchema(GenerateSchemaEventArgs $event): void
    {
        $dbalConnection = $event->getEntityManager()->getConnection();

        foreach ($this->rememberMeHandlers as $rememberMeHandler) {
            if (
                $rememberMeHandler instanceof PersistentRememberMeHandler
                && ($tokenProvider = $rememberMeHandler->getTokenProvider()) instanceof DoctrineTokenProvider
            ) {
                $tokenProvider->configureSchema($event->getSchema(), $dbalConnection);
            }
        }
    }

    public function getSubscribedEvents(): array
    {
        if (!class_exists(ToolEvents::class)) {
            return [];
        }

        return [
            ToolEvents::postGenerateSchema,
        ];
    }
}
