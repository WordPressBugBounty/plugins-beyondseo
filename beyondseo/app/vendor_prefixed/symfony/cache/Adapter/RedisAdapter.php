<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Component\Cache\Adapter;

use BeyondSEODeps\Symfony\Component\Cache\Marshaller\MarshallerInterface;
use BeyondSEODeps\Symfony\Component\Cache\Traits\RedisClusterProxy;
use BeyondSEODeps\Symfony\Component\Cache\Traits\RedisProxy;
use BeyondSEODeps\Symfony\Component\Cache\Traits\RedisTrait;

class RedisAdapter extends AbstractAdapter
{
    use RedisTrait;

    public function __construct(\Redis|\RedisArray|\RedisCluster|\Predis\ClientInterface|RedisProxy|RedisClusterProxy $redis, string $namespace = '', int $defaultLifetime = 0, ?MarshallerInterface $marshaller = null)
    {
        $this->init($redis, $namespace, $defaultLifetime, $marshaller);
    }
}
