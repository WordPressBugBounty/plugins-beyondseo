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

use BeyondSEODeps\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use BeyondSEODeps\Symfony\Component\DependencyInjection\ContainerBuilder;
use BeyondSEODeps\Symfony\Component\DependencyInjection\Exception\LogicException;

/**
 * @author Christian Flothmann <christian.flothmann@sensiolabs.de>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class WorkflowGuardListenerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('workflow.has_guard_listeners')) {
            return;
        }

        $container->getParameterBag()->remove('workflow.has_guard_listeners');

        $servicesNeeded = [
            'security.token_storage',
            'security.authorization_checker',
            'security.authentication.trust_resolver',
            'security.role_hierarchy',
        ];

        foreach ($servicesNeeded as $service) {
            if (!$container->has($service)) {
                throw new LogicException(sprintf('The "%s" service is needed to be able to use the workflow guard listener.', $service));
            }
        }
    }
}
