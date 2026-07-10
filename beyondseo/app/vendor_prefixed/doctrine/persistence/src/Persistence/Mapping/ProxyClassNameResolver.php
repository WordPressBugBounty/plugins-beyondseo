<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\Persistence\Mapping;

use BeyondSEODeps\Doctrine\Persistence\Proxy;

interface ProxyClassNameResolver
{
    /**
     * @phpstan-param class-string<Proxy<T>>|class-string<T> $className
     *
     * @phpstan-return class-string<T>
     *
     * @template T of object
     */
    public function resolveClassName(string $className): string;
}
