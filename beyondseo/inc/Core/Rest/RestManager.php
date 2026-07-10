<?php
declare(strict_types=1);
/**
 * REST API Manager for rankingCoach SEO Plugin
 *
 * Manages all REST API endpoint registrations and authentication for the plugin.
 * Follows WordPress REST API best practices with explicit route registration.
 *
 * @package RankingCoach\Inc\Core\Rest
 */

namespace RankingCoach\Inc\Core\Rest;

if (!defined('ABSPATH')) {
    exit;
}

use BeyondSEO\Domain\Integrations\WordPress\Plugin\Entities\WPSettings;
use BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\ModulesResponseDto;
use BeyondSEODeps\DDD\Infrastructure\Reflection\ReflectionClass;
use BeyondSEODeps\DDD\Infrastructure\Reflection\ReflectionProperty;
use Exception;
use RankingCoach\Inc\Core\Api\User\UserApiManager;
use RankingCoach\Inc\Core\CustomVersionLoader;
use RankingCoach\Inc\Core\Base\BaseConstants;
use RankingCoach\Inc\Core\Base\Traits\RcLoggerTrait;
use RankingCoach\Inc\Core\Breadcrumbs\BreadcrumbsMultipleResponseHandler;
use RankingCoach\Inc\Core\Breadcrumbs\Dtos\BreadcrumbsRequestDto;
use RankingCoach\Inc\Core\Breadcrumbs\Dtos\BreadcrumbsResponseDto;
use RankingCoach\Inc\Core\CapabilityManager;
use RankingCoach\Inc\Core\CurrentUserManager;
use RankingCoach\Inc\Core\DB\DatabaseManager;
use RankingCoach\Inc\Core\Helpers\Attributes\RcDocumentation;
use RankingCoach\Inc\Core\Helpers\CoreHelper;
use RankingCoach\Inc\Core\Helpers\RestHelpers;
use RankingCoach\Inc\Core\Helpers\Traits\RcApiTrait;
use RankingCoach\Inc\Core\Helpers\WordpressHelpers;
use RankingCoach\Inc\Core\OpenApiGenerator;
use RankingCoach\Inc\Core\Settings\Dtos\SettingsRequestDto;
use RankingCoach\Inc\Core\Settings\Dtos\SettingsResponseDto;
use RankingCoach\Inc\Core\Settings\Dtos\SingleSettingRequestDto;
use RankingCoach\Inc\Core\Settings\Dtos\SingleSettingResponseDto;
use RankingCoach\Inc\Core\Settings\SettingsManager;
use RankingCoach\Inc\Modules\ModuleManager;
use ReflectionException;
use Throwable;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Class RestManager
 *
 * Handles all REST API endpoint registrations for the rankingCoach plugin.
 * Each endpoint category is registered explicitly with proper permission callbacks
 * using WordPress capabilities for access control.
 *
 * @since 1.0.0
 */
class RestManager extends WP_REST_Controller {

    use RcApiTrait;
    use RcLoggerTrait;

    /**
     * Prefix for download logs token options.
     *
     * @var string
     */
    private string $tokenOptionPrefix = 'rankingcoach_download_logs_token_';

    /**
     * Prefix for SDK generation token options.
     *
     * @var string
     */
    private string $sdkTokenOptionPrefix = 'rankingcoach_sdk_gen_token_';

    /**
     * Time-to-live for download tokens in seconds.
     *
     * @var int
     */
    private int $tokenTTL = 300;

    /**
     * Time-to-live for SDK tokens in seconds (10 minutes).
     *
     * @var int
     */
    private int $sdkTokenTTL = 600;

    /**
     * Constructor.
     *
     * Initializes the REST manager and registers API routes.
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->namespace = RANKINGCOACH_REST_API_BASE;

        // Register the API routes with WordPress
        add_action('rest_api_init', [$this, 'registerAllRoutes'], 5);

        // REST URL fallback filter for compatibility
        add_filter('rest_url', [$this, 'filterRestUrl'], 10, 1);
    }

    /**
     * Filter REST URL for fallback compatibility.
     *
     * @since 1.0.0
     * @param string $url The REST API URL.
     * @return string Modified URL if fallback is needed.
     */
    public function filterRestUrl(string $url): string {
        if ((bool) get_transient(RestHelpers::TRANSIENT_KEY)) {
            return trailingslashit(home_url('/index.php')) . '?rest_route=/';
        }
        return $url;
    }

    /**
     * Register all REST API routes.
     *
     * This method is the main entry point for route registration.
     * Routes are organized by category for better maintainability.
     *
     * @since 1.0.0
     * @return void
     */
    public function registerAllRoutes(): void {
        // Register Symfony proxy routes (external API integration)
        $this->registerSdkDocumentationRoutes();
        $this->registerServiceIntegrationRoutes();
        $this->registerServiceSyncRoutes();
        $this->registerAdminRoutes();
        $this->registerConfigRoutes();
        $this->registerMetatagsRoutes();
        $this->registerContentAnalysisRoutes();
        $this->registerOnboardingRoutes();
        $this->registerPluginInformationRoutes();
        $this->registerOptimiserRoutes();
        $this->registerAdvancedSettingsRoutes();
        $this->registerSocialRoutes();

        // Register legacy/native WordPress routes
        $this->registerLegacyRoutes();
    }

    /*
    |--------------------------------------------------------------------------
    | SDK Documentation Routes
    |--------------------------------------------------------------------------
    |
    | Routes for SDK/OpenAPI documentation generation.
    | Requires SDK token authentication for security.
    |
    */

