<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD;

use BeyondSEODeps\DDD\Infrastructure\Libs\Config;
use BeyondSEODeps\DDD\Infrastructure\Services\DDDService;
use BeyondSEODeps\Symfony\Component\DependencyInjection\ContainerInterface;
use BeyondSEODeps\Symfony\Component\HttpKernel\Bundle\Bundle;

class DDDBundle extends Bundle
{
    protected static ContainerInterface $defaultContainer;

    public function boot()
    {
        $projectDirectory = $this->container->getParameterBag()->get('kernel.project_dir');
        if (!defined('BEYONDSEO_APP_ROOT_DIR')) {
            define('BEYONDSEO_APP_ROOT_DIR', $projectDirectory);
        }
        self::$defaultContainer = $this->container;

        Config::addConfigDirectory(DDDService::instance()->getRootDir() . '/config/app');
        parent::boot();
    }

    public static function getContainer(): ContainerInterface
    {
        return self::$defaultContainer;
    }


}