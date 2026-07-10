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
class InstanceofConfigurator extends AbstractServiceConfigurator
{
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AutowireTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\BindTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\CallTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ConfiguratorTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\LazyTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\PropertyTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\PublicTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ShareTrait;
    use \BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator\Traits\TagTrait;
    public const FACTORY = 'instanceof';
    private ?string $path;
    public function __construct(ServicesConfigurator $parent, Definition $definition, string $id, ?string $path = null)
    {
        parent::__construct($parent, $definition, $id, []);
        $this->path = $path;
    }
    /**
     * Defines an instanceof-conditional to be applied to following service definitions.
     */
    final public function instanceof(string $fqcn): self
    {
        return $this->parent->instanceof($fqcn);
    }
}
