<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler;

use BeyondSEODeps\Symfony\Component\Config\ConfigCache;
use BeyondSEODeps\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use BeyondSEODeps\Symfony\Component\DependencyInjection\ContainerBuilder;
use BeyondSEODeps\Symfony\Component\DependencyInjection\Dumper\XmlDumper;

/**
 * Dumps the ContainerBuilder to a cache file so that it can be used by
 * debugging tools such as the debug:container console command.
 *
 * @author Ryan Weaver <ryan@thatsquality.com>
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ContainerBuilderDebugDumpPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $cache = new ConfigCache($container->getParameter('debug.container.dump'), true);
        if (!$cache->isFresh()) {
            $cache->write((new XmlDumper($container))->dump(), $container->getResources());
        }
    }
}
