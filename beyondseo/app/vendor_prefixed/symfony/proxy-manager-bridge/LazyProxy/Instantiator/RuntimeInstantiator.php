<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Bridge\ProxyManager\LazyProxy\Instantiator;

use BeyondSEODeps\ProxyManager\Configuration;
use BeyondSEODeps\ProxyManager\GeneratorStrategy\EvaluatingGeneratorStrategy;
use BeyondSEODeps\ProxyManager\Proxy\LazyLoadingInterface;
use BeyondSEODeps\Symfony\Component\DependencyInjection\ContainerInterface;
use BeyondSEODeps\Symfony\Component\DependencyInjection\Definition;
use BeyondSEODeps\Symfony\Component\DependencyInjection\LazyProxy\Instantiator\InstantiatorInterface;

/**
 * Runtime lazy loading proxy generator.
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class RuntimeInstantiator implements InstantiatorInterface
{
    private $factory;

    public function __construct()
    {
        $config = new Configuration();
        $config->setGeneratorStrategy(new EvaluatingGeneratorStrategy());

        $this->factory = new LazyLoadingValueHolderFactory($config);
    }

    /**
     * {@inheritdoc}
     */
    public function instantiateProxy(ContainerInterface $container, Definition $definition, string $id, callable $realInstantiator): object
    {
        return $this->factory->createProxy(
            $this->factory->getGenerator()->getProxifiedClass($definition),
            function (&$wrappedInstance, LazyLoadingInterface $proxy) use ($realInstantiator) {
                $wrappedInstance = $realInstantiator();

                $proxy->setProxyInitializer(null);

                return true;
            },
            [
                'fluentSafe' => $definition->hasTag('proxy'),
                'skipDestructor' => true,
            ]
        );
    }
}
