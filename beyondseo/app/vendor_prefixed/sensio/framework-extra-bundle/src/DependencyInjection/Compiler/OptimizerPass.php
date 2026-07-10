<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Sensio\Bundle\FrameworkExtraBundle\DependencyInjection\Compiler;

use BeyondSEODeps\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use BeyondSEODeps\Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Optimizes the container by removing unneeded listeners.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class OptimizerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('security.token_storage')) {
            $container->removeDefinition('sensio_framework_extra.security.listener');
        }

        if (!$container->hasDefinition('twig')) {
            $container->removeDefinition('sensio_framework_extra.view.listener');
        }
    }
}
