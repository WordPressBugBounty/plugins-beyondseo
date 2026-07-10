<?php

declare(strict_types=1);

namespace BeyondSEODeps\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator;

use InvalidArgumentException;
use BeyondSEODeps\Laminas\Code\Generator\ParameterGenerator;
use BeyondSEODeps\Laminas\Code\Generator\PropertyGenerator;
use BeyondSEODeps\ProxyManager\Generator\MagicMethodGenerator;
use BeyondSEODeps\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\Util\InterceptorGenerator;
use BeyondSEODeps\ProxyManager\ProxyGenerator\PropertyGenerator\PublicPropertiesMap;
use BeyondSEODeps\ProxyManager\ProxyGenerator\Util\GetMethodIfExists;
use BeyondSEODeps\ProxyManager\ProxyGenerator\Util\PublicScopeSimulator;
use ReflectionClass;

/**
 * Magic `__unset` for method interceptor value holder objects
 */
class MagicUnset extends MagicMethodGenerator
{
    /**
     * Constructor
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        ReflectionClass $originalClass,
        PropertyGenerator $valueHolder,
        PropertyGenerator $prefixInterceptors,
        PropertyGenerator $suffixInterceptors,
        PublicPropertiesMap $publicProperties
    ) {
        parent::__construct($originalClass, '__unset', [new ParameterGenerator('name')]);

        $parent          = GetMethodIfExists::get($originalClass, '__unset');
        $valueHolderName = $valueHolder->getName();

        $callParent = PublicScopeSimulator::getPublicAccessSimulationCode(
            PublicScopeSimulator::OPERATION_UNSET,
            'name',
            null,
            $valueHolder,
            'returnValue',
            $originalClass
        );

        if (! $publicProperties->isEmpty()) {
            $callParent = 'if (isset(self::$' . $publicProperties->getName() . "[\$name])) {\n"
                . '    unset($this->' . $valueHolderName . '->$name);'
                . "\n} else {\n    " . $callParent . "\n}\n\n";
        }

        $callParent .= '$returnValue = false;';

        $this->setBody(InterceptorGenerator::createInterceptedMethodBody(
            $callParent,
            $this,
            $valueHolder,
            $prefixInterceptors,
            $suffixInterceptors,
            $parent
        ));
    }
}
