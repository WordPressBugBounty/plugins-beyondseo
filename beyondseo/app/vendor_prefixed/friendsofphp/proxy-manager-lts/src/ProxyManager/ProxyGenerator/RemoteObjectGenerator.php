<?php

declare(strict_types=1);

namespace BeyondSEODeps\ProxyManager\ProxyGenerator;

use BeyondSEODeps\Laminas\Code\Generator\ClassGenerator;
use BeyondSEODeps\Laminas\Code\Generator\Exception\InvalidArgumentException;
use BeyondSEODeps\Laminas\Code\Generator\MethodGenerator;
use BeyondSEODeps\Laminas\Code\Reflection\MethodReflection;
use BeyondSEODeps\ProxyManager\Exception\InvalidProxiedClassException;
use BeyondSEODeps\ProxyManager\Generator\Util\ClassGeneratorUtils;
use BeyondSEODeps\ProxyManager\Proxy\RemoteObjectInterface;
use BeyondSEODeps\ProxyManager\ProxyGenerator\Assertion\CanProxyAssertion;
use BeyondSEODeps\ProxyManager\ProxyGenerator\RemoteObject\MethodGenerator\MagicGet;
use BeyondSEODeps\ProxyManager\ProxyGenerator\RemoteObject\MethodGenerator\MagicIsset;
use BeyondSEODeps\ProxyManager\ProxyGenerator\RemoteObject\MethodGenerator\MagicSet;
use BeyondSEODeps\ProxyManager\ProxyGenerator\RemoteObject\MethodGenerator\MagicUnset;
use BeyondSEODeps\ProxyManager\ProxyGenerator\RemoteObject\MethodGenerator\RemoteObjectMethod;
use BeyondSEODeps\ProxyManager\ProxyGenerator\RemoteObject\MethodGenerator\StaticProxyConstructor;
use BeyondSEODeps\ProxyManager\ProxyGenerator\RemoteObject\PropertyGenerator\AdapterProperty;
use BeyondSEODeps\ProxyManager\ProxyGenerator\Util\ProxiedMethodsFilter;
use ReflectionClass;
use ReflectionMethod;

use function array_map;
use function array_merge;

/**
 * Generator for proxies implementing {@see \ProxyManager\Proxy\RemoteObjectInterface}
 *
 * {@inheritDoc}
 */
class RemoteObjectGenerator implements ProxyGeneratorInterface
{
    /**
     * {@inheritDoc}
     *
     * @return void
     *
     * @throws InvalidProxiedClassException
     * @throws InvalidArgumentException
     */
    public function generate(ReflectionClass $originalClass, ClassGenerator $classGenerator)
    {
        CanProxyAssertion::assertClassCanBeProxied($originalClass);

        $interfaces = [RemoteObjectInterface::class];

        if ($originalClass->isInterface()) {
            $interfaces[] = $originalClass->getName();
        } else {
            $classGenerator->setExtendedClass($originalClass->getName());
        }

        $classGenerator->setImplementedInterfaces($interfaces);
        $classGenerator->addPropertyFromGenerator($adapter = new AdapterProperty());

        array_map(
            static function (MethodGenerator $generatedMethod) use ($originalClass, $classGenerator): void {
                ClassGeneratorUtils::addMethodIfNotFinal($originalClass, $classGenerator, $generatedMethod);
            },
            array_merge(
                array_map(
                    static function (ReflectionMethod $method) use ($adapter, $originalClass): RemoteObjectMethod {
                        return RemoteObjectMethod::generateMethod(
                            new MethodReflection($method->getDeclaringClass()->getName(), $method->getName()),
                            $adapter,
                            $originalClass
                        );
                    },
                    ProxiedMethodsFilter::getProxiedMethods(
                        $originalClass,
                        ['__get', '__set', '__isset', '__unset']
                    )
                ),
                [
                    new StaticProxyConstructor($originalClass, $adapter),
                    new MagicGet($originalClass, $adapter),
                    new MagicSet($originalClass, $adapter),
                    new MagicIsset($originalClass, $adapter),
                    new MagicUnset($originalClass, $adapter),
                ]
            )
        );
    }
}
