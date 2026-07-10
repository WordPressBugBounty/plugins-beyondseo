<?php

declare(strict_types=1);

namespace BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator;

use BeyondSEODeps\Laminas\Code\Generator\Exception\InvalidArgumentException;
use BeyondSEODeps\Laminas\Code\Generator\PropertyGenerator;
use BeyondSEODeps\ProxyManager\Generator\MethodGenerator;

/**
 * Implementation for {@see \ProxyManager\Proxy\LazyLoadingInterface::isProxyInitialized}
 * for lazy loading value holder objects
 */
class IsProxyInitialized extends MethodGenerator
{
    /**
     * Constructor
     *
     * @throws InvalidArgumentException
     */
    public function __construct(PropertyGenerator $initializerProperty)
    {
        parent::__construct('isProxyInitialized');
        $this->setReturnType('bool');
        $this->setBody('return ! $this->' . $initializerProperty->getName() . ';');
    }
}
