<?php

namespace BeyondSEODeps\Doctrine\DBAL\Event\Listeners;

use BeyondSEODeps\Doctrine\Common\EventSubscriber;
use BeyondSEODeps\Doctrine\DBAL\Event\ConnectionEventArgs;
use BeyondSEODeps\Doctrine\DBAL\Events;
use BeyondSEODeps\Doctrine\DBAL\Exception;

/** @deprecated Use {@see \Doctrine\DBAL\Driver\AbstractSQLiteDriver\Middleware\EnableForeignKeys} instead. */
class SQLiteSessionInit implements EventSubscriber
{
    /**
     * @return void
     *
     * @throws Exception
     */
    public function postConnect(ConnectionEventArgs $args)
    {
        $args->getConnection()->executeStatement('PRAGMA foreign_keys=ON');
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return [Events::postConnect];
    }
}
