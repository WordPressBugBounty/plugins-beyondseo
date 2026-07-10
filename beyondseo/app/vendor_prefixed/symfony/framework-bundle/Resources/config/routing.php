<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Component\DependencyInjection\Loader\Configurator;

use Psr\Container\ContainerInterface;
use BeyondSEODeps\Symfony\Bundle\FrameworkBundle\CacheWarmer\RouterCacheWarmer;
use BeyondSEODeps\Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use BeyondSEODeps\Symfony\Bundle\FrameworkBundle\Controller\TemplateController;
use BeyondSEODeps\Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader;
use BeyondSEODeps\Symfony\Bundle\FrameworkBundle\Routing\RedirectableCompiledUrlMatcher;
use BeyondSEODeps\Symfony\Bundle\FrameworkBundle\Routing\Router;
use BeyondSEODeps\Symfony\Component\Config\Loader\LoaderResolver;
use BeyondSEODeps\Symfony\Component\HttpKernel\EventListener\RouterListener;
use BeyondSEODeps\Symfony\Component\Routing\Generator\CompiledUrlGenerator;
use BeyondSEODeps\Symfony\Component\Routing\Generator\Dumper\CompiledUrlGeneratorDumper;
use BeyondSEODeps\Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use BeyondSEODeps\Symfony\Component\Routing\Loader\ContainerLoader;
use BeyondSEODeps\Symfony\Component\Routing\Loader\DirectoryLoader;
use BeyondSEODeps\Symfony\Component\Routing\Loader\GlobFileLoader;
use BeyondSEODeps\Symfony\Component\Routing\Loader\PhpFileLoader;
use BeyondSEODeps\Symfony\Component\Routing\Loader\XmlFileLoader;
use BeyondSEODeps\Symfony\Component\Routing\Loader\YamlFileLoader;
use BeyondSEODeps\Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;
use BeyondSEODeps\Symfony\Component\Routing\Matcher\ExpressionLanguageProvider;
use BeyondSEODeps\Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use BeyondSEODeps\Symfony\Component\Routing\RequestContext;
use BeyondSEODeps\Symfony\Component\Routing\RequestContextAwareInterface;
use BeyondSEODeps\Symfony\Component\Routing\RouterInterface;

return static function (ContainerConfigurator $container) {
    $container->parameters()
        ->set('router.request_context.host', 'localhost')
        ->set('router.request_context.scheme', 'http')
        ->set('router.request_context.base_url', '')
    ;

    $container->services()
        ->set('routing.resolver', LoaderResolver::class)

        ->set('routing.loader.xml', XmlFileLoader::class)
            ->args([
                service('file_locator'),
                '%kernel.environment%',
            ])
            ->tag('routing.loader')

        ->set('routing.loader.yml', YamlFileLoader::class)
            ->args([
                service('file_locator'),
                '%kernel.environment%',
            ])
            ->tag('routing.loader')

        ->set('routing.loader.php', PhpFileLoader::class)
            ->args([
                service('file_locator'),
                '%kernel.environment%',
            ])
            ->tag('routing.loader')

        ->set('routing.loader.glob', GlobFileLoader::class)
            ->args([
                service('file_locator'),
                '%kernel.environment%',
            ])
            ->tag('routing.loader')

        ->set('routing.loader.directory', DirectoryLoader::class)
            ->args([
                service('file_locator'),
                '%kernel.environment%',
            ])
            ->tag('routing.loader')

        ->set('routing.loader.container', ContainerLoader::class)
            ->args([
                tagged_locator('routing.route_loader'),
                '%kernel.environment%',
            ])
            ->tag('routing.loader')

        ->set('routing.loader', DelegatingLoader::class)
            ->public()
            ->args([
                service('routing.resolver'),
                [], // Default options
                [], // Default requirements
            ])

        ->set('router.default', Router::class)
            ->args([
                service(ContainerInterface::class),
                param('router.resource'),
                [
                    'cache_dir' => param('kernel.cache_dir'),
                    'debug' => param('kernel.debug'),
                    'generator_class' => CompiledUrlGenerator::class,
                    'generator_dumper_class' => CompiledUrlGeneratorDumper::class,
                    'matcher_class' => RedirectableCompiledUrlMatcher::class,
                    'matcher_dumper_class' => CompiledUrlMatcherDumper::class,
                ],
                service('router.request_context')->ignoreOnInvalid(),
                service('parameter_bag')->ignoreOnInvalid(),
                service('logger')->ignoreOnInvalid(),
                param('kernel.default_locale'),
            ])
            ->call('setConfigCacheFactory', [
                service('config_cache_factory'),
            ])
            ->tag('monolog.logger', ['channel' => 'router'])
            ->tag('container.service_subscriber', ['id' => 'routing.loader'])
        ->alias('router', 'router.default')
            ->public()
        ->alias(RouterInterface::class, 'router')
        ->alias(UrlGeneratorInterface::class, 'router')
        ->alias(UrlMatcherInterface::class, 'router')
        ->alias(RequestContextAwareInterface::class, 'router')

        ->set('router.request_context', RequestContext::class)
            ->factory([RequestContext::class, 'fromUri'])
            ->args([
                param('router.request_context.base_url'),
                param('router.request_context.host'),
                param('router.request_context.scheme'),
                param('request_listener.http_port'),
                param('request_listener.https_port'),
            ])
            ->call('setParameter', [
                '_functions',
                service('router.expression_language_provider')->ignoreOnInvalid(),
            ])
        ->alias(RequestContext::class, 'router.request_context')

        ->set('router.expression_language_provider', ExpressionLanguageProvider::class)
            ->args([
                tagged_locator('routing.expression_language_function', 'function'),
            ])
            ->tag('routing.expression_language_provider')

        ->set('router.cache_warmer', RouterCacheWarmer::class)
            ->args([service(ContainerInterface::class)])
            ->tag('container.service_subscriber', ['id' => 'router'])
            ->tag('kernel.cache_warmer')

        ->set('router_listener', RouterListener::class)
            ->args([
                service('router'),
                service('request_stack'),
                service('router.request_context')->ignoreOnInvalid(),
                service('logger')->ignoreOnInvalid(),
                param('kernel.project_dir'),
                param('kernel.debug'),
            ])
            ->tag('kernel.event_subscriber')
            ->tag('monolog.logger', ['channel' => 'request'])

        ->set(RedirectController::class)
            ->public()
            ->args([
                service('router'),
                inline_service('int')
                    ->factory([service('router.request_context'), 'getHttpPort']),
                inline_service('int')
                    ->factory([service('router.request_context'), 'getHttpsPort']),
            ])

        ->set(TemplateController::class)
            ->args([
                service('twig')->ignoreOnInvalid(),
            ])
            ->public()
    ;
};
