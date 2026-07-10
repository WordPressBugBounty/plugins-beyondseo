<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Bundle\SecurityBundle\DependencyInjection\Compiler;

use BeyondSEODeps\Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use BeyondSEODeps\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use BeyondSEODeps\Symfony\Component\DependencyInjection\ContainerBuilder;
use BeyondSEODeps\Symfony\Component\DependencyInjection\Definition;
use BeyondSEODeps\Symfony\Component\DependencyInjection\Reference;
use BeyondSEODeps\Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * @author Wouter de Jong <wouter@wouterj.nl>
 *
 * @internal
 */
class RegisterLdapLocatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->setDefinition('security.ldap_locator', new Definition(ServiceLocator::class));

        $locators = [];
        foreach ($container->findTaggedServiceIds('ldap') as $serviceId => $tags) {
            $locators[$serviceId] = new ServiceClosureArgument(new Reference($serviceId));
        }

        $definition->addArgument($locators);
    }
}
