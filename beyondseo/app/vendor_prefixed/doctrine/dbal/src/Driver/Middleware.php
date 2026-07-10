<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\DBAL\Driver;

use BeyondSEODeps\Doctrine\DBAL\Driver;

interface Middleware
{
    public function wrap(Driver $driver): Driver;
}
