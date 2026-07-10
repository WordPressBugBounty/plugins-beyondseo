<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator;

use BeyondSEODeps\Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use BeyondSEODeps\Symfony\Component\PropertyInfo\PropertyAccessExtractorInterface;
use BeyondSEODeps\Symfony\Component\PropertyInfo\PropertyDescriptionExtractorInterface;
use BeyondSEODeps\Symfony\Component\PropertyInfo\PropertyInfoCacheExtractor;
use BeyondSEODeps\Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use BeyondSEODeps\Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use BeyondSEODeps\Symfony\Component\PropertyInfo\PropertyInitializableExtractorInterface;
use BeyondSEODeps\Symfony\Component\PropertyInfo\PropertyListExtractorInterface;
use BeyondSEODeps\Symfony\Component\PropertyInfo\PropertyReadInfoExtractorInterface;
use BeyondSEODeps\Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use BeyondSEODeps\Symfony\Component\PropertyInfo\PropertyWriteInfoExtractorInterface;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('property_info', PropertyInfoExtractor::class)
            ->args([[], [], [], [], []])

        ->alias(PropertyAccessExtractorInterface::class, 'property_info')
        ->alias(PropertyDescriptionExtractorInterface::class, 'property_info')
        ->alias(PropertyInfoExtractorInterface::class, 'property_info')
        ->alias(PropertyTypeExtractorInterface::class, 'property_info')
        ->alias(PropertyListExtractorInterface::class, 'property_info')
        ->alias(PropertyInitializableExtractorInterface::class, 'property_info')

        ->set('property_info.cache', PropertyInfoCacheExtractor::class)
            ->decorate('property_info')
            ->args([service('property_info.cache.inner'), service('cache.property_info')])

        // Extractor
        ->set('property_info.reflection_extractor', ReflectionExtractor::class)
            ->tag('property_info.list_extractor', ['priority' => -1000])
            ->tag('property_info.type_extractor', ['priority' => -1002])
            ->tag('property_info.access_extractor', ['priority' => -1000])
            ->tag('property_info.initializable_extractor', ['priority' => -1000])

        ->alias(PropertyReadInfoExtractorInterface::class, 'property_info.reflection_extractor')
        ->alias(PropertyWriteInfoExtractorInterface::class, 'property_info.reflection_extractor')
    ;
};
