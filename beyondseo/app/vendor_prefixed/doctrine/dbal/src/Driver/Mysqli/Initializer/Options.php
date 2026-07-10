<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\DBAL\Driver\Mysqli\Initializer;

use BeyondSEODeps\Doctrine\DBAL\Driver\Mysqli\Exception\InvalidOption;
use BeyondSEODeps\Doctrine\DBAL\Driver\Mysqli\Initializer;
use mysqli;

use function mysqli_options;

final class Options implements Initializer
{
    /** @var array<int,mixed> */
    private array $options;

    /** @param array<int,mixed> $options */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function initialize(mysqli $connection): void
    {
        foreach ($this->options as $option => $value) {
            if (! mysqli_options($connection, $option, $value)) {
                throw InvalidOption::fromOption($option, $value);
            }
        }
    }
}
