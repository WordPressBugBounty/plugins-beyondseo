<?php
declare(strict_types=1);

namespace BeyondSEO\Presentation\Api\Public\Integrations\WordPress\Controller;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Infrastructure\Traits\ResponseErrorTrait;
use BeyondSEO\Presentation\Api\Public\Integrations\WordPress\Dtos\ServiceSyncResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\Controller\HttpController;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Summary;
use BeyondSEODeps\DDD\Presentation\Base\Router\Routes\Post;
use BeyondSEODeps\DDD\Presentation\Base\Router\Routes\Route;
use Exception;
use RankingCoach\Inc\Core\Api\Content\ContentApiManager;
use Throwable;

/**
 * Class WPPingController
 */
#[Route('sync')]
class WPServiceSyncController extends HttpController
{
    use ResponseErrorTrait;

    /**
     * Retrieve 'ok' response for ping requests.
     * @return ServiceSyncResponseDto
     * @throws Throwable
     */
    #[Post('/keywords')]
    #[Summary('Sync Keywords')]
    public function syncKeywords(): ServiceSyncResponseDto
    {
        $response = new ServiceSyncResponseDto();
        try {
            $result = ContentApiManager::handleKeywordsSynchronization();
            $response->keywords = (object)$result ?? null;
            $response->success = true;
        } catch (Exception $e) {
            $response->success = false;
            $response->message = $e->getMessage();
        }
        return $response;
    }
}