<?php
declare(strict_types=1);

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Controller;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Common\Entities\Accounts\Legacy\WPLegacyAccount;
use BeyondSEO\Domain\Integrations\WordPress\Plugin\Services\WPPluginService;
use BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\PluginInformationRequestDto;
use BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\PluginInformationResponseDto;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use BeyondSEODeps\DDD\Presentation\Base\Controller\HttpController;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Summary;
use BeyondSEODeps\DDD\Presentation\Base\Router\Routes\Post;
use BeyondSEODeps\DDD\Presentation\Base\Router\Routes\Route;
use BeyondSEODeps\Doctrine\ORM\Mapping\MappingException;
use Exception;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;
use WP_User;

/**
 * Class WPPluginController
 */
#[Route('pluginInformation')]
class WPPluginController extends HttpController
{
    /**
     * Get plugin data / information and current user data
     *
     * @param PluginInformationRequestDto $requestDto
     * @param WPPluginService $wpPluginService
     * @return PluginInformationResponseDto
     */
    #[Post]
    #[Summary('Get plugin information')]
    public function getPluginInformation(
        PluginInformationRequestDto $requestDto,
        WPPluginService $wpPluginService,
    ): PluginInformationResponseDto
    {
        $response = new PluginInformationResponseDto();
        $response->pluginData = $wpPluginService->getPlugin();

        /**
         * @TODO Implement database lazy loading if needed.
         */
//        $currentWpUser = wp_get_current_user();
//        if($currentWpUser instanceof WP_User) {
//            $currentUserId = $currentWpUser->ID;
//            $userInstance = new WPLegacyAccount($currentUserId);
//            $user = $userInstance->getById();
//        }
        $response->userData = null;
        $response->rcAccountId = $wpPluginService->getRankingCoachAccountId();
        $response->rcProjectId = $wpPluginService->getRankingCoachProjectId();

        // Map subscription codes to display names
        $subscription = $wpPluginService->getRankingCoachSubscription();
        switch ($subscription) {
            case 'seo_wp_free':
            case 'radar_wp_test':
                $response->rcSubscriptionName = 'Free';
                break;
            case 'seo_ai_small':
                $response->rcSubscriptionName = 'Standard';
                break;
            case 'seo_ai_medium':
            case 'seo_ai_medium2025':
                $response->rcSubscriptionName = 'Advanced';
                break;
            case 'seo_ai_large':
            case 'seo_wp_pro':
                $response->rcSubscriptionName = 'Pro';
                break;
            case 'seo_ai_social':
            case 'seo_wp_social':
                $response->rcSubscriptionName = 'Social';
                break;
            case 'annual_360':
            case 'monthly_360':
            case '360_wp_test':
            case '360_wp_test_annual':
            case 'monthly_360_eu':
            case 'annual_360_eu':
            case 'monthly_360_int':
            case 'annual_360_int':
            case 'monthly_360_us':
            case 'annual_360_us':
                $response->rcSubscriptionName = '360';
                break;
            default:
                // Fallback for unknown plans
                $response->rcSubscriptionName = 'Free';
                break;
        }

        $response->rcRemainingKeywords = $wpPluginService->getRankingCoachRemainingKeywords();

        return $response;
    }
}