    /**
     * Register SDK documentation routes.
     *
     * These endpoints provide OpenAPI/Swagger documentation for SDK generation.
     * Access requires a valid SDK token generated via the /sdk_token endpoint.
     *
     * @since 1.0.0
     * @return void
     */
    private function registerSdkDocumentationRoutes(): void {
        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/documentation',
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkSdkDocumentationPermission'],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );

        // Documentation sub-routes (e.g., /documentation/openapi.json)
        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/documentation/(?P<path>.+)',
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkSdkDocumentationPermission'],
                    'args'                => [
                        'path' => [
                            'description'       => 'Documentation sub-path',
                            'type'              => 'string',
                            'required'          => true,
                            'sanitize_callback' => 'sanitize_text_field',
                        ],
                    ],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );
    }

    /**
     * Check permission for SDK documentation endpoints.
     *
     * Validates SDK token authentication for documentation access.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @return bool|WP_Error True if authorized, WP_Error on failure.
     */
    public function checkSdkDocumentationPermission(WP_REST_Request $request): bool|WP_Error {
        if (!$this->verifySdkTokenAuth($request)) {
            return new WP_Error(
                'rest_forbidden',
                __('Invalid or missing SDK token. Generate a token via POST /sdk_token endpoint.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Service Integration Routes
    |--------------------------------------------------------------------------
    |
    | Routes for external service integrations.
    | Requires WordPress Application Password authentication.
    |
    */

    /**
     * Register integration service routes.
     *
     * These endpoints handle external service integrations (e.g., rankingCoach API).
     * Access requires WordPress Application Password authentication.
     *
     * @since 1.0.0
     * @return void
     */
    private function registerServiceIntegrationRoutes(): void {
        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/integration',
            [
                [
                    'methods'             => WP_REST_Server::ALLMETHODS,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkServiceIntegrationPermission'],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );

        // Integration sub-routes
        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/integration/(?P<path>.+)',
            [
                [
                    'methods'             => WP_REST_Server::ALLMETHODS,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkServiceIntegrationPermission'],
                    'args'                => [
                        'path' => [
                            'description'       => 'Integration endpoint path',
                            'type'              => 'string',
                            'required'          => true,
                            'sanitize_callback' => 'sanitize_text_field',
                        ],
                    ],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );
    }

    /**
     * Check permission for service integration endpoints.
     *
     * Validates Application Password authentication and capability.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @return bool|WP_Error True if authorized, WP_Error on failure.
     */
    public function checkServiceIntegrationPermission(WP_REST_Request $request): bool|WP_Error {
        // Verify Application Password authentication
        if (!$this->verifyAppPasswordAuth($request)) {
            return new WP_Error(
                'rest_forbidden',
                __('Authentication required. Use WordPress Application Password with Basic Auth.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        // Check user capability
        if (!current_user_can(CapabilityManager::CAPABILITY_API_INTEGRATION)) {
            return new WP_Error(
                'rest_forbidden',
                __('You do not have permission to access integration endpoints.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        $this->log('Integration endpoint access authenticated successfully.', 'INFO');
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Service Sync Routes
    |--------------------------------------------------------------------------
    |
    | Routes for data synchronization services.
    | Requires WordPress Application Password authentication.
    |
    */

    /**
     * Register sync service routes.
     *
     * These endpoints handle data synchronization with external services.
     * Access requires WordPress Application Password authentication.
     *
     * @since 1.0.0
     * @return void
     */
    private function registerServiceSyncRoutes(): void {
        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/sync',
            [
                [
                    'methods'             => WP_REST_Server::ALLMETHODS,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkServiceSyncPermission'],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );

        // Sync sub-routes
        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/sync/(?P<path>.+)',
            [
                [
                    'methods'             => WP_REST_Server::ALLMETHODS,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkServiceSyncPermission'],
                    'args'                => [
                        'path' => [
                            'description'       => 'Sync endpoint path',
                            'type'              => 'string',
                            'required'          => true,
                            'sanitize_callback' => 'sanitize_text_field',
                        ],
                    ],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );
    }

    /**
     * Check permission for sync service endpoints.
     *
     * Validates Application Password authentication and capability.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @return bool|WP_Error True if authorized, WP_Error on failure.
     */
    public function checkServiceSyncPermission(WP_REST_Request $request): bool|WP_Error {
        // Verify Application Password authentication
        if (!$this->verifyAppPasswordAuth($request)) {
            return new WP_Error(
                'rest_forbidden',
                __('Authentication required. Use WordPress Application Password with Basic Auth.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        // Check user capability
        if (!current_user_can(CapabilityManager::CAPABILITY_API_SYNC)) {
            return new WP_Error(
                'rest_forbidden',
                __('You do not have permission to access sync endpoints.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        $this->log('Sync endpoint access authenticated successfully.', 'INFO');
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Admin Management Routes
    |--------------------------------------------------------------------------
    |
    | Routes for plugin administration.
    | Requires nonce authentication and admin capabilities.
    |
    */

    /**
     * Register admin management routes.
     *
     * These endpoints handle administrative plugin functions.
     * Access requires nonce authentication and manage_options capability.
     *
     * @since 1.0.0
     * @return void
     */
    private function registerAdminRoutes(): void {
        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/admin',
            [
                [
                    'methods'             => WP_REST_Server::ALLMETHODS,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkAdminPermission'],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );

        // Admin sub-routes
        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/admin/(?P<path>.+)',
            [
                [
                    'methods'             => WP_REST_Server::ALLMETHODS,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkAdminPermission'],
                    'args'                => [
                        'path' => [
                            'description'       => 'Admin endpoint path',
                            'type'              => 'string',
                            'required'          => true,
                            'sanitize_callback' => 'sanitize_text_field',
                        ],
                    ],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );
    }

    /**
     * Check permission for admin endpoints.
     *
     * Validates nonce authentication and admin capability.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @return bool|WP_Error True if authorized, WP_Error on failure.
     */
    public function checkAdminPermission(WP_REST_Request $request): bool|WP_Error {
        // Verify nonce authentication
        if (!$this->verifyNonceAuth($request)) {
            return new WP_Error(
                'rest_forbidden',
                __('Invalid or missing security nonce.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        // Check admin capability
        if (!current_user_can('manage_options')) {
            return new WP_Error(
                'rest_forbidden',
                __('You do not have permission to access admin endpoints.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        // Check plugin-specific capability
        if (!current_user_can(CapabilityManager::CAPABILITY_API_ADMIN)) {
            return new WP_Error(
                'rest_forbidden',
                __('You do not have the required plugin capability.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Configuration Routes
    |--------------------------------------------------------------------------
    |
    | Routes for plugin configuration management.
    | Requires nonce authentication and admin capabilities.
    |
    */

    /**
     * Register configuration routes.
     *
     * These endpoints handle plugin configuration settings.
     * Access requires nonce authentication and manage_options capability.
     *
     * @since 1.0.0
     * @return void
     */
    private function registerConfigRoutes(): void {
        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/config',
            [
                [
                    'methods'             => WP_REST_Server::ALLMETHODS,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkConfigPermission'],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );

        // Config sub-routes
        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/config/(?P<path>.+)',
            [
                [
                    'methods'             => WP_REST_Server::ALLMETHODS,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkConfigPermission'],
                    'args'                => [
                        'path' => [
                            'description'       => 'Config endpoint path',
                            'type'              => 'string',
                            'required'          => true,
                            'sanitize_callback' => 'sanitize_text_field',
                        ],
                    ],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );
    }

    /**
     * Check permission for config endpoints.
     *
     * Validates nonce authentication and config capability.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @return bool|WP_Error True if authorized, WP_Error on failure.
     */
    public function checkConfigPermission(WP_REST_Request $request): bool|WP_Error {
        if (!$this->verifyNonceAuth($request)) {
            return new WP_Error(
                'rest_forbidden',
                __('Invalid or missing security nonce.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        if (!current_user_can('manage_options') || !current_user_can(CapabilityManager::CAPABILITY_API_CONFIG)) {
            return new WP_Error(
                'rest_forbidden',
                __('You do not have permission to access config endpoints.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Meta Tags Routes
    |--------------------------------------------------------------------------
    |
    | Routes for SEO meta tags management.
    | Requires nonce authentication and admin capabilities.
    |
    */

    /**
     * Register meta tags management routes.
     *
     * @since 1.0.0
     * @return void
     */
    private function registerMetatagsRoutes(): void {
        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/metatags',
            [
                [
                    'methods'             => WP_REST_Server::ALLMETHODS,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkMetatagsPermission'],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );

        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/metatags/(?P<path>.+)',
            [
                [
                    'methods'             => WP_REST_Server::ALLMETHODS,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkMetatagsPermission'],
                    'args'                => [
                        'path' => [
                            'description'       => 'Metatags endpoint path',
                            'type'              => 'string',
                            'required'          => true,
                            'sanitize_callback' => 'sanitize_text_field',
                        ],
                    ],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );
    }

    /**
     * Check permission for metatags endpoints.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @return bool|WP_Error True if authorized, WP_Error on failure.
     */
    public function checkMetatagsPermission(WP_REST_Request $request): bool|WP_Error {
        if (!$this->verifyNonceAuth($request)) {
            return new WP_Error(
                'rest_forbidden',
                __('Invalid or missing security nonce.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        if (!current_user_can('manage_options') || !current_user_can(CapabilityManager::CAPABILITY_API_METATAGS)) {
            return new WP_Error(
                'rest_forbidden',
                __('You do not have permission to access metatags endpoints.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Content Analysis Routes
    |--------------------------------------------------------------------------
    |
    | Routes for SEO content analysis.
    | Requires nonce authentication and appropriate capabilities.
    |
    */

    /**
     * Register content analysis routes.
     *
     * @since 1.0.0
     * @return void
     */
    private function registerContentAnalysisRoutes(): void {
        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/contentAnalysis',
            [
                [
                    'methods'             => WP_REST_Server::ALLMETHODS,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkContentAnalysisPermission'],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );

        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/contentAnalysis/(?P<path>.+)',
            [
                [
                    'methods'             => WP_REST_Server::ALLMETHODS,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkContentAnalysisPermission'],
                    'args'                => [
                        'path' => [
                            'description'       => 'Content analysis endpoint path',
                            'type'              => 'string',
                            'required'          => true,
                            'sanitize_callback' => 'sanitize_text_field',
                        ],
                    ],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );
    }

    /**
     * Check permission for content analysis endpoints.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @return bool|WP_Error True if authorized, WP_Error on failure.
     */
    public function checkContentAnalysisPermission(WP_REST_Request $request): bool|WP_Error {
        if (!$this->verifyNonceAuth($request)) {
            return new WP_Error(
                'rest_forbidden',
                __('Invalid or missing security nonce.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        // Content analysis is available to users who can edit posts
        if (!current_user_can('edit_posts') || !current_user_can(CapabilityManager::CAPABILITY_API_CONTENT_ANALYSIS)) {
            return new WP_Error(
                'rest_forbidden',
                __('You do not have permission to access content analysis.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Onboarding Routes
    |--------------------------------------------------------------------------
    |
    | Routes for plugin onboarding process.
    | Requires nonce authentication and admin capabilities.
    |
    */

    /**
     * Register onboarding routes.
     *
     * @since 1.0.0
     * @return void
     */
    private function registerOnboardingRoutes(): void {
        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/onboarding',
            [
                [
                    'methods'             => WP_REST_Server::ALLMETHODS,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkOnboardingPermission'],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );

        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/onboarding/(?P<path>.+)',
            [
                [
                    'methods'             => WP_REST_Server::ALLMETHODS,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkOnboardingPermission'],
                    'args'                => [
                        'path' => [
                            'description'       => 'Onboarding endpoint path',
                            'type'              => 'string',
                            'required'          => true,
                            'sanitize_callback' => 'sanitize_text_field',
                        ],
                    ],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );
    }

    /**
     * Check permission for onboarding endpoints.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @return bool|WP_Error True if authorized, WP_Error on failure.
     */
    public function checkOnboardingPermission(WP_REST_Request $request): bool|WP_Error {
        if (!$this->verifyNonceAuth($request)) {
            return new WP_Error(
                'rest_forbidden',
                __('Invalid or missing security nonce.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        if (!current_user_can('manage_options') || !current_user_can(CapabilityManager::CAPABILITY_API_ONBOARDING)) {
            return new WP_Error(
                'rest_forbidden',
                __('You do not have permission to access onboarding.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Plugin Information Routes
    |--------------------------------------------------------------------------
    |
    | Routes for plugin information and status.
    | Requires nonce authentication and admin capabilities.
    |
    */

    /**
     * Register plugin information routes.
     *
     * @since 1.0.0
     * @return void
     */
    private function registerPluginInformationRoutes(): void {
        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/pluginInformation',
            [
                [
                    'methods'             => WP_REST_Server::ALLMETHODS,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkPluginInformationPermission'],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );

        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/pluginInformation/(?P<path>.+)',
            [
                [
                    'methods'             => WP_REST_Server::ALLMETHODS,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkPluginInformationPermission'],
                    'args'                => [
                        'path' => [
                            'description'       => 'Plugin information endpoint path',
                            'type'              => 'string',
                            'required'          => true,
                            'sanitize_callback' => 'sanitize_text_field',
                        ],
                    ],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );
    }

    /**
     * Check permission for plugin information endpoints.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @return bool|WP_Error True if authorized, WP_Error on failure.
     */
    public function checkPluginInformationPermission(WP_REST_Request $request): bool|WP_Error {
        if (!$this->verifyNonceAuth($request)) {
            return new WP_Error(
                'rest_forbidden',
                __('Invalid or missing security nonce.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        if (!current_user_can('manage_options') || !current_user_can(CapabilityManager::CAPABILITY_API_PLUGIN_INFO)) {
            return new WP_Error(
                'rest_forbidden',
                __('You do not have permission to access plugin information.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | SEO Optimiser Routes
    |--------------------------------------------------------------------------
    |
    | Routes for SEO optimization features.
    | Requires nonce authentication, admin capabilities, and completed onboarding.
    |
    */

    /**
     * Register SEO optimizer routes.
     *
     * @since 1.0.0
     * @return void
     */
    private function registerOptimiserRoutes(): void {
        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/optimiser',
            [
                [
                    'methods'             => WP_REST_Server::ALLMETHODS,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkOptimiserPermission'],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );

        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/optimiser/(?P<path>.+)',
            [
                [
                    'methods'             => WP_REST_Server::ALLMETHODS,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkOptimiserPermission'],
                    'args'                => [
                        'path' => [
                            'description'       => 'Optimiser endpoint path',
                            'type'              => 'string',
                            'required'          => true,
                            'validate_callback' => function ($param) {
                                return is_numeric($param) && (int) $param > 0;
                            },
                            'sanitize_callback' => 'absint',
                        ],
                    ],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );
    }

    /**
     * Check permission for optimizer endpoints.
     *
     * Validates nonce, capabilities, and onboarding completion.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @return bool|WP_Error True if authorized, WP_Error on failure.
     */
    public function checkOptimiserPermission(WP_REST_Request $request): bool|WP_Error {

        if (!$this->verifyNonceAuth($request)) {
            return new WP_Error(
                'rest_forbidden',
                __('Invalid or missing security nonce.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        if (!current_user_can('manage_options') || !current_user_can(CapabilityManager::CAPABILITY_API_OPTIMISER)) {
            $this->log('Optimiser endpoint access denied: insufficient permissions', 'WARNING');
            return new WP_Error(
                'rest_forbidden',
                __('You do not have permission to access the SEO Optimiser.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        return true;
    }

    /*
|--------------------------------------------------------------------------
| Advanced Tab Settings Routes
|--------------------------------------------------------------------------
|
| Routes for advanced tab settings.
| Requires nonce authentication and admin capabilities.
|
*/

    /**
     * Register social media management routes.
     *
     * @since 1.0.0
     * @return void
     */
    private function registerAdvancedSettingsRoutes(): void {
        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/advancedSettings',
            [
                [
                    'methods'             => WP_REST_Server::ALLMETHODS,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkAdvancedSettingsPermission'],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );

        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/advancedSettings/(?P<post_id>.+)',
            [
                [
                    'methods'             => WP_REST_Server::ALLMETHODS,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkSocialPermission'],
                    'args'                => [
                        'post_id' => [
                            'description'       => 'The ID of the post to manage advanced settings for',
                            'type'              => 'integer',
                            'required'          => true,
                            'validate_callback' => function ($param) {
                                return is_numeric($param) && (int) $param > 0;
                            },
                            'sanitize_callback' => 'absint',
                        ],
                    ],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );
    }

    /**
     * Check permission for social media endpoints.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @return bool|WP_Error True if authorized, WP_Error on failure.
     */
    public function checkAdvancedSettingsPermission(WP_REST_Request $request): bool|WP_Error {
        if (!$this->verifyNonceAuth($request)) {
            return new WP_Error(
                'rest_forbidden',
                __('Invalid or missing security nonce.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        if (!current_user_can('manage_options') || !current_user_can(CapabilityManager::CAPABILITY_MANAGE_SETTINGS)) {
            return new WP_Error(
                'rest_forbidden',
                __('You do not have permission to access social media settings.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Social Media Routes
    |--------------------------------------------------------------------------
    |
    | Routes for social media optimization features.
    | Requires nonce authentication and admin capabilities.
    |
    */

    /**
     * Register social media management routes.
     *
     * @since 1.0.0
     * @return void
     */
    private function registerSocialRoutes(): void {
        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/social',
            [
                [
                    'methods'             => WP_REST_Server::ALLMETHODS,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkSocialPermission'],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );

        register_rest_route(
            RANKINGCOACH_REST_APP_BASE,
            '/social/(?P<path>.+)',
            [
                [
                    'methods'             => WP_REST_Server::ALLMETHODS,
                    'callback'            => [$this, 'handleSymfonyProxy'],
                    'permission_callback' => [$this, 'checkSocialPermission'],
                    'args'                => [
                        'path' => [
                            'description'       => 'Social media endpoint path',
                            'type'              => 'string',
                            'required'          => true,
                            'sanitize_callback' => 'sanitize_text_field',
                        ],
                    ],
                ],
                [
                    'methods'             => 'OPTIONS',
                    'callback'            => [$this, 'handleOptionsRequest'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );
    }

    /**
     * Check permission for social media endpoints.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @return bool|WP_Error True if authorized, WP_Error on failure.
     */
    public function checkSocialPermission(WP_REST_Request $request): bool|WP_Error {
        if (!$this->verifyNonceAuth($request)) {
            return new WP_Error(
                'rest_forbidden',
                __('Invalid or missing security nonce.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        if (!current_user_can('manage_options') || !current_user_can(CapabilityManager::CAPABILITY_API_SOCIAL)) {
            return new WP_Error(
                'rest_forbidden',
                __('You do not have permission to access social media settings.', 'beyondseo'),
                ['status' => rest_authorization_required_code()]
            );
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Legacy/Native WordPress Routes
    |--------------------------------------------------------------------------
    |
    | WordPress handles These routes directly without proxying to Symfony.
    |
    */

    /**
     * Register legacy WordPress routes.
     *
     * These endpoints are handled natively by WordPress and don't require
     * the Symfony proxy. Each route has explicit capability requirements.
     *
     * @since 1.0.0
     * @return void
     */
    public function registerLegacyRoutes(): void {
        // Feature modules endpoint
        register_rest_route(
            $this->namespace,
            '/feature_modules',
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'rankingcoachModules'],
                'permission_callback' => function () {
                    return current_user_can(CapabilityManager::CAPABILITY_READ_MODULES_LIST);
                },
            ]
        );

        // Account details endpoint
        register_rest_route(
            $this->namespace,
            '/account_details',
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'rankingcoachAccountDetails'],
                'permission_callback' => function () {
                    return current_user_can(CapabilityManager::CAPABILITY_READ_ACCOUNT_DETAILS);
                },
            ]
        );

        // Location keywords endpoint
        register_rest_route(
            $this->namespace,
            '/location_keywords',
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'rankingcoachLocationKeywords'],
                'permission_callback' => function () {
                    return current_user_can(CapabilityManager::CAPABILITY_READ_LOCATION_KEYWORDS);
                },
            ]
        );

        // Variables endpoint with post ID
        register_rest_route(
            $this->namespace,
            '/rc_variables/(?P<id>\d+)/data',
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'rankingcoachVariables'],
                'permission_callback' => function () {
                    return current_user_can(CapabilityManager::CAPABILITY_READ_ACCOUNT_DETAILS);
                },
                'args'                => [
                    'id' => [
                        'description'       => 'The ID of the post',
                        'type'              => 'integer',
                        'required'          => true,
                        'validate_callback' => function ($param) {
                            return is_numeric($param) && (int) $param > 0;
                        },
                        'sanitize_callback' => 'absint',
                    ],
                ],
            ]
        );

        // OpenAPI specification generator
        register_rest_route(
            $this->namespace,
            '/generate_sdk',
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'generateOpenApiSpecifications'],
                'permission_callback' => [$this, 'checkSdkDocumentationPermission'],
            ]
        );

        // SDK token generation
        register_rest_route(
            $this->namespace,
            '/sdk_token',
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'createSdkGenerationToken'],
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                },
            ]
        );

        // General settings endpoint
        register_rest_route(
            $this->namespace,
            '/settings',
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [$this, 'handleGeneralSettings'],
                    'permission_callback' => function () {
                        return current_user_can(CapabilityManager::CAPABILITY_MANAGE_SETTINGS);
                    },
                ],
                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [$this, 'handleGeneralSettings'],
                    'permission_callback' => function () {
                        return current_user_can(CapabilityManager::CAPABILITY_MANAGE_SETTINGS);
                    },
                ],
            ]
        );

        // Single setting endpoint
        register_rest_route(
            $this->namespace,
            '/settings/(?P<key>[a-zA-Z0-9_-]+)',
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [$this, 'handleSingleSetting'],
                    'permission_callback' => function () {
                        return current_user_can(CapabilityManager::CAPABILITY_MANAGE_SETTINGS);
                    },
                    'args'                => [
                        'key' => [
                            'description'       => 'The setting key to retrieve or update',
                            'type'              => 'string',
                            'required'          => true,
                            'validate_callback' => function ($param) {
                                return !empty($param) && is_string($param);
                            },
                            'sanitize_callback' => 'sanitize_key',
                        ],
                    ],
                ],
                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [$this, 'handleSingleSetting'],
                    'permission_callback' => function () {
                        return current_user_can(CapabilityManager::CAPABILITY_MANAGE_SETTINGS);
                    },
                    'args'                => [
                        'key' => [
                            'description'       => 'The setting key to update',
                            'type'              => 'string',
                            'required'          => true,
                            'validate_callback' => function ($param) {
                                return !empty($param) && is_string($param);
                            },
                            'sanitize_callback' => 'sanitize_key',
                        ],
                    ],
                ],
                [
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => [$this, 'handleSingleSetting'],
                    'permission_callback' => function () {
                        return current_user_can(CapabilityManager::CAPABILITY_MANAGE_SETTINGS);
                    },
                    'args'                => [
                        'key' => [
                            'description'       => 'The setting key to reset',
                            'type'              => 'string',
                            'required'          => true,
                            'validate_callback' => function ($param) {
                                return !empty($param) && is_string($param);
                            },
                            'sanitize_callback' => 'sanitize_key',
                        ],
                    ],
                ],
            ]
        );

        // Breadcrumbs endpoint
        register_rest_route(
            $this->namespace,
            '/breadcrumbs',
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'handleBreadcrumbs'],
                'permission_callback' => function () {
                    return current_user_can(CapabilityManager::CAPABILITY_READ_BREADCRUMBS);
                },
                'args'                => [
                    'types' => [
                        'description'       => 'Array of breadcrumb types to generate',
                        'type'              => 'array',
                        'required'          => true,
                        'validate_callback' => function ($param) {
                            return is_array($param) && !empty($param);
                        },
                        'sanitize_callback' => function ($param) {
                            return array_map('sanitize_text_field', $param);
                        },
                    ],
                ],
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Request Handlers
    |--------------------------------------------------------------------------
    */

    /**
     * Handle Symfony proxy requests.
     *
     * This method proxies REST API requests to the Symfony application
     * for endpoints that require Symfony's routing and controllers.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @return void
     */
    public function handleSymfonyProxy(WP_REST_Request $request): void {
        // Define API context constant
        if (!defined('RANKINGCOACH_API_CONTEXT')) {
            define('RANKINGCOACH_API_CONTEXT', true);
        }

        // Set JSON content type header
        header('Content-Type: application/json');

        // Set SCRIPT_FILENAME to the Symfony entry point
        // This is required for proper Symfony routing
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
        $_SERVER['SCRIPT_FILENAME'] = RANKINGCOACH_PLUGIN_APP_DIR . 'public/index.php';

        // Load Symfony runtime
        require_once RANKINGCOACH_PLUGIN_APP_DIR . 'vendor/autoload_runtime.php';
    }

    /**
     * Handle OPTIONS requests for CORS preflight.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @return WP_REST_Response Empty response with CORS headers.
     */
    public function handleOptionsRequest(WP_REST_Request $request): WP_REST_Response {
        return new WP_REST_Response(null, 200);
    }

    /*
    |--------------------------------------------------------------------------
    | Authentication Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Verify SDK token authentication.
     *
     * Validates a temporary SDK token for documentation access.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The request object.
     * @return bool True if authenticated, false otherwise.
     */
    private function verifySdkTokenAuth(WP_REST_Request $request): bool {
        // Get token from header or query parameter
        $token = $request->get_header('X-SDK-Token');
        if (empty($token)) {
            $token = $request->get_param('sdk_token');
        }

        if (empty($token)) {
            $this->log('SDK token authentication failed: Missing token', 'WARNING');
            return false;
        }

        // Validate token exists
        $tokenData = get_option($this->sdkTokenOptionPrefix . $token);
        if (!$tokenData) {
            $this->log('SDK token authentication failed: Invalid token', 'WARNING');
            return false;
        }

        // Check expiration
        if (time() > $tokenData['expires_at']) {
            delete_option($this->sdkTokenOptionPrefix . $token);
            $this->log('SDK token authentication failed: Token expired', 'WARNING');
            return false;
        }

        // Check usage count
        $maxUsageCount = 10;
        $usageCount = $tokenData['usage_count'] ?? 0;
        if ($usageCount >= $maxUsageCount) {
            delete_option($this->sdkTokenOptionPrefix . $token);
            $this->log('SDK token authentication failed: Max usage count exceeded', 'WARNING');
            return false;
        }

        // Clean up old tokens
        DatabaseManager::getInstance()->deleteOptionsByPrefix(
            $this->sdkTokenOptionPrefix,
            [$this->sdkTokenOptionPrefix . $token]
        );

        // Update usage count
        $tokenData['usage_count'] = $usageCount + 1;
        $tokenData['last_used_at'] = time();
        update_option($this->sdkTokenOptionPrefix . $token, $tokenData);

        $this->log('SDK token authentication successful', 'INFO');
        return true;
    }

    /**
     * Verify WordPress Application Password authentication.
     *
     * Validates Basic Auth credentials against WordPress Application Passwords.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The request object.
     * @return bool True if authenticated, false otherwise.
     */
    private function verifyAppPasswordAuth(WP_REST_Request $request): bool {
        // Get Authorization header
        $auth_header = $request->get_header('Authorization');
        if (empty($auth_header) || !str_starts_with($auth_header, 'Basic ')) {
            $this->log('App password authentication failed: Missing or invalid Authorization header', 'WARNING');
            return false;
        }

        // Decode credentials
        $auth = substr($auth_header, 6);
        $auth = base64_decode($auth);
        if (!$auth || !str_contains($auth, ':')) {
            $this->log('App password authentication failed: Invalid credential format', 'WARNING');
            return false;
        }

        [$username, $password] = explode(':', $auth, 2);

        if (empty($username) || empty($password)) {
            $this->log('App password authentication failed: Empty username or password', 'WARNING');
            return false;
        }

        // Verify user exists
        $user_data = get_user_by('login', $username);
        if (!$user_data) {
            wp_authenticate_application_password(null, $username, $password);
            $this->log('App password authentication failed: Invalid username', 'WARNING');
            return false;
        }

        // Authenticate using WordPress Application Passwords
        $user = wp_authenticate_application_password(null, $username, $password);

        if (is_wp_error($user)) {
            $this->log('App password authentication failed: ' . $user->get_error_message(), 'WARNING');
            return false;
        }

        // Verify basic capability
        if (!user_can($user, 'edit_posts')) {
            $this->log('App password authentication failed: User lacks required capabilities', 'WARNING');
            return false;
        }

        // Set current user
        wp_set_current_user($user->ID);

        $this->log('App password authentication successful for user: ' . $username, 'INFO');
        return true;
    }

    /**
     * Verify WordPress nonce authentication.
     *
     * Validates the REST API nonce for frontend requests.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The request object.
     * @return bool True if authenticated, false otherwise.
     */
    private function verifyNonceAuth(WP_REST_Request $request): bool {
        // Get nonce from header first
        $nonce = $request->get_header('X-WP-Nonce');
        if (empty($nonce) && isset($_SERVER['HTTP_X_WP_NONCE'])) {
            $nonce = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_WP_NONCE']));
        }

        // Fallback to body/query parameters
        if (empty($nonce)) {
            $body_params = $request->get_body_params();
            $nonce = $request->get_param('_ajax_nonce')
                ?? $request->get_param('_wpnonce')
                ?? ($body_params['_ajax_nonce'] ?? null)
                ?? ($body_params['_wpnonce'] ?? null)
                ?? null;
        }

        if (empty($nonce)) {
            return false;
        }

        // Verify nonce
        $verified = wp_verify_nonce($nonce, 'wp_rest');

        if (!$verified) {
            $this->log('Nonce verification failed', 'WARNING');
            return false;
        }

        return is_user_logged_in() && current_user_can('read');
    }

    /*
    |--------------------------------------------------------------------------
    | Callback Methods (Existing implementations)
    |--------------------------------------------------------------------------
    |
    | The following methods remain unchanged from the original implementation.
    | They handle the actual business logic for each endpoint.
    |
    */

    /**
     * Get the list of available modules.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_REST_Response
     * @throws ReflectionException
     * @throws Exception
     */
    #[RcDocumentation(
        responseDto: ModulesResponseDto::class,
        description: 'Returns the list of available modules with their names.',
        summary: 'Get the list of available modules.'
    )]
    public function rankingcoachModules(WP_REST_Request $request): WP_REST_Response {
        if ('GET' !== $request->get_method()) {
            return $this->generateErrorResponse(null, 'Method not allowed', 405);
        }

        /** @var ModuleManager $moduleManager */
        $moduleManager = ModuleManager::instance();
        try {
            $modulesData = apply_filters('rankingcoach_modules/data', $moduleManager->get_modules_names());

            return $this->generateApiResponse([
                'modules' => $modulesData,
            ]);
        } catch (Exception $e) {
            $this->log('RankingCoach Modules Error: ' . $e->getMessage(), 'ERROR');
            return $this->generateErrorResponse($e, $e->getMessage(), 500);
        }
    }

    /**
     * Get the account details.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_REST_Response
     * @throws Throwable
     */
    #[RcDocumentation(
        description: 'Returns the rankingCoach account details including user data, subscription data, and other account details.',
        summary: 'Get the current logged account details.'
    )]
    public function rankingcoachAccountDetails(WP_REST_Request $request): WP_REST_Response {
        if ('GET' !== $request->get_method()) {
            return $this->generateErrorResponse(null, 'Method not allowed', 405);
        }

        try {
            $accountDetails = UserApiManager::getInstance(bearerToken: true)->fetchAndInsertAccountData();
            return $this->generateSuccessResponse($accountDetails);
        } catch (Exception $e) {
            $this->log('RankingCoach Account Details Error: ' . $e->getMessage(), 'ERROR');
            return $this->generateErrorResponse($e, $e->getMessage(), 500);
        }
    }

    /**
     * Get the location keywords.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_REST_Response
     * @throws Throwable
     */
    #[RcDocumentation(
        description: 'Returns from rankingCoach the location keywords for the current logged account.',
        summary: 'Get the location keywords.'
    )]
    public function rankingcoachLocationKeywords(WP_REST_Request $request): WP_REST_Response {
        if ('GET' !== $request->get_method()) {
            return $this->generateErrorResponse(null, 'Method not allowed', 405);
        }

        try {
            $keywordsDetails = UserApiManager::getInstance(bearerToken: true)->fetchAndInsertAccountData();
            return $this->generateSuccessResponse($keywordsDetails);
        } catch (Exception $e) {
            $this->log('Location keywords error: ' . $e->getMessage(), 'ERROR');
            return $this->generateErrorResponse($e, $e->getMessage(), 500);
        }
    }

    /**
     * Get available variables for a post.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_REST_Response
     */
    #[RcDocumentation(
        description: 'Returns the available variables for a post.',
        summary: 'Get the available variables for a post.'
    )]
    public function rankingcoachVariables(WP_REST_Request $request): WP_REST_Response {
        if ('GET' !== $request->get_method()) {
            return $this->generateErrorResponse(null, 'Method not allowed', 405);
        }

        $postId = (int) $request->get_param('id');
        $content = get_post($postId);
        if (!$content) {
            return $this->generateErrorResponse(null, __('Invalid post ID', 'beyondseo'));
        }

        try {
            $variables = WordpressHelpers::get_available_WPVariables(['post' => $content]);
            return $this->generateSuccessResponse($variables);
        } catch (Exception $e) {
            $this->log('RankingCoach Variables Error: ' . $e->getMessage(), 'ERROR');
            return $this->generateErrorResponse($e, $e->getMessage(), 500);
        }
    }

    /**
     * Generate OpenAPI specification for the plugin.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_REST_Response|void
     */
    #[RcDocumentation(
        description: 'Generates the OpenAPI spec for registered routes. This is used for generating stores on frontend.',
        summary: 'Generate OpenAPI specification.'
    )]
    public function generateOpenApiSpecifications(WP_REST_Request $request) {
        if ('GET' !== $request->get_method()) {
            return $this->generateErrorResponse(null, 'Method not allowed', 405);
        }

        $generator = new OpenApiGenerator();
        header('Content-Type: application/json');
        echo wp_json_encode($generator->getOpenApi(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Create SDK generation token.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_REST_Response
     * @throws Throwable
     */
    #[RcDocumentation(
        description: 'Creates a temporary token for SDK generation. The token is valid for 10 minutes and allows access to documentation/OpenAPI endpoints.',
        summary: 'Create SDK Generation Token'
    )]
    public function createSdkGenerationToken(WP_REST_Request $request): WP_REST_Response {
        if ('POST' !== $request->get_method()) {
            return $this->generateErrorResponse(null, 'Method not allowed', 405);
        }

        $token = CoreHelper::generateSecureToken();
        $expires_at = time() + $this->sdkTokenTTL;

        $tokenData = [
            'expires_at'  => $expires_at,
            'created_at'  => time(),
            'usage_count' => 0,
            'created_by'  => get_current_user_id(),
        ];

        update_option($this->sdkTokenOptionPrefix . $token, $tokenData);

        $this->log('SDK generation token created for user: ' . get_current_user_id(), 'INFO');

        return $this->generateSuccessResponse([
            'data' => [
                'token'       => $token,
                'expires_at'  => gmdate('c', $expires_at),
                'ttl_seconds' => $this->sdkTokenTTL,
                'message'     => 'SDK token created successfully. Use this token with --token parameter or X-SDK-Token header.',
                'usage'       => [
                    'cli'         => 'npm run gen:SDK --base=http://your-site.local --token=' . $token,
                    'header'      => 'X-SDK-Token: ' . $token,
                    'query_param' => 'sdk_token=' . $token,
                ],
            ],
        ]);
    }

    /**
     * Apply CORS headers for REST API requests.
     *
     * @since 1.0.0
     * @param bool $allowFullCors Force applying headers regardless of configuration.
     * @return void
     */
    public static function wpFullCorsHeaders(bool $allowFullCors = false): void {
        global $wp_version;

        if (!$allowFullCors) {
            return;
        }

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
        $jwtHeaders = apply_filters('rankingcoach_jwt_auth_cors_allow_headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, Cookie');
        $restHeaders = 'Content-Type, Authorization, X-Requested-With';

        $allHeaders = $jwtHeaders . ', ' . $restHeaders;
        $split = preg_split('/[\s,]+/', $allHeaders);
        $uniqueHeaders = implode(', ', array_unique(array_filter($split)));

        if (version_compare($wp_version, '5.5.0', '>=') && !headers_sent()) {
            header(sprintf('Access-Control-Allow-Headers: %s', $uniqueHeaders));
            add_filter('rest_allowed_cors_headers', function (array $headers) use ($split) {
                return array_unique(array_merge($headers, $split));
            });
        } elseif (!headers_sent()) {
            header(sprintf('Access-Control-Allow-Headers: %s', $uniqueHeaders));
        }

        header('Access-Control-Allow-Credentials: true');

        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }

    /**
     * Register post meta fields for REST API exposure.
     *
     * @since 1.0.0
     * @return void
     */
    public function registerMetaToRestResponse(): void {
        // Schema cache meta for pages
        register_post_meta('page', BaseConstants::OPTION_SCHEMA_CACHE, [
            'show_in_rest' => true,
            'type'         => 'string',
            'single'       => true,
        ]);

        // Schema cache meta for posts
        register_post_meta('post', BaseConstants::OPTION_SCHEMA_CACHE, [
            'show_in_rest' => true,
            'type'         => 'string',
            'single'       => true,
        ]);

        // SEO title meta for posts
        register_post_meta('post', BaseConstants::META_KEY_SEO_TITLE, [
            'show_in_rest'  => true,
            'single'        => true,
            'type'          => 'string',
            'auth_callback' => function () {
                return current_user_can('edit_posts');
            },
        ]);

        // SEO description meta for posts
        register_post_meta('post', BaseConstants::META_KEY_SEO_DESCRIPTION, [
            'show_in_rest'  => true,
            'single'        => true,
            'type'          => 'string',
            'auth_callback' => function () {
                return current_user_can('edit_posts');
            },
        ]);

        // SEO title meta for pages
        register_post_meta('page', BaseConstants::META_KEY_SEO_TITLE, [
            'show_in_rest'  => true,
            'single'        => true,
            'type'          => 'string',
            'auth_callback' => function () {
                return current_user_can('edit_pages');
            },
        ]);

        // SEO description meta for pages
        register_post_meta('page', BaseConstants::META_KEY_SEO_DESCRIPTION, [
            'show_in_rest'  => true,
            'single'        => true,
            'type'          => 'string',
            'auth_callback' => function () {
                return current_user_can('edit_pages');
            },
        ]);
    }

    /**
     * Add filtered excerpt to REST API response.
     *
     * @since 1.0.0
     * @param mixed $response The response object.
     * @param mixed $post The post object.
     * @param mixed $request The request object.
     * @return mixed Modified response.
     */
    public static function addFilteredExcerptToRestResponse($response, $post, $request): mixed {
        if ($post->post_type !== 'post' && $post->post_type !== 'page' && $post->post_type !== 'attachment') {
            return $response;
        }

        if (isset($response->data['excerpt']['rendered'])) {
            $excerpt = wp_strip_all_tags($response->data['excerpt']['rendered']);
            $max_chars = 300;
            $excerpt = trim($excerpt);

            if (strlen($excerpt) > $max_chars) {
                $excerpt = mb_substr($excerpt, 0, $max_chars - 3);
                $excerpt = preg_replace('/\s+\S*$/u', '', $excerpt);
                $excerpt .= '...';
            }

            $response->data['excerpt']['filtered'] = $excerpt;
        }

        return $response;
    }

    /**
     * Handle general settings endpoint for CRUD operations
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object
     * @return WP_REST_Response
     */
    #[RcDocumentation(
        requestDto: SettingsRequestDto::class,
        responseDto: SettingsResponseDto::class,
        description: 'Handle general settings CRUD operations',
        summary: 'General Settings Management'
    )]
    public function handleGeneralSettings(WP_REST_Request $request): WP_REST_Response {
        try {
            $method = $request->get_method();
            $settingsManager = SettingsManager::instance();

            switch ($method) {
                case 'GET':
                    return $this->getGeneralSettings($settingsManager);

                case 'POST':
                    return $this->updateGeneralSettings($request, $settingsManager);

                default:
                    return $this->generateErrorResponse(
                        null,
                        __('Method not allowed', 'beyondseo'),
                        405
                    );
            }
        } catch (Exception $e) {
            $this->log('Settings endpoint error: ' . $e->getMessage(), 'ERROR');
            return $this->generateErrorResponse(
                null,
                /* translators: %s: error message */
                sprintf(__('Internal server error: %s', 'beyondseo'), $e->getMessage()),
                500
            );
        }
    }

    /**
     * Handle single setting endpoint for individual setting operations
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object
     * @return WP_REST_Response
     */
    #[RcDocumentation(
        requestDto: SingleSettingRequestDto::class,
        responseDto: SingleSettingResponseDto::class,
        description: 'Handle individual setting CRUD operations',
        summary: 'Single Setting Management'
    )]
    public function handleSingleSetting(WP_REST_Request $request): WP_REST_Response {
        try {
            $method = $request->get_method();
            $key = $request->get_param('key');
            $settingsManager = SettingsManager::instance();

            switch ($method) {
                case 'GET':
                    return $this->getSingleSetting($key, $settingsManager);

                case 'POST':
                    return $this->updateSingleSetting($request, $key, $settingsManager);

                case 'DELETE':
                    return $this->resetSingleSetting($key, $settingsManager);

                default:
                    return $this->generateErrorResponse(
                        null,
                        'Method not allowed',
                        405
                    );
            }
        } catch (Exception $e) {
            $this->log('Single setting endpoint error: ' . $e->getMessage(), 'ERROR');
            return $this->generateErrorResponse(
                null,
                /* translators: %s: error message */
                sprintf(__('Internal server error: %s', 'beyondseo'), $e->getMessage()),
                500
            );
        }
    }

    /**
     * Handle breadcrumbs endpoint for generating breadcrumbs for multiple types
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object
     * @return WP_REST_Response
     */
    #[RcDocumentation(
        requestDto: BreadcrumbsRequestDto::class,
        responseDto: BreadcrumbsResponseDto::class,
        description: 'Generate breadcrumbs for multiple context types with full customization support. Supports post types, archives, search results, 404 pages, taxonomies, and date archives. Context-aware generation based on provided parameters.',
        summary: 'Multi-context Breadcrumbs Generator'
    )]
    public function handleBreadcrumbs(WP_REST_Request $request): WP_REST_Response {
        try {
            $method = $request->get_method();
            if ($method !== 'POST') {
                return $this->generateErrorResponse(
                    null,
                    'Method not allowed. Use POST.',
                    405
                );
            }

            // Check if breadcrumbs are enabled
            $settingsManager = SettingsManager::instance();
            if (!$settingsManager->enable_breadcrumbs) {
                return $this->generateErrorResponse(
                    null,
                    'Breadcrumbs are disabled in settings',
                    403
                );
            }

            $body = $request->get_json_params();

            // Create and validate DTO
            $requestDto = BreadcrumbsRequestDto::fromArray($body ?? []);
            $validationErrors = $requestDto->validate();

            if (!empty($validationErrors)) {
                return $this->generateErrorResponse(
                    null,
                    'Request validation failed: ' . implode(', ', $validationErrors),
                    400
                );
            }

            // Process breadcrumbs using the handler
            $responseHandler = new BreadcrumbsMultipleResponseHandler();
            $responseDto = $responseHandler->processMultipleTypes($requestDto->types, $requestDto->context);

            return $this->generateSuccessResponse(
                $responseDto->toArray(),
                'Breadcrumbs generated successfully for ' . count($requestDto->types) . ' type(s)'
            );

        } catch (Exception $e) {
            $this->log('Breadcrumbs endpoint error: ' . $e->getMessage(), 'ERROR');
            return $this->generateErrorResponse(
                null,
                /* translators: %s: error message */
                sprintf(__('Internal server error: %s', 'beyondseo'), $e->getMessage()),
                500
            );
        }
    }

    /**
     * Get all general settings
     *
     * @since 1.0.0
     * @param SettingsManager $settingsManager The settings manager instance.
     * @return WP_REST_Response
     */
    private function getGeneralSettings(SettingsManager $settingsManager): WP_REST_Response {
        try {
            $settings = $settingsManager->get_options();

            // Group settings by category for better frontend consumption
            $categorizedSettings = $this->categorizeSettings($settings);

            return $this->generateSuccessResponse([
                'settings' => $settings,
                'categorized' => $categorizedSettings,
                'meta' => [
                    'total_settings' => count($settings),
                    'timestamp' => wp_date('Y-m-d H:i:s'),
                    'version' => get_option('rankingcoach_version', '1.0.0')
                ]
            ]);
        } catch (Exception $e) {
            $this->log('Error retrieving general settings: ' . $e->getMessage(), 'ERROR');
            return $this->generateErrorResponse(
                null,
                __('Failed to retrieve settings', 'beyondseo'),
                500
            );
        }
    }

    /**
     * Update general settings (bulk update)
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @param SettingsManager $settingsManager The settings manager instance.
     * @return WP_REST_Response
     */
    private function updateGeneralSettings(WP_REST_Request $request, SettingsManager $settingsManager): WP_REST_Response {
        try {
            $body = $request->get_json_params();
            if (empty($body) || !is_array($body) || !isset($body['settings']) || !is_array($body['settings'])) {
                return $this->generateErrorResponse(
                    null,
                    __('Invalid request body. Expected JSON object with settings.', 'beyondseo'),
                    400
                );
            }

            $validatedSettings = $this->validateSettings($body['settings']);
            if (is_wp_error($validatedSettings)) {
                return $this->generateErrorResponse(
                    null,
                    $validatedSettings->get_error_message(),
                    400
                );
            }

            $updatedSettings = [];
            $errors = [];

            foreach ($validatedSettings as $key => $value) {
                try {
                    $settingsManager->update_option($key, $value);
                    $updatedSettings[$key] = $value;
                } catch (Exception $e) {
                    $errors[$key] = $e->getMessage();
                    $this->log("Failed to update setting '{$key}': " . $e->getMessage(), 'ERROR');
                }
            }

            if (!empty($errors)) {
                return $this->generateErrorResponse(
                    $e ?? null,
                    'Some settings could not be updated. Reason: ' . json_encode($errors),
                    207 // Multi-Status
                );
            }

            return $this->generateSuccessResponse([
                'updated_settings' => $updatedSettings,
                'count' => count($updatedSettings),
                'timestamp' => wp_date('Y-m-d H:i:s')
            ], 'Settings updated successfully');

        } catch (Exception $e) {
            $this->log('Error updating general settings: ' . $e->getMessage(), 'ERROR');
            return $this->generateErrorResponse(
                null,
                /* translators: %s: error message */
                sprintf(__('Failed to update settings: %s', 'beyondseo'), $e->getMessage()),
                500
            );
        }
    }

    /**
     * Get a single setting
     *
     * @since 1.0.0
     * @param string $key The setting key.
     * @param SettingsManager $settingsManager The settings manager instance.
     * @return WP_REST_Response
     */
    private function getSingleSetting(string $key, SettingsManager $settingsManager): WP_REST_Response {
        try {
            if (!$this->isValidSettingKey($key)) {
                return $this->generateErrorResponse(
                    null,
                    /* translators: %s: setting key name */
                    sprintf(__('Invalid setting key: %s', 'beyondseo'), $key),
                    400
                );
            }

            $value = $settingsManager->get_option($key);

            if ($value === null) {
                return $this->generateErrorResponse(
                    null,
                    "Setting '{$key}' not found",
                    404
                );
            }

            return $this->generateSuccessResponse([
                'key' => $key,
                'value' => $value,
                'type' => gettype($value)
            ]);

        } catch (Exception $e) {
            $this->log("Error retrieving setting '{$key}': " . $e->getMessage(), 'ERROR');
            return $this->generateErrorResponse(
                null,
                __('Failed to retrieve setting', 'beyondseo'),
                500
            );
        }
    }

    /**
     * Update a single setting
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The REST request object.
     * @param string $key The setting key.
     * @param SettingsManager $settingsManager The settings manager instance.
     * @return WP_REST_Response
     */
    private function updateSingleSetting(WP_REST_Request $request, string $key, SettingsManager $settingsManager): WP_REST_Response {
        try {
            if (!$this->isValidSettingKey($key)) {
                return $this->generateErrorResponse(
                    null,
                    /* translators: %s: setting key name */
                    sprintf(__('Invalid setting key: %s', 'beyondseo'), $key),
                    400
                );
            }

            $body = $request->get_json_params();
            if (!isset($body['value'])) {
                return $this->generateErrorResponse(
                    null,
                    "Missing 'value' parameter in request body",
                    400
                );
            }

            $value = $body['value'];
            $validatedValue = $this->validateSingleSetting($key, $value);

            if (is_wp_error($validatedValue)) {
                return $this->generateErrorResponse(
                    null,
                    $validatedValue->get_error_message(),
                    400
                );
            }

            // Get previous value before updating
            $previousValue = $settingsManager->get_option($key);
            $settingsManager->update_option($key, $validatedValue);

            return $this->generateSuccessResponse([
                'key' => $key,
                'value' => $validatedValue,
                'previous_value' => $previousValue,
                'timestamp' => wp_date('Y-m-d H:i:s')
            ], "Setting '{$key}' updated successfully");

        } catch (Exception $e) {
            $this->log("Error updating setting '{$key}': " . $e->getMessage(), 'ERROR');
            return $this->generateErrorResponse(
                null,
                /* translators: %s: error message */
                sprintf(__('Failed to update setting: %s', 'beyondseo'), $e->getMessage()),
                500
            );
        }
    }

    /**
     * Reset a single setting to its default value
     *
     * @since 1.0.0
     * @param string $key The setting key.
     * @param SettingsManager $settingsManager The settings manager instance.
     * @return WP_REST_Response
     */
    private function resetSingleSetting(string $key, SettingsManager $settingsManager): WP_REST_Response {
        try {
            if (!$this->isValidSettingKey($key)) {
                return $this->generateErrorResponse(
                    null,
                    /* translators: %s: setting key name */
                    sprintf(__('Invalid setting key: %s', 'beyondseo'), $key),
                    400
                );
            }

            $defaultValue = $this->getDefaultSettingValue($key);
            if ($defaultValue === null) {
                return $this->generateErrorResponse(
                    null,
                    "No default value found for setting: {$key}",
                    400
                );
            }

            $previousValue = $settingsManager->get_option($key);
            $settingsManager->update_option($key, $defaultValue);

            $this->log("Setting '{$key}' reset to default value", 'INFO');

            return $this->generateSuccessResponse([
                'key' => $key,
                'value' => $defaultValue,
                'previous_value' => $previousValue,
                'timestamp' => wp_date('Y-m-d H:i:s')
            ], "Setting '{$key}' reset to default value");

        } catch (Exception $e) {
            $this->log("Error resetting setting '{$key}': " . $e->getMessage(), 'ERROR');
            return $this->generateErrorResponse(
                null,
                /* translators: %s: error message */
                sprintf(__('Failed to reset setting: %s', 'beyondseo'), $e->getMessage()),
                500
            );
        }
    }

    /**
     * Validate settings data
     *
     * @since 1.0.0
     * @param array $settings Array of settings to validate.
     * @return array|WP_Error Validated settings array or WP_Error on failure.
     */
    private function validateSettings(array $settings): array|WP_Error {
        $validatedSettings = [];
        $errors = [];

        foreach ($settings as $key => $value) {
            if (!$this->isValidSettingKey($key)) {
                /* translators: %s: setting key name */
                $errors[] = sprintf(__('Invalid setting key: %s', 'beyondseo'), $key);
                continue;
            }

            $validatedValue = $this->validateSingleSetting($key, $value);
            if (is_wp_error($validatedValue)) {
                /* translators: 1: setting key name, 2: error message */
                $errors[] = sprintf(__("Setting '%1\$s': %2\$s", 'beyondseo'), $key, $validatedValue->get_error_message());
                continue;
            }

            $validatedSettings[$key] = $validatedValue;
        }

        if (!empty($errors)) {
            return new WP_Error('validation_failed', implode('; ', $errors));
        }

        return $validatedSettings;
    }

    /**
     * Validate a single setting value
     *
     * @since 1.0.0
     * @param string $key The setting key.
     * @param mixed $value The value to validate.
     * @return mixed|WP_Error Validated value or WP_Error on failure.
     */
    private function validateSingleSetting(string $key, mixed $value): mixed {
        // Get the expected type and validation rules for this setting
        $validationRules = $this->getSettingValidationRules($key);

        if (empty($validationRules)) {
            // If no specific validation rules, return the value as-is
            return $value;
        }

        // Type validation
        if (isset($validationRules['type'])) {
            $expectedType = $validationRules['type'];

            switch ($expectedType) {
                case 'boolean':
                    if (!is_bool($value) && !in_array($value, [0, 1, '0', '1', true, false], true)) {
                        /* translators: %s: setting key name */
                        return new WP_Error('invalid_type', sprintf(__("Setting '%s' must be a boolean value", 'beyondseo'), $key));
                    }
                    $value = (bool) $value;
                    break;

                case 'integer':
                    if (!is_numeric($value)) {
                        /* translators: %s: setting key name */
                        return new WP_Error('invalid_type', sprintf(__("Setting '%s' must be a numeric value", 'beyondseo'), $key));
                    }
                    $value = (int) $value;
                    break;

                case 'string':
                    $value = (string) $value;
                    break;

                case 'array':
                    if (!is_array($value)) {
                        return new WP_Error('invalid_type', "Setting '{$key}' must be an array");
                    }
                    break;
            }
        }

        // Range validation for numeric values
        if (isset($validationRules['min']) && is_numeric($value) && $value < $validationRules['min']) {
            return new WP_Error('value_too_small', "Setting '{$key}' must be at least {$validationRules['min']}");
        }

        if (isset($validationRules['max']) && is_numeric($value) && $value > $validationRules['max']) {
            return new WP_Error('value_too_large', "Setting '{$key}' must not exceed {$validationRules['max']}");
        }

        // String length validation
        if (isset($validationRules['max_length']) && is_string($value) && strlen($value) > $validationRules['max_length']) {
            return new WP_Error('string_too_long', "Setting '{$key}' must not exceed {$validationRules['max_length']} characters");
        }

        // Enum validation
        if (isset($validationRules['enum']) && !in_array($value, $validationRules['enum'], true)) {
            $allowed = implode(', ', $validationRules['enum']);
            return new WP_Error('invalid_value', "Setting '{$key}' must be one of: {$allowed}");
        }

        return $value;
    }

    /**
     * Check if a setting key is valid
     *
     * @since 1.0.0
     * @param string $key The setting key to check.
     * @return bool True if valid, false otherwise.
     */
    private function isValidSettingKey(string $key): bool {
        $validKeys = $this->getValidSettingKeys();
        return in_array($key, $validKeys, true);
    }

    /**
     * Get all valid setting keys from WPSettings entity
     *
     * @since 1.0.0
     * @return array Array of valid setting keys.
     */
    private function getValidSettingKeys(): array {
        static $validKeys = null;

        if ($validKeys === null) {
            try {
                $reflection = new ReflectionClass(WPSettings::class);
                $validKeys = [];

                foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
                    $validKeys[] = strtolower($property->getName());
                }
            } catch (ReflectionException $e) {
                $this->log('Error getting valid setting keys: ' . $e->getMessage(), 'ERROR');
                $validKeys = [];
            }
        }

        return $validKeys;
    }

    /**
     * Get validation rules for a specific setting
     *
     * @since 1.0.0
     * @param string $key The setting key.
     * @return array Validation rules array.
     */
    private function getSettingValidationRules(string $key): array {
        $rules = [
            // SEO Analysis
            'seo_analysis' => ['type' => 'boolean'],
            'seo_score_threshold' => ['type' => 'integer', 'min' => 1, 'max' => 100],
            'enable_readability_check' => ['type' => 'boolean'],

            // Keyword Optimization
            'focus_keyword_limit' => ['type' => 'integer', 'min' => 1, 'max' => 20],
            'focus_keyword_analysis' => ['type' => 'boolean'],

            // Google Analytics
            'google_analytics_integration' => ['type' => 'boolean'],
            'ga_tracking_id' => ['type' => 'string', 'max_length' => 50],

            // Schema Markup
            'enable_schema_markup' => ['type' => 'boolean'],
            'default_schema_type' => ['type' => 'string', 'enum' => ['Article', 'BlogPosting', 'NewsArticle', 'WebPage']],
            'site_represents' => ['type' => 'string', 'enum' => ['organization', 'person']],
            'site_links' => ['type' => 'boolean'],
            'organisation_or_person_name' => ['type' => 'string', 'max_length' => 100],
            'organisation_email' => ['type' => 'string', 'max_length' => 100],
            'organisation_phone' => ['type' => 'string', 'max_length' => 20],
            'organisation_logo' => ['type' => 'string', 'max_length' => 255],
            'run_shortcodes' => ['type' => 'boolean'],
            'website_alternate_name' => ['type' => 'string', 'max_length' => 100],

            // Redirects and 404
            'redirect_manager' => ['type' => 'boolean'],
            'redirect_404_to_home' => ['type' => 'boolean'],
            'monitoring_404' => ['type' => 'boolean'],

            // Indexing Control
            'default_noindex_posts' => ['type' => 'boolean'],
            'default_noindex_pages' => ['type' => 'boolean'],
            'index_categories' => ['type' => 'boolean'],
            'index_tags' => ['type' => 'boolean'],

            // Social Media
            'enable_social_optimization' => ['type' => 'boolean'],
            'default_og_image' => ['type' => 'string', 'max_length' => 255],
            'default_twitter_card' => ['type' => 'string', 'enum' => ['summary', 'summary_large_image', 'app', 'player']],
            'organization_social_facebook' => ['type' => 'string', 'max_length' => 255],
            'organization_social_twitter' => ['type' => 'string', 'max_length' => 255],
            'organization_social_instagram' => ['type' => 'string', 'max_length' => 255],
            'organization_social_linkedin' => ['type' => 'string', 'max_length' => 255],
            'organization_social_youtube' => ['type' => 'string', 'max_length' => 255],
            'organization_social_tiktok' => ['type' => 'string', 'max_length' => 255],
            'organization_social_pinterest' => ['type' => 'string', 'max_length' => 255],
            'organization_social_github' => ['type' => 'string', 'max_length' => 255],
            'organization_social_snapchat' => ['type' => 'string', 'max_length' => 255],
            'organization_social_tumblr' => ['type' => 'string', 'max_length' => 255],
            'organization_social_reddit' => ['type' => 'string', 'max_length' => 255],
            'organization_social_whatsapp' => ['type' => 'string', 'max_length' => 255],
            'organization_social_telegram' => ['type' => 'string', 'max_length' => 255],
            'organization_social_mastodon' => ['type' => 'string', 'max_length' => 255],
            'organization_social_flickr' => ['type' => 'string', 'max_length' => 255],
            'organization_social_vimeo' => ['type' => 'string', 'max_length' => 255],
            'organization_social_foursquare' => ['type' => 'string', 'max_length' => 255],
            'organization_social_yelp' => ['type' => 'string', 'max_length' => 255],
            'organization_social_quora' => ['type' => 'string', 'max_length' => 255],
            'organization_social_discord' => ['type' => 'string', 'max_length' => 255],
            'organization_social_slack' => ['type' => 'string', 'max_length' => 255],
            'organization_social_wechat' => ['type' => 'string', 'max_length' => 255],
            'organization_social_weibo' => ['type' => 'string', 'max_length' => 255],
            'organization_social_line' => ['type' => 'string', 'max_length' => 255],
            'organization_social_vk' => ['type' => 'string', 'max_length' => 255],
            'organization_social_telegram_channel' => ['type' => 'string', 'max_length' => 255],
            'organization_social_telegram_group' => ['type' => 'string', 'max_length' => 255],
            'organization_social_messenger' => ['type' => 'string', 'max_length' => 255],
            'organization_social_whatsapp_group' => ['type' => 'string', 'max_length' => 255],
            'organization_social_signal' => ['type' => 'string', 'max_length' => 255],
            'organization_additional_social_urls' => ['type' => 'array', 'items' => ['type' => 'string', 'max_length' => 255]],

            // Sitemap
            'sitemap' => ['type' => 'array'],

            // Local SEO
            'enable_local_seo' => ['type' => 'boolean'],
            'default_business_type' => ['type' => 'string', 'max_length' => 50],
            'business_latitude' => ['type' => 'string', 'max_length' => 20],
            'business_longitude' => ['type' => 'string', 'max_length' => 20],

            // Internal Links
            'internal_link_suggestions' => ['type' => 'boolean'],
            'enable_breadcrumbs' => ['type' => 'boolean'],

            // Security
            'security_noopen' => ['type' => 'boolean'],
            'security_nosnippet' => ['type' => 'boolean'],

            // Performance
            'enable_lazy_loading' => ['type' => 'boolean'],
            'minify_html' => ['type' => 'boolean'],

            // Cache
            'account_details_cache_seconds' => ['type' => 'integer', 'min' => 300, 'max' => 86400],
            'gmb_categories_cache_seconds' => ['type' => 'integer', 'min' => 300, 'max' => 86400],

            // RSS
            'rss' => ['type' => 'array'],

            // Miscellaneous
            'beyondseo_comm_opt_in' => ['type' => 'boolean'],
            'enable_wp_cron_service' => ['type' => 'boolean'],
            'allow_seo_optimiser_on_saved_posts' => ['type' => 'boolean'],
            'allow_sync_keywords_to_rankingcoach' => ['type' => 'boolean'],

            // Cleanup and Uninstall
            'remove_settings_on_deactivation' => ['type' => 'boolean'],

            // Complex arrays
            'separators' => ['type' => 'array'],
            'organisation_number_of_employees' => ['type' => 'array'],
        ];

        return $rules[$key] ?? [];
    }

    /**
     * Get default value for a setting
     *
     * @since 1.0.0
     * @param string $key The setting key.
     * @return mixed|null Default value or null if not found.
     */
    private function getDefaultSettingValue(string $key): mixed {
        try {
            $reflection = new ReflectionClass(WPSettings::class);
            $property = $reflection->getProperty($key);
            return $property->getDefaultValue();
        } catch (ReflectionException $e) {
            $this->log("Error getting default value for setting '{$key}': " . $e->getMessage(), 'ERROR');
            return null;
        }
    }

    /**
     * Categorize settings for better frontend organization
     *
     * @since 1.0.0
     * @param array $settings Array of settings to categorize.
     * @return array Categorized settings array.
     */
    private function categorizeSettings(array $settings): array {
        $categories = [
            'seo' => [
                'label' => 'SEO Analysis',
                'settings' => []
            ],
            'keywords' => [
                'label' => 'Keywords',
                'settings' => []
            ],
            'analytics' => [
                'label' => 'Analytics',
                'settings' => []
            ],
            'schema' => [
                'label' => 'Schema Markup',
                'settings' => []
            ],
            'redirects' => [
                'label' => 'Redirects & 404',
                'settings' => []
            ],
            'indexing' => [
                'label' => 'Indexing',
                'settings' => []
            ],
            'social' => [
                'label' => 'Social Media',
                'settings' => []
            ],
            'sitemap' => [
                'label' => 'XML Sitemap',
                'settings' => []
            ],
            'local' => [
                'label' => 'Local SEO',
                'settings' => []
            ],
            'links' => [
                'label' => 'Internal Links',
                'settings' => []
            ],
            'security' => [
                'label' => 'Security',
                'settings' => []
            ],
            'performance' => [
                'label' => 'Performance',
                'settings' => []
            ],
            'advanced' => [
                'label' => 'Advanced',
                'settings' => []
            ]
        ];

        $categoryMapping = [
            'beyondseo_comm_opt_in' => 'advanced',
            'enable_wp_cron_service' => 'advanced',
            'allow_seo_optimiser_on_saved_posts' => 'advanced',
            'allow_sync_keywords_to_rankingcoach' => 'advanced',
            'remove_settings_on_deactivation' => 'advanced',
            'seo_analysis' => 'seo',
            'seo_score_threshold' => 'seo',
            'enable_readability_check' => 'seo',
            'focus_keyword_limit' => 'keywords',
            'focus_keyword_analysis' => 'keywords',
            'google_analytics_integration' => 'analytics',
            'ga_tracking_id' => 'analytics',
            'enable_schema_markup' => 'schema',
            'default_schema_type' => 'schema',
            'site_represents' => 'schema',
            'site_links' => 'schema',
            'organisation_or_person_name' => 'schema',
            'organisation_email' => 'schema',
            'organisation_phone' => 'schema',
            'organisation_logo' => 'schema',
            'organisation_founding_date' => 'schema',
            'organisation_number_of_employees' => 'schema',
            'run_shortcodes' => 'schema',
            'website_alternate_name' => 'schema',
            'person_manual_name' => 'schema',
            'person_manual_image' => 'schema',
            'organization_social_facebook' => 'social',
            'organization_social_twitter' => 'social',
            'organization_social_instagram' => 'social',
            'organization_social_linkedin' => 'social',
            'organization_social_youtube' => 'social',
            'organization_social_tiktok' => 'social',
            'organization_social_pinterest' => 'social',
            'organization_social_github' => 'social',
            'organization_social_tumblr' => 'social',
            'organization_social_snapchat' => 'social',
            'organization_social_wikipedia' => 'social',
            'organization_social_personal_website' => 'social',
            'organization_additional_social_urls' => 'social',
            'redirect_manager' => 'redirects',
            'redirect_404_to_home' => 'redirects',
            'monitoring_404' => 'redirects',
            'default_noindex_posts' => 'indexing',
            'default_noindex_pages' => 'indexing',
            'index_categories' => 'indexing',
            'index_tags' => 'indexing',
            'enable_social_optimization' => 'social',
            'default_og_image' => 'social',
            'default_twitter_card' => 'social',
            'sitemap' => 'sitemap',
            'enable_local_seo' => 'local',
            'default_business_type' => 'local',
            'business_latitude' => 'local',
            'business_longitude' => 'local',
            'internal_link_suggestions' => 'links',
            'enable_breadcrumbs' => 'links',
            'security_noopen' => 'security',
            'security_nosnippet' => 'security',
            'enable_lazy_loading' => 'performance',
            'minify_html' => 'performance',
        ];

        foreach ($settings as $key => $value) {
            $category = $categoryMapping[$key] ?? 'advanced';
            $categories[$category]['settings'][$key] = $value;
        }

        // Remove empty categories
        return array_filter($categories, fn($category) => !empty($category['settings']));
    }

    /**
     * Get the legacy API base namespace.
     *
     * @since 1.0.0
     * @return string The legacy API base.
     */
    private function getLegacyApiBase(): string {
        return $this->namespace;
    }
}
