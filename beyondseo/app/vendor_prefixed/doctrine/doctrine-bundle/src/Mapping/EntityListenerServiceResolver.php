<?php

namespace BeyondSEODeps\Doctrine\Bundle\DoctrineBundle\Mapping;

use BeyondSEODeps\Doctrine\ORM\Mapping\EntityListenerResolver;

interface EntityListenerServiceResolver extends EntityListenerResolver
{
    /**
     * @param string $className
     * @param string $serviceId
     */
    // phpcs:ignore
    public function registerService($className, $serviceId);
}
