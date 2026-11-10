<?php
declare(strict_types=1);

namespace RankingCoach\Inc\Core\Rest\Controllers;

if (!defined('ABSPATH')) {
    exit;
}

use RankingCoach\Inc\Core\Base\Traits\RcLoggerTrait;
use RankingCoach\Inc\Core\Helpers\Traits\RcApiTrait;
use RankingCoach\Inc\Core\Seo\Services\ActionPlanService;
use Throwable;
use WP_REST_Request;
use WP_REST_Response;

/** GET /action-plan: the page issues of the Action Plan page (ActionPlanService::getPlan()). */
class ActionPlanController
{
    use RcApiTrait;
    use RcLoggerTrait;

    public function get(WP_REST_Request $request): WP_REST_Response
    {
        try {
            return new WP_REST_Response(ActionPlanService::getInstance()->getPlan(), 200);
        } catch (Throwable $e) {
            $this->log('Error in ActionPlanController::get: ' . $e->getMessage(), 'ERROR');
            return $this->generateErrorResponse($e, null, 500);
        }
    }
}
