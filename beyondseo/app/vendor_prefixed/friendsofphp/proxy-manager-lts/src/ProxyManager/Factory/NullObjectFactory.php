<?php

declare(strict_types=1);

namespace BeyondSEODeps\ProxyManager\Factory;

use OutOfBoundsException;
use BeyondSEODeps\ProxyManager\Configuration;
use BeyondSEODeps\ProxyManager\Proxy\NullObjectInterface;
use BeyondSEODeps\ProxyManager\ProxyGenerator\NullObjectGenerator;
use BeyondSEODeps\ProxyManager\ProxyGenerator\ProxyGeneratorInterface;
use BeyondSEODeps\ProxyManager\Signature\Exception\InvalidSignatureException;
use BeyondSEODeps\ProxyManager\Signature\Exception\MissingSignatureException;

use function get_class;
use function is_object;

/**
 * Factory responsible of producing proxy objects
 */
class NullObjectFactory extends AbstractBaseFactory
{
    private $generator;

    public function __construct(?Configuration $configuration = null)
    {
        parent::__construct($configuration);

        $this->generator = new NullObjectGenerator();
    }

    /**
     * @param object|string $instanceOrClassName the object to be wrapped or interface to transform to null object
     * @psalm-param RealObjectType|class-string<RealObjectType> $instanceOrClassName
     *
     * @psalm-return RealObjectType&NullObjectInterface
     *
     * @throws InvalidSignatureException
     * @throws MissingSignatureException
     * @throws OutOfBoundsException
     *
     * @psalm-template RealObjectType of object
     * @psalm-suppress MixedInferredReturnType We ignore type checks here, since `staticProxyConstructor` is not
     *                                         interfaced (by design)
     */
    public function createProxy($instanceOrClassName): NullObjectInterface
    {
        $className      = is_object($instanceOrClassName) ? get_class($instanceOrClassName) : $instanceOrClassName;
        $proxyClassName = $this->generateProxy($className);

        /**
         * We ignore type checks here, since `staticProxyConstructor` is not interfaced (by design)
         *
         * @psalm-suppress MixedMethodCall
         * @psalm-suppress MixedReturnStatement
         */
        return $proxyClassName::staticProxyConstructor();
    }

    protected function getGenerator(): ProxyGeneratorInterface
    {
        return $this->generator;
    }
}
