<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Component\PropertyInfo\DependencyInjection;

use BeyondSEODeps\Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use BeyondSEODeps\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use BeyondSEODeps\Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use BeyondSEODeps\Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Adds extractors to the property_info.constructor_extractor service.
 *
 * @author Dmitrii Poddubnyi <dpoddubny@gmail.com>
 */
final class PropertyInfoConstructorPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('property_info.constructor_extractor')) {
            return;
        }
        $definition = $container->getDefinition('property_info.constructor_extractor');

        $listExtractors = $this->findAndSortTaggedServices('property_info.constructor_extractor', $container);
        $definition->replaceArgument(0, new IteratorArgument($listExtractors));
    }
}
