<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\DBAL\Driver\Mysqli;

use BeyondSEODeps\Doctrine\DBAL\Driver\Exception;
use mysqli;

interface Initializer
{
    /** @throws Exception */
    public function initialize(mysqli $connection): void;
}
