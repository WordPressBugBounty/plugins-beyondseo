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

use BeyondSEODeps\Symfony\Component\DependencyInjection\Definition;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class InlineServiceConfigurator extends AbstractConfigurator
{
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ArgumentTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AutowireTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\BindTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\CallTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ConfiguratorTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\FactoryTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\FileTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\LazyTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ParentTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\PropertyTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\TagTrait;
    public const FACTORY = 'service';
    private string $id = '[inline]';
    private bool $allowParent = true;
    private ?string $path = null;
    public function __construct(Definition $definition)
    {
        $this->definition = $definition;
    }
}