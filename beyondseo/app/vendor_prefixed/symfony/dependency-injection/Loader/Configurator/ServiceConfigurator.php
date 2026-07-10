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

use BeyondSEODeps\Symfony\Component\DependencyInjection\ContainerBuilder;
use BeyondSEODeps\Symfony\Component\DependencyInjection\Definition;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ServiceConfigurator extends AbstractServiceConfigurator
{
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AbstractTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ArgumentTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AutoconfigureTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AutowireTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\BindTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\CallTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ClassTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ConfiguratorTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\DecorateTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\DeprecateTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\FactoryTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\FileTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\LazyTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ParentTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\PropertyTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\PublicTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ShareTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\SyntheticTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\TagTrait;
    public const FACTORY = 'services';
    private $container;
    private array $instanceof;
    private bool $allowParent;
    private ?string $path;
    private bool $destructed = false;
    public function __construct(ContainerBuilder $container, array $instanceof, bool $allowParent, ServicesConfigurator $parent, Definition $definition, ?string $id, array $defaultTags, ?string $path = null)
    {
        $this->container = $container;
        $this->instanceof = $instanceof;
        $this->allowParent = $allowParent;
        $this->path = $path;
        parent::__construct($parent, $definition, $id, $defaultTags);
    }
    public function __destruct()
    {
        if ($this->destructed) {
            return;
        }
        $this->destructed = true;
        parent::__destruct();
        $this->container->removeBindings($this->id);
        $this->container->setDefinition($this->id, $this->definition->setInstanceofConditionals($this->instanceof));
    }
}
