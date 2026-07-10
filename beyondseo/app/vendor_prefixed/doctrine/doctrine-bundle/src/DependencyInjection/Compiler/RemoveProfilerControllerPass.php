<?php

namespace BeyondSEODeps\Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler;

use BeyondSEODeps\Doctrine\Bundle\DoctrineBundle\Controller\ProfilerController;
use BeyondSEODeps\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use BeyondSEODeps\Symfony\Component\DependencyInjection\ContainerBuilder;

/** @internal */
final class RemoveProfilerControllerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->has('twig') && $container->has('profiler')) {
            return;
        }

        $container->removeDefinition(ProfilerController::class);
    }
}
