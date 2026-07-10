<?php

declare(strict_types=1);

namespace BeyondSEO\Presentation\Api\Documentation\Controller;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Presentation\Api\Documentation\Dtos\DocumentationOpenApiRequestDto;
use BeyondSEO\Presentation\Api\Documentation\Dtos\DocumentationOpenApiResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\Controller\DocumentationController;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\HtmlResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Ignore;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Info;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Server;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Document;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Exceptions\TypeDefinitionMissingOrWrong;
use BeyondSEODeps\DDD\Presentation\Base\Router\Routes\Get;
use BeyondSEODeps\DDD\Presentation\Base\Router\Routes\Route;
use ReflectionException;

#[Ignore]
#[Info('RC Client API', '3.0.0')]
#[Server('https://www.rankingcoach.com/')]
#[Route('documentation')]
class ClientDocumentationController extends DocumentationController
{
    public const DOCUMENTATION_ROUTE = 'documentationRoute';

    /**
     * @param DocumentationOpenApiRequestDto $requestDto
     * @return DocumentationOpenApiResponseDto
     * @throws ReflectionException
     * @throws TypeDefinitionMissingOrWrong
     */
    #[Ignore]
    #[Get('/openapi', name: self::DOCUMENTATION_ROUTE)]
    public function openApi(DocumentationOpenApiRequestDto $requestDto): DocumentationOpenApiResponseDto
    {
	    $routeCollection = $this->getRouteCollection();
	    $responseDto = new DocumentationOpenApiResponseDto();
        $responseDto->document = new Document($routeCollection);
        if ($requestDto->removeSchemaTags) {
            $responseDto->document->removeSchemaTags();
        }

        return $responseDto;
    }

    /**
     * Generates HTML documentation using Scalar.
     * @param DocumentationOpenApiRequestDto $requestDto
     * @return HtmlResponseDto
     */
    #[Ignore]
    #[Get]
    public function documentation(DocumentationOpenApiRequestDto $requestDto): HtmlResponseDto
    {
        $docUrl = $this->generateUrl(self::DOCUMENTATION_ROUTE) . '?removeSchemaTags=1';
        return $this->render(
            'Api/Documentation/Scalar.twig',
            ['docUrl' => $docUrl, 'title' => 'RC Client Documentation']
        );
    }
}