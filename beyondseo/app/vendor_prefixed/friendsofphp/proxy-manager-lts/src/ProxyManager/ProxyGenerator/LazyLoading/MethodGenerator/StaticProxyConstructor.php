<?php

declare(strict_types=1);

namespace BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoading\MethodGenerator;

use BeyondSEODeps\Laminas\Code\Generator\Exception\InvalidArgumentException;
use BeyondSEODeps\Laminas\Code\Generator\ParameterGenerator;
use BeyondSEODeps\Laminas\Code\Generator\PropertyGenerator;
use BeyondSEODeps\ProxyManager\Generator\MethodGenerator;
use BeyondSEODeps\ProxyManager\ProxyGenerator\Util\Properties;
use BeyondSEODeps\ProxyManager\ProxyGenerator\Util\UnsetPropertiesGenerator;

/**
 * The `staticProxyConstructor` implementation for lazy loading proxies
 */
class StaticProxyConstructor extends MethodGenerator
{
    /**
     * Static constructor
     *
     * @throws InvalidArgumentException
     */
    public function __construct(PropertyGenerator $initializerProperty, Properties $properties)
    {
        parent::__construct('staticProxyConstructor', [], self::FLAG_PUBLIC | self::FLAG_STATIC);

        $this->setParameter(new ParameterGenerator('initializer'));

        $this->setDocBlock("Constructor for lazy initialization\n\n@param \\Closure|null \$initializer");
        $this->setBody(
            'static $reflection;' . "\n\n"
            . '$reflection = $reflection ?? new \ReflectionClass(__CLASS__);' . "\n"
            . '$instance   = $reflection->newInstanceWithoutConstructor();' . "\n\n"
            . UnsetPropertiesGenerator::generateSnippet($properties, 'instance')
            . '$instance->' . $initializerProperty->getName() . ' = $initializer;' . "\n\n"
            . 'return $instance;'
        );
    }
}
