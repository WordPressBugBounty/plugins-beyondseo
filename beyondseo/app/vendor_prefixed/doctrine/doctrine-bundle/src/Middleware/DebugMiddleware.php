<?php

namespace BeyondSEODeps\Doctrine\Bundle\DoctrineBundle\Middleware;

use BeyondSEODeps\Doctrine\DBAL\Driver as DriverInterface;
use BeyondSEODeps\Doctrine\DBAL\Driver\Middleware;
use BeyondSEODeps\Symfony\Bridge\Doctrine\Middleware\Debug\DebugDataHolder;
use BeyondSEODeps\Symfony\Bridge\Doctrine\Middleware\Debug\Driver;
use Symfony\Component\Stopwatch\Stopwatch;

class DebugMiddleware implements Middleware, ConnectionNameAwareInterface
{
    private DebugDataHolder $debugDataHolder;
    private ?Stopwatch $stopwatch;
    private string $connectionName = 'default';

    public function __construct(DebugDataHolder $debugDataHolder, ?Stopwatch $stopwatch)
    {
        $this->debugDataHolder = $debugDataHolder;
        $this->stopwatch       = $stopwatch;
    }

    public function setConnectionName(string $name): void
    {
        $this->connectionName = $name;
    }

    public function wrap(DriverInterface $driver): DriverInterface
    {
        /** @psalm-suppress InternalClass,InternalMethod */
        return new Driver($driver, $this->debugDataHolder, $this->stopwatch, $this->connectionName);
    }
}
