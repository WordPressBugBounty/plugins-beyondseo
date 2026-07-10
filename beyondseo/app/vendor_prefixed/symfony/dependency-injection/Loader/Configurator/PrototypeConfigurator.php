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
use BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class PrototypeConfigurator extends AbstractServiceConfigurator
{
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AbstractTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ArgumentTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AutoconfigureTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AutowireTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\BindTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\CallTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ConfiguratorTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\DeprecateTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\FactoryTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\LazyTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ParentTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\PropertyTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\PublicTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ShareTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\TagTrait;
    public const FACTORY = 'load';
    private $loader;
    private string $resource;
    private ?array $excludes = null;
    private bool $allowParent;
    public function __construct(ServicesConfigurator $parent, PhpFileLoader $loader, Definition $defaults, string $namespace, string $resource, bool $allowParent)
    {
        $definition = new Definition();
        if (!$defaults->isPublic() || !$defaults->isPrivate()) {
            $definition->setPublic($defaults->isPublic());
        }
        $definition->setAutowired($defaults->isAutowired());
        $definition->setAutoconfigured($defaults->isAutoconfigured());
        // deep clone, to avoid multiple process of the same instance in the passes
        $definition->setBindings(unserialize(serialize($defaults->getBindings())));
        $definition->setChanges([]);
        $this->loader = $loader;
        $this->resource = $resource;
        $this->allowParent = $allowParent;
        parent::__construct($parent, $definition, $namespace, $defaults->getTags());
    }
    public function __destruct()
    {
        parent::__destruct();
        if (isset($this->loader)) {
            $this->loader->registerClasses($this->definition, $this->id, $this->resource, $this->excludes);
        }
        unset($this->loader);
    }
    /**
     * Excludes files from registration using glob patterns.
     *
     * @param string[]|string $excludes
     *
     * @return $this
     */
    final public function exclude(array|string $excludes): static
    {
        $this->excludes = (array) $excludes;
        return $this;
    }
}