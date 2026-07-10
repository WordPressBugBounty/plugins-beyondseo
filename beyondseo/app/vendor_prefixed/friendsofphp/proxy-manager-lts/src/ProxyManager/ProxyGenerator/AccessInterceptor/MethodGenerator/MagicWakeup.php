<?php

declare(strict_types=1);

namespace BeyondSEODeps\ProxyManager\ProxyGenerator\AccessInterceptor\MethodGenerator;

use BeyondSEODeps\ProxyManager\Generator\MagicMethodGenerator;
use BeyondSEODeps\ProxyManager\ProxyGenerator\Util\Properties;
use BeyondSEODeps\ProxyManager\ProxyGenerator\Util\UnsetPropertiesGenerator;
use ReflectionClass;

/**
 * Magic `__wakeup` for lazy loading value holder objects
 */
class MagicWakeup extends MagicMethodGenerator
{
    /**
     * Constructor
     */
    public function __construct(ReflectionClass $originalClass)
    {
        parent::__construct($originalClass, '__wakeup');

        $this->setBody(UnsetPropertiesGenerator::generateSnippet(
            Properties::fromReflectionClass($originalClass),
            'this'
        ));
    }
}
