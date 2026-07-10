<?php

namespace BeyondSEODeps\Doctrine\DBAL\Event\Listeners;

use BeyondSEODeps\Doctrine\Common\EventSubscriber;
use BeyondSEODeps\Doctrine\DBAL\Event\ConnectionEventArgs;
use BeyondSEODeps\Doctrine\DBAL\Events;
use BeyondSEODeps\Doctrine\DBAL\Exception;

/**
 * Session init listener for executing a single SQL statement right after a connection is opened.
 *
 * @deprecated Implement a middleware instead.
 */
class SQLSessionInit implements EventSubscriber
{
    /** @var string */
    protected $sql;

    /** @param string $sql */
    public function __construct($sql)
    {
        $this->sql = $sql;
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function postConnect(ConnectionEventArgs $args)
    {
        $args->getConnection()->executeStatement($this->sql);
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return [Events::postConnect];
    }
}
