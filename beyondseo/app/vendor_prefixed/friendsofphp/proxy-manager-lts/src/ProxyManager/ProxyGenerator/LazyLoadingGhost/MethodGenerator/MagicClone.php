<?php

declare(strict_types=1);

namespace BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator;

use BeyondSEODeps\Laminas\Code\Generator\MethodGenerator;
use BeyondSEODeps\Laminas\Code\Generator\PropertyGenerator;
use BeyondSEODeps\ProxyManager\Generator\MagicMethodGenerator;
use ReflectionClass;

/**
 * Magic `__clone` for lazy loading ghost objects
 */
class MagicClone extends MagicMethodGenerator
{
    /**
     * Constructor
     */
    public function __construct(
        ReflectionClass $originalClass,
        PropertyGenerator $initializerProperty,
        MethodGenerator $callInitializer
    ) {
        parent::__construct($originalClass, '__clone');

        $this->setBody(
            '$this->' . $initializerProperty->getName() . ' && $this->' . $callInitializer->getName()
            . '(\'__clone\', []);'
            . ($originalClass->hasMethod('__clone') ? "\n\nparent::__clone();" : '')
        );
    }
}
