<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Presentation\Base\Controller;

use BeyondSEODeps\DDD\Infrastructure\Reflection\ReflectionClass;
use BeyondSEODeps\DDD\Presentation\Base\Controller\Filters\After;
use BeyondSEODeps\DDD\Presentation\Base\Controller\Filters\Before;
use ReflectionException;
use ReflectionMethod;
use BeyondSEODeps\Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class BaseController extends AbstractController
{
    public const FILTER_BEFORE = Before::class;
    public const FILTER_AFTER = After::class;

    /**
     * Returns Methods
     * @param string $filterType
     * @return ReflectionMethod[]
     * @throws ReflectionException
     */
    public static function getBeforeAndAfterMethods(string $filterType): array
    {
        /** @var ReflectionMethod[] $methods */
        $methods = [];
        foreach (ReflectionClass::instance(static::class)->getMethods() as $method) {
            foreach ($method->getAttributes($filterType) as $attribute) {
                $methods[] = $method;
            }
        }
        return $methods;
    }


}