<?php

declare(strict_types=1);

namespace BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator;

use InvalidArgumentException;
use BeyondSEODeps\Laminas\Code\Generator\ParameterGenerator;
use BeyondSEODeps\Laminas\Code\Generator\PropertyGenerator;
use BeyondSEODeps\ProxyManager\Generator\MagicMethodGenerator;
use BeyondSEODeps\ProxyManager\ProxyGenerator\PropertyGenerator\PublicPropertiesMap;
use BeyondSEODeps\ProxyManager\ProxyGenerator\Util\PublicScopeSimulator;
use ReflectionClass;

/**
 * Magic `__set` for lazy loading value holder objects
 */
class MagicSet extends MagicMethodGenerator
{
    /**
     * Constructor
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        ReflectionClass $originalClass,
        PropertyGenerator $initializerProperty,
        PropertyGenerator $valueHolderProperty,
        PublicPropertiesMap $publicProperties
    ) {
        parent::__construct(
            $originalClass,
            '__set',
            [new ParameterGenerator('name'), new ParameterGenerator('value')]
        );

        $hasParent   = $originalClass->hasMethod('__set');
        $initializer = $initializerProperty->getName();
        $valueHolder = $valueHolderProperty->getName();
        $callParent  = '';

        if (! $publicProperties->isEmpty()) {
            $callParent = 'if (isset(self::$' . $publicProperties->getName() . "[\$name])) {\n"
                . '    return ($this->' . $valueHolder . '->$name = $value);'
                . "\n}\n\n";
        }

        $callParent .= $hasParent
            ? 'return $this->' . $valueHolder . '->__set($name, $value);'
            : PublicScopeSimulator::getPublicAccessSimulationCode(
                PublicScopeSimulator::OPERATION_SET,
                'name',
                'value',
                $valueHolderProperty,
                null,
                $originalClass
            );

        $this->setBody(
            '$this->' . $initializer . ' && ($this->' . $initializer
            . '->__invoke($' . $valueHolder . ', $this, '
            . '\'__set\', array(\'name\' => $name, \'value\' => $value), $this->' . $initializer . ') || 1)'
            . ' && $this->' . $valueHolder . ' = $' . $valueHolder . ';'
            . "\n\n" . $callParent
        );
    }
}
