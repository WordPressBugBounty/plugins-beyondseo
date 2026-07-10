<?php

declare(strict_types=1);

namespace BeyondSEODeps\ProxyManager\ProxyGenerator;

use InvalidArgumentException;
use BeyondSEODeps\Laminas\Code\Generator\ClassGenerator;
use BeyondSEODeps\Laminas\Code\Generator\MethodGenerator;
use BeyondSEODeps\Laminas\Code\Reflection\MethodReflection;
use BeyondSEODeps\ProxyManager\Exception\InvalidProxiedClassException;
use BeyondSEODeps\ProxyManager\Generator\Util\ClassGeneratorUtils;
use BeyondSEODeps\ProxyManager\Proxy\VirtualProxyInterface;
use BeyondSEODeps\ProxyManager\ProxyGenerator\AccessInterceptor\MethodGenerator\MagicWakeup;
use BeyondSEODeps\ProxyManager\ProxyGenerator\Assertion\CanProxyAssertion;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoading\MethodGenerator\StaticProxyConstructor;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\GetProxyInitializer;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\InitializeProxy;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\IsProxyInitialized;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\LazyLoadingMethodInterceptor;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\MagicClone;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\MagicGet;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\MagicIsset;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\MagicSet;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\MagicSleep;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\MagicUnset;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\SetProxyInitializer;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator\SkipDestructor;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\PropertyGenerator\InitializerProperty;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingValueHolder\PropertyGenerator\ValueHolderProperty;
use BeyondSEODeps\ProxyManager\ProxyGenerator\PropertyGenerator\PublicPropertiesMap;
use BeyondSEODeps\ProxyManager\ProxyGenerator\Util\Properties;
use BeyondSEODeps\ProxyManager\ProxyGenerator\Util\ProxiedMethodsFilter;
use BeyondSEODeps\ProxyManager\ProxyGenerator\ValueHolder\MethodGenerator\Constructor;
use BeyondSEODeps\ProxyManager\ProxyGenerator\ValueHolder\MethodGenerator\GetWrappedValueHolderValue;
use ReflectionClass;
use ReflectionMethod;

use function array_map;
use function array_merge;
use function func_get_arg;
use function func_num_args;
use function str_replace;
use function substr;

/**
 * Generator for proxies implementing {@see \ProxyManager\Proxy\VirtualProxyInterface}
 *
 * {@inheritDoc}
 */
class LazyLoadingValueHolderGenerator implements ProxyGeneratorInterface
{
    /**
     * {@inheritDoc}
     *
     * @psalm-param array{skipDestructor?: bool, fluentSafe?: bool} $proxyOptions
     *
     * @return void
     *
     * @throws InvalidProxiedClassException
     * @throws InvalidArgumentException
     */
    public function generate(ReflectionClass $originalClass, ClassGenerator $classGenerator/*, array $proxyOptions = []*/)
    {
        /** @psalm-var array{skipDestructor?: bool, fluentSafe?: bool} $proxyOptions */
        $proxyOptions = func_num_args() >= 3 ? func_get_arg(2) : [];

        CanProxyAssertion::assertClassCanBeProxied($originalClass);

        $interfaces       = [VirtualProxyInterface::class];
        $publicProperties = new PublicPropertiesMap(Properties::fromReflectionClass($originalClass));

        if ($originalClass->isInterface()) {
            $interfaces[] = $originalClass->getName();
        } else {
            $classGenerator->setExtendedClass($originalClass->getName());
        }

        $classGenerator->setImplementedInterfaces($interfaces);
        $classGenerator->addPropertyFromGenerator($valueHolder = new ValueHolderProperty($originalClass));
        $classGenerator->addPropertyFromGenerator($initializer = new InitializerProperty());
        $classGenerator->addPropertyFromGenerator($publicProperties);

        $skipDestructor  = ($proxyOptions['skipDestructor'] ?? false) && $originalClass->hasMethod('__destruct');
        $excludedMethods = ProxiedMethodsFilter::DEFAULT_EXCLUDED;

        if ($skipDestructor) {
            $excludedMethods[] = '__destruct';
        }

        array_map(
            static function (MethodGenerator $generatedMethod) use ($originalClass, $classGenerator): void {
                ClassGeneratorUtils::addMethodIfNotFinal($originalClass, $classGenerator, $generatedMethod);
            },
            array_merge(
                array_map(
                    $this->buildLazyLoadingMethodInterceptor($initializer, $valueHolder, $proxyOptions['fluentSafe'] ?? false),
                    ProxiedMethodsFilter::getProxiedMethods($originalClass, $excludedMethods)
                ),
                [
                    new StaticProxyConstructor($initializer, Properties::fromReflectionClass($originalClass)),
                    Constructor::generateMethod($originalClass, $valueHolder),
                    new MagicGet($originalClass, $initializer, $valueHolder, $publicProperties),
                    new MagicSet($originalClass, $initializer, $valueHolder, $publicProperties),
                    new MagicIsset($originalClass, $initializer, $valueHolder, $publicProperties),
                    new MagicUnset($originalClass, $initializer, $valueHolder, $publicProperties),
                    new MagicClone($originalClass, $initializer, $valueHolder),
                    new MagicSleep($originalClass, $initializer, $valueHolder),
                    new MagicWakeup($originalClass),
                    new SetProxyInitializer($initializer),
                    new GetProxyInitializer($initializer),
                    new InitializeProxy($initializer, $valueHolder),
                    new IsProxyInitialized($valueHolder),
                    new GetWrappedValueHolderValue($valueHolder),
                ],
                $skipDestructor ? [new SkipDestructor($initializer, $valueHolder)] : []
            )
        );
    }

    private function buildLazyLoadingMethodInterceptor(
        InitializerProperty $initializer,
        ValueHolderProperty $valueHolder,
        bool $fluentSafe
    ): callable {
        return static function (ReflectionMethod $method) use ($initializer, $valueHolder, $fluentSafe): LazyLoadingMethodInterceptor {
            $byRef  = $method->returnsReference() ? '& ' : '';
            $method = LazyLoadingMethodInterceptor::generateMethod(
                new MethodReflection($method->getDeclaringClass()->getName(), $method->getName()),
                $initializer,
                $valueHolder
            );

            if ($fluentSafe) {
                $valueHolderName = '$this->' . $valueHolder->getName();
                $body            = $method->getBody();
                $newBody         = str_replace('return ' . $valueHolderName, 'if (' . $valueHolderName . ' === $returnValue = ' . $byRef . $valueHolderName, $body);

                if ($newBody !== $body) {
                    $method->setBody(
                        substr($newBody, 0, -1) . ') {' . "\n"
                        . '    return $this;' . "\n"
                        . '}' . "\n\n"
                        . 'return $returnValue;'
                    );
                }
            }

            return $method;
        };
    }
}
