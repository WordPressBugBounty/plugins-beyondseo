<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Tools\Console;

use BeyondSEODeps\Doctrine\ORM\EntityManagerInterface;

interface EntityManagerProvider
{
    public function getDefaultManager(): EntityManagerInterface;

    public function getManager(string $name): EntityManagerInterface;
}
