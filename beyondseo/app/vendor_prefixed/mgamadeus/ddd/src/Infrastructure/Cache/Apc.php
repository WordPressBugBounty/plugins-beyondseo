<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Infrastructure\Cache;

use BeyondSEODeps\Symfony\Component\Cache\Adapter\ApcuAdapter;

class Apc extends Cache
{
    private ApcuAdapter $adapter;

    public function getCacheAdapter(): ApcuAdapter
    {
        if (!isset($this->adapter)) {
            $this->adapter = new ApcuAdapter(
                $this->config['namespace'] ?? '',
                $this->ttl,
                null
            );
        }
        return $this->adapter;
    }
}