<?php

declare(strict_types=1);

namespace BeyondSEODeps\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator;

use BeyondSEODeps\Laminas\Code\Generator\PropertyGenerator;
use BeyondSEODeps\ProxyManager\Generator\MagicMethodGenerator;
use BeyondSEODeps\ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\Util\InterceptorGenerator;
use BeyondSEODeps\ProxyManager\ProxyGenerator\Util\GetMethodIfExists;
use ReflectionClass;

/**
 * Magic `__sleep` for lazy loading ghost objects
 */
class MagicSleep extends MagicMethodGenerator
{
    /**
     * Constructor
     */
    public function __construct(
        ReflectionClass $originalClass,
        PropertyGenerator $prefixInterceptors,
        PropertyGenerator $suffixInterceptors
    ) {
        parent::__construct($originalClass, '__sleep');

        $parent = GetMethodIfExists::get($originalClass, '__sleep');

        $callParent = $parent ? '$returnValue = & parent::__sleep();' : '$returnValue = array_keys((array) $this);';

        $this->setBody(InterceptorGenerator::createInterceptedMethodBody(
            $callParent,
            $this,
            $prefixInterceptors,
            $suffixInterceptors,
            $parent
        ));
    }
}
