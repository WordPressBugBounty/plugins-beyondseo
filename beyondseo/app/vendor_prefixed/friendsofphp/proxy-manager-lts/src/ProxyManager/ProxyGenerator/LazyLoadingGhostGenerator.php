<?php

declare(strict_types=1);

namespace BeyondSEODeps\ProxyManager\ProxyGenerator;

use InvalidArgumentException;
use BeyondSEODeps\Laminas\Code\Generator\ClassGenerator;
use BeyondSEODeps\Laminas\Code\Generator\MethodGenerator;
use BeyondSEODeps\Laminas\Code\Reflection\MethodReflection;
use BeyondSEODeps\ProxyManager\Exception\InvalidProxiedClassException;
use BeyondSEODeps\ProxyManager\Generator\MethodGenerator as ProxyManagerMethodGenerator;
use BeyondSEODeps\ProxyManager\Generator\Util\ClassGeneratorUtils;
use BeyondSEODeps\ProxyManager\Proxy\GhostObjectInterface;
use BeyondSEODeps\ProxyManager\ProxyGenerator\Assertion\CanProxyAssertion;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoading\MethodGenerator\StaticProxyConstructor;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\CallInitializer;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\GetProxyInitializer;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\InitializeProxy;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\IsProxyInitialized;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\MagicClone;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\MagicGet;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\MagicIsset;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\MagicSet;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\MagicSleep;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\MagicUnset;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\SetProxyInitializer;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator\SkipDestructor;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\InitializationTracker;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\InitializerProperty;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\PrivatePropertiesMap;
use BeyondSEODeps\ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\ProtectedPropertiesMap;
use BeyondSEODeps\ProxyManager\ProxyGenerator\PropertyGenerator\PublicPropertiesMap;
use BeyondSEODeps\ProxyManager\ProxyGenerator\Util\Properties;
use BeyondSEODeps\ProxyManager\ProxyGenerator\Util\ProxiedMethodsFilter;
use ReflectionClass;
use ReflectionMethod;

use function array_map;
use function array_merge;

/**
 * Generator for proxies implementing {@see \ProxyManager\Proxy\GhostObjectInterface}
 *
 * {@inheritDoc}
 */
class LazyLoadingGhostGenerator implements ProxyGeneratorInterface
{
    /**
     * {@inheritDoc}
     *
     * @psalm-param array{skippedProperties?: array<int, string>, skipDestructor?: bool} $proxyOptions
     *
     * @return void
     *
     * @throws InvalidProxiedClassException
     * @throws InvalidArgumentException
     */
    public function generate(ReflectionClass $originalClass, ClassGenerator $classGenerator, array $proxyOptions = [])
    {
        CanProxyAssertion::assertClassCanBeProxied($originalClass, false);

        $filteredProperties = Properties::fromReflectionClass($originalClass)
            ->filter($proxyOptions['skippedProperties'] ?? []);

        $publicProperties    = new PublicPropertiesMap($filteredProperties, true);
        $privateProperties   = new PrivatePropertiesMap($filteredProperties);
        $protectedProperties = new ProtectedPropertiesMap($filteredProperties);
        $skipDestructor      = ($proxyOptions['skipDestructor'] ?? false) && $originalClass->hasMethod('__destruct');

        $classGenerator->setExtendedClass($originalClass->getName());
        $classGenerator->setImplementedInterfaces([GhostObjectInterface::class]);
        $classGenerator->addPropertyFromGenerator($initializer           = new InitializerProperty());
        $classGenerator->addPropertyFromGenerator($initializationTracker = new InitializationTracker());
        $classGenerator->addPropertyFromGenerator($publicProperties);
        $classGenerator->addPropertyFromGenerator($privateProperties);
        $classGenerator->addPropertyFromGenerator($protectedProperties);

        $init = new CallInitializer($initializer, $initializationTracker, $filteredProperties);

        array_map(
            static function (MethodGenerator $generatedMethod) use ($originalClass, $classGenerator): void {
                ClassGeneratorUtils::addMethodIfNotFinal($originalClass, $classGenerator, $generatedMethod);
            },
            array_merge(
                $this->getAbstractProxiedMethods($originalClass, $skipDestructor),
                [
                    $init,
                    new StaticProxyConstructor($initializer, $filteredProperties),
                    new MagicGet(
                        $originalClass,
                        $initializer,
                        $init,
                        $publicProperties,
                        $protectedProperties,
                        $privateProperties,
                        $initializationTracker
                    ),
                    new MagicSet(
                        $originalClass,
                        $initializer,
                        $init,
                        $publicProperties,
                        $protectedProperties,
                        $privateProperties
                    ),
                    new MagicIsset(
                        $originalClass,
                        $initializer,
                        $init,
                        $publicProperties,
                        $protectedProperties,
                        $privateProperties
                    ),
                    new MagicUnset(
                        $originalClass,
                        $initializer,
                        $init,
                        $publicProperties,
                        $protectedProperties,
                        $privateProperties
                    ),
                    new MagicClone($originalClass, $initializer, $init),
                    new MagicSleep($originalClass, $initializer, $init),
                    new SetProxyInitializer($initializer),
                    new GetProxyInitializer($initializer),
                    new InitializeProxy($initializer, $init),
                    new IsProxyInitialized($initializer),
                ],
                $skipDestructor ? [new SkipDestructor($initializer)] : []
            )
        );
    }

    /**
     * Retrieves all abstract methods to be proxied
     *
     * @return MethodGenerator[]
     */
    private function getAbstractProxiedMethods(ReflectionClass $originalClass, bool $skipDestructor): array
    {
        $excludedMethods = ProxiedMethodsFilter::DEFAULT_EXCLUDED;

        if ($skipDestructor) {
            $excludedMethods[] = '__destruct';
        }

        return array_map(
            static function (ReflectionMethod $method): ProxyManagerMethodGenerator {
                $generated = ProxyManagerMethodGenerator::fromReflectionWithoutBodyAndDocBlock(
                    new MethodReflection($method->getDeclaringClass()->getName(), $method->getName())
                );

                $generated->setAbstract(false);

                return $generated;
            },
            ProxiedMethodsFilter::getAbstractProxiedMethods($originalClass, $excludedMethods)
        );
    }
}
