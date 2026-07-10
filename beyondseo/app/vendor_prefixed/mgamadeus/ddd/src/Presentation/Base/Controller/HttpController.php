<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Presentation\Base\Controller;

use BeyondSEODeps\DDD\Infrastructure\Services\DDDService;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\HtmlResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RedirectResponseDto;
use BeyondSEODeps\Symfony\Component\HttpFoundation\RequestStack;
use BeyondSEODeps\Symfony\Component\HttpFoundation\Response;

class HttpController extends BaseController
{
    public function __construct(RequestStack $requestStack)
    {
        $request = $requestStack->getCurrentRequest();
        if ($request->query->get('noCache')){
            DDDService::instance()->deactivateCaches();
        }
    }

    /**
     * Renders a view.
     */
    protected function render(string $view, array $parameters = [], ?Response $response = null): HtmlResponseDto
    {
        $content = $this->renderView($view, $parameters);

        if (null === $response) {
            $response = new HtmlResponseDto();
        }

        $response->setContent($content);

        return $response;
    }

    public function getResponse(string $content, ?HtmlResponseDto $responseDto = null): HtmlResponseDto{
        if (null === $responseDto) {
            $responseDto = new HtmlResponseDto();
        }

        $responseDto->setContent($content);

        return $responseDto;
    }

    protected function redirect(string $url, int $status = 302): RedirectResponseDto
    {
        return new RedirectResponseDto($url, $status);
    }
}
