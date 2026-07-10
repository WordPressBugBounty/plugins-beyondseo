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
 * Magic `__get` for lazy loading value holder objects
 */
class MagicGet extends MagicMethodGenerator
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
        parent::__construct($originalClass, '__get', [new ParameterGenerator('name')]);

        $hasParent = $originalClass->hasMethod('__get');

        $initializer = $initializerProperty->getName();
        $valueHolder = $valueHolderProperty->getName();
        $callParent  = 'if (isset(self::$' . $publicProperties->getName() . "[\$name])) {\n"
            . '    return $this->' . $valueHolder . '->$name;'
            . "\n}\n\n";

        if ($hasParent) {
            $this->setInitializerBody(
                $initializer,
                $valueHolder,
                $callParent . 'return $this->' . $valueHolder . '->__get($name);'
            );

            return;
        }

        $this->setInitializerBody(
            $initializer,
            $valueHolder,
            $callParent . PublicScopeSimulator::getPublicAccessSimulationCode(
                PublicScopeSimulator::OPERATION_GET,
                'name',
                null,
                $valueHolderProperty,
                null,
                $originalClass
            )
        );
    }

    private function setInitializerBody(string $initializer, string $valueHolder, string $callParent): void
    {
        $this->setBody(
            '$this->' . $initializer . ' && ($this->' . $initializer
            . '->__invoke($' . $valueHolder . ', $this, \'__get\', [\'name\' => $name], $this->'
            . $initializer . ') || 1) && $this->' . $valueHolder . ' = $' . $valueHolder . ';'
            . "\n\n" . $callParent
        );
    }
}
