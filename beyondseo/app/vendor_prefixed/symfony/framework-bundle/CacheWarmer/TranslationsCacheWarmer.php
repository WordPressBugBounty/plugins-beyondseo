<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Bundle\FrameworkBundle\CacheWarmer;

use Psr\Container\ContainerInterface;
use BeyondSEODeps\Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use BeyondSEODeps\Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use BeyondSEODeps\Symfony\Contracts\Service\ServiceSubscriberInterface;
use BeyondSEODeps\Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Generates the catalogues for translations.
 *
 * @author Xavier Leune <xavier.leune@gmail.com>
 */
class TranslationsCacheWarmer implements CacheWarmerInterface, ServiceSubscriberInterface
{
    private $container;
    private $translator;

    public function __construct(ContainerInterface $container)
    {
        // As this cache warmer is optional, dependencies should be lazy-loaded, that's why a container should be injected.
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     *
     * @return string[]
     */
    public function warmUp(string $cacheDir): array
    {
        $this->translator ??= $this->container->get('translator');

        if ($this->translator instanceof WarmableInterface) {
            return (array) $this->translator->warmUp($cacheDir);
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices(): array
    {
        return [
            'translator' => TranslatorInterface::class,
        ];
    }
}
