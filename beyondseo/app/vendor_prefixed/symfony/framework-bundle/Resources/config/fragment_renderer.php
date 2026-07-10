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

use BeyondSEODeps\Symfony\Component\HttpKernel\DependencyInjection\LazyLoadingFragmentHandler;
use BeyondSEODeps\Symfony\Component\HttpKernel\Fragment\EsiFragmentRenderer;
use BeyondSEODeps\Symfony\Component\HttpKernel\Fragment\FragmentUriGenerator;
use BeyondSEODeps\Symfony\Component\HttpKernel\Fragment\FragmentUriGeneratorInterface;
use BeyondSEODeps\Symfony\Component\HttpKernel\Fragment\HIncludeFragmentRenderer;
use BeyondSEODeps\Symfony\Component\HttpKernel\Fragment\InlineFragmentRenderer;
use BeyondSEODeps\Symfony\Component\HttpKernel\Fragment\SsiFragmentRenderer;

return static function (ContainerConfigurator $container) {
    $container->parameters()
        ->set('fragment.renderer.hinclude.global_template', null)
        ->set('fragment.path', '/_fragment')
    ;

    $container->services()
        ->set('fragment.handler', LazyLoadingFragmentHandler::class)
            ->args([
                abstract_arg('fragment renderer locator'),
                service('request_stack'),
                param('kernel.debug'),
            ])

        ->set('fragment.uri_generator', FragmentUriGenerator::class)
            ->args([param('fragment.path'), service('uri_signer'), service('request_stack')])
        ->alias(FragmentUriGeneratorInterface::class, 'fragment.uri_generator')

        ->set('fragment.renderer.inline', InlineFragmentRenderer::class)
            ->args([service('http_kernel'), service('event_dispatcher')])
            ->call('setFragmentPath', [param('fragment.path')])
            ->tag('kernel.fragment_renderer', ['alias' => 'inline'])

        ->set('fragment.renderer.hinclude', HIncludeFragmentRenderer::class)
            ->args([
                service('twig')->nullOnInvalid(),
                service('uri_signer'),
                param('fragment.renderer.hinclude.global_template'),
            ])
            ->call('setFragmentPath', [param('fragment.path')])

        ->set('fragment.renderer.esi', EsiFragmentRenderer::class)
            ->args([
                service('esi')->nullOnInvalid(),
                service('fragment.renderer.inline'),
                service('uri_signer'),
            ])
            ->call('setFragmentPath', [param('fragment.path')])
            ->tag('kernel.fragment_renderer', ['alias' => 'esi'])

        ->set('fragment.renderer.ssi', SsiFragmentRenderer::class)
            ->args([
                service('ssi')->nullOnInvalid(),
                service('fragment.renderer.inline'),
                service('uri_signer'),
            ])
            ->call('setFragmentPath', [param('fragment.path')])
            ->tag('kernel.fragment_renderer', ['alias' => 'ssi'])
    ;
};
