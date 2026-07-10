<?php

declare(strict_types=1);

namespace BeyondSEODeps\ProxyManager\Signature;

use BeyondSEODeps\Laminas\Code\Exception\InvalidArgumentException;
use BeyondSEODeps\Laminas\Code\Generator\ClassGenerator;
use BeyondSEODeps\Laminas\Code\Generator\PropertyGenerator;

/**
 * Applies a signature to a given class generator
 */
final class ClassSignatureGenerator implements ClassSignatureGeneratorInterface
{
    private $signatureGenerator;

    public function __construct(SignatureGeneratorInterface $signatureGenerator)
    {
        $this->signatureGenerator = $signatureGenerator;
    }

    /**
     * {@inheritDoc}
     *
     * @throws InvalidArgumentException
     */
    public function addSignature(ClassGenerator $classGenerator, array $parameters): ClassGenerator
    {
        $classGenerator->addPropertyFromGenerator(new PropertyGenerator(
            'signature' . $this->signatureGenerator->generateSignatureKey($parameters),
            $this->signatureGenerator->generateSignature($parameters),
            PropertyGenerator::FLAG_STATIC | PropertyGenerator::FLAG_PRIVATE
        ));

        return $classGenerator;
    }
}
