<?php

namespace BeyondSEODeps\DDD\Symfony\Loaders;

use ReflectionClass;
use BeyondSEODeps\Symfony\Component\Config\FileLocatorInterface;
use BeyondSEODeps\Symfony\Component\Config\Resource\FileResource;
use BeyondSEODeps\Symfony\Component\HttpKernel\Config\FileLocator;
use BeyondSEODeps\Symfony\Component\Routing\Loader\AnnotationFileLoader;
use BeyondSEODeps\Symfony\Component\Routing\RouteCollection;

class CustomAnnotationFileLoader extends AnnotationFileLoader
{
    public function __construct(FileLocator $locator, CustomAnnotationClassLoader $loader)
    {
        parent::__construct($locator, $loader);
    }

    public function load(mixed $file, ?string $type = null): ?RouteCollection
    {
        $path = $this->locator->locate($file);

        $collection = new RouteCollection();
        if ($class = $this->findClass($path)) {
            $refl = new ReflectionClass($class);
            if ($refl->isAbstract()) {
                return null;
            }

            $collection->addResource(new FileResource($path));
            $collection->addCollection($this->loader->load($class, $type));
        }

        gc_mem_caches();

        return $collection;
    }

}
