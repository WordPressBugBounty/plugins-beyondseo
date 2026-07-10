<?php
declare(strict_types=1);

namespace BeyondSEO\Presentation\Api\Public\Integrations\WordPress\Controller;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Infrastructure\Traits\ResponseErrorTrait;
use BeyondSEO\Presentation\Api\Public\Integrations\WordPress\Dtos\PingGetResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\Controller\HttpController;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Summary;
use BeyondSEODeps\DDD\Presentation\Base\Router\Routes\Post;
use BeyondSEODeps\DDD\Presentation\Base\Router\Routes\Route;

/**
 * Class WPPingController
 */
#[Route('integration')]
class WPServiceIntegrationController extends HttpController
{
    use ResponseErrorTrait;

    /**
     * Retrieve 'ok' response for ping requests.
     * @return PingGetResponseDto
     */
    #[Post('/status')]
    #[Summary('Integration Status')]
    public function integrationStatus(): PingGetResponseDto
    {
        $response = new PingGetResponseDto();
        $response->ok = true;
        return $response;
    }
}