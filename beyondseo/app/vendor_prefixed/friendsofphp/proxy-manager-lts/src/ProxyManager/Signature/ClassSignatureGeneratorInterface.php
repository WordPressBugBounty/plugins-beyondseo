<?php

declare(strict_types=1);

namespace BeyondSEODeps\ProxyManager\Signature;

use BeyondSEODeps\Laminas\Code\Generator\ClassGenerator;

class_exists(\Zend\Code\Generator\ClassGenerator::class);

/**
 * Applies a signature to a given class generator
 */
interface ClassSignatureGeneratorInterface
{
    /**
     * Applies a signature to a given class generator
     *
     * @param array<string, mixed> $parameters
     */
    public function addSignature(ClassGenerator $classGenerator, array $parameters): ClassGenerator;
}
