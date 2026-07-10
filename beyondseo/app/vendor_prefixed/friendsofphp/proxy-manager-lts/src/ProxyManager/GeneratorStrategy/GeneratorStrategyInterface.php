<?php

declare(strict_types=1);

namespace BeyondSEODeps\ProxyManager\GeneratorStrategy;

use BeyondSEODeps\Laminas\Code\Generator\ClassGenerator;

class_exists(\Zend\Code\Generator\ClassGenerator::class);

/**
 * Generator strategy interface - defines basic behavior of class generators
 */
interface GeneratorStrategyInterface
{
    /**
     * Generate the provided class
     */
    public function generate(ClassGenerator $classGenerator): string;
}
