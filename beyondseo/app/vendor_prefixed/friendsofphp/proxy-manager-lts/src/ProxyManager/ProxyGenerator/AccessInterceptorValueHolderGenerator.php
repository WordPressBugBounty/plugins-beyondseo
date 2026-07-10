<?php

declare(strict_types=1);

namespace BeyondSEODeps\ProxyManager\ProxyGenerator;

use InvalidArgumentException;
use BeyondSEODeps\Laminas\Code\Generator\ClassGenerator;
use BeyondSEODeps\Laminas\Code\Generator\MethodGenerator;
use BeyondSEODeps\Laminas\Code\Reflection\MethodReflection;
use BeyondSEODeps\ProxyManager\Exception\InvalidProxiedClassException;
use BeyondSEODeps\ProxyManager\Generator\Util\ClassGeneratorUtils;
use BeyondSEODeps\ProxyManager\Proxy\AccessInterceptorValueHolderInterface;
use BeyondSEODeps\ProxyManager\ProxyGenerator\AccessInterceptor\MethodGenerator\MagicWakeup;
use BeyondSEODeps\ProxyManager\ProxyGenerator\AccessInterceptor\MethodGenerator\SetMethodPrefixInterceptor;
use BeyondSEODeps\ProxyManager\ProxyGenerator\AccessInterceptor\MethodGenerator\SetMethodSuffixInterceptor;
use BeyondSEODeps\ProxyManager\ProxyGenerator\AccessInterceptor\PropertyGenerator\MethodPrefixInterceptors;
use BeyondSEODeps\ProxyManager\ProxyGenerator\AccessInterceptor\PropertyGenerator\MethodSuffixInterceptors;
use BeyondSEODeps\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\InterceptedMethod;
use BeyondSEODeps\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\MagicClone;
use BeyondSEODeps\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\MagicGet;
use BeyondSEODeps\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\MagicIsset;
use BeyondSEODeps\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\MagicSet;
use BeyondSEODeps\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\MagicUnset;
use BeyondSEODeps\ProxyManager\ProxyGenerator\AccessInterceptorValueHolder\MethodGenerator\StaticProxyConstructor;
use BeyondSEODeps\ProxyManager\ProxyGenerator\Assertion\CanProxyAssertion;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\PropertyGenerator\ValueHolderProperty;
use BeyondSEODeps\ProxyManager\ProxyGenerator\PropertyGenerator\PublicPropertiesMap;
use BeyondSEODeps\ProxyManager\ProxyGenerator\Util\Properties;
use BeyondSEODeps\ProxyManager\ProxyGenerator\Util\ProxiedMethodsFilter;
use BeyondSEODeps\ProxyManager\ProxyGenerator\ValueHolder\MethodGenerator\Constructor;
use BeyondSEODeps\ProxyManager\ProxyGenerator\ValueHolder\MethodGenerator\GetWrappedValueHolderValue;
use BeyondSEODeps\ProxyManager\ProxyGenerator\ValueHolder\MethodGenerator\MagicSleep;
use ReflectionClass;
use ReflectionMethod;

use function array_map;
use function array_merge;

/**
 * Generator for proxies implementing {@see \ProxyManager\Proxy\ValueHolderInterface}
 * and {@see \ProxyManager\Proxy\AccessInterceptorInterface}
 *
 * {@inheritDoc}
 */
class AccessInterceptorValueHolderGenerator implements ProxyGeneratorInterface
{
    /**
     * {@inheritDoc}
     *
     * @return void
     *
     * @throws InvalidArgumentException
     * @throws InvalidProxiedClassException
     */
    public function generate(ReflectionClass $originalClass, ClassGenerator $classGenerator)
    {
        CanProxyAssertion::assertClassCanBeProxied($originalClass);

        $publicProperties = new PublicPropertiesMap(Properties::fromReflectionClass($originalClass));
        $interfaces       = [AccessInterceptorValueHolderInterface::class];

        if ($originalClass->isInterface()) {
            $interfaces[] = $originalClass->getName();
        } else {
            $classGenerator->setExtendedClass($originalClass->getName());
        }

        $classGenerator->setImplementedInterfaces($interfaces);
        $classGenerator->addPropertyFromGenerator($valueHolder        = new ValueHolderProperty($originalClass));
        $classGenerator->addPropertyFromGenerator($prefixInterceptors = new MethodPrefixInterceptors());
        $classGenerator->addPropertyFromGenerator($suffixInterceptors = new MethodSuffixInterceptors());
        $classGenerator->addPropertyFromGenerator($publicProperties);

        array_map(
            static function (MethodGenerator $generatedMethod) use ($originalClass, $classGenerator): void {
                ClassGeneratorUtils::addMethodIfNotFinal($originalClass, $classGenerator, $generatedMethod);
            },
            array_merge(
                array_map(
                    $this->buildMethodInterceptor($prefixInterceptors, $suffixInterceptors, $valueHolder),
                    ProxiedMethodsFilter::getProxiedMethods($originalClass)
                ),
                [
                    Constructor::generateMethod($originalClass, $valueHolder),
                    new StaticProxyConstructor($originalClass, $valueHolder, $prefixInterceptors, $suffixInterceptors),
                    new GetWrappedValueHolderValue($valueHolder),
                    new SetMethodPrefixInterceptor($prefixInterceptors),
                    new SetMethodSuffixInterceptor($suffixInterceptors),
                    new MagicGet(
                        $originalClass,
                        $valueHolder,
                        $prefixInterceptors,
                        $suffixInterceptors,
                        $publicProperties
                    ),
                    new MagicSet(
                        $originalClass,
                        $valueHolder,
                        $prefixInterceptors,
                        $suffixInterceptors,
                        $publicProperties
                    ),
                    new MagicIsset(
                        $originalClass,
                        $valueHolder,
                        $prefixInterceptors,
                        $suffixInterceptors,
                        $publicProperties
                    ),
                    new MagicUnset(
                        $originalClass,
                        $valueHolder,
                        $prefixInterceptors,
                        $suffixInterceptors,
                        $publicProperties
                    ),
                    new MagicClone($originalClass, $valueHolder, $prefixInterceptors, $suffixInterceptors),
                    new MagicSleep($originalClass, $valueHolder),
                    new MagicWakeup($originalClass),
                ]
            )
        );
    }

    private function buildMethodInterceptor(
        MethodPrefixInterceptors $prefixes,
        MethodSuffixInterceptors $suffixes,
        ValueHolderProperty $valueHolder
    ): callable {
        return static function (ReflectionMethod $method) use ($prefixes, $suffixes, $valueHolder): InterceptedMethod {
            return InterceptedMethod::generateMethod(
                new MethodReflection($method->getDeclaringClass()->getName(), $method->getName()),
                $valueHolder,
                $prefixes,
                $suffixes
            );
        };
    }
}
