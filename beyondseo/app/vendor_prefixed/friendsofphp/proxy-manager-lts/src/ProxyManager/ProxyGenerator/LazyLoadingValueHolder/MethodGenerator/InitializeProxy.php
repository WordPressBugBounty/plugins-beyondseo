<?php

declare(strict_types=1);

namespace BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator;

use BeyondSEODeps\Laminas\Code\Generator\Exception\InvalidArgumentException;
use BeyondSEODeps\Laminas\Code\Generator\PropertyGenerator;
use BeyondSEODeps\ProxyManager\Generator\MethodGenerator;

/**
 * Implementation for {@see \ProxyManager\Proxy\LazyLoadingInterface::initializeProxy}
 * for lazy loading value holder objects
 */
class InitializeProxy extends MethodGenerator
{
    /**
     * Constructor
     *
     * @throws InvalidArgumentException
     */
    public function __construct(PropertyGenerator $initializerProperty, PropertyGenerator $valueHolderProperty)
    {
        parent::__construct('initializeProxy');
        $this->setReturnType('bool');

        $initializer = $initializerProperty->getName();
        $valueHolder = $valueHolderProperty->getName();

        $this->setBody(
            'return $this->' . $initializer . ' && ($this->' . $initializer
            . '->__invoke($' . $valueHolder
            . ', $this, \'initializeProxy\', array(), $this->' . $initializer . ') || 1)'
            . ' && $this->' . $valueHolder . ' = $' . $valueHolder . ';'
        );
    }
}
