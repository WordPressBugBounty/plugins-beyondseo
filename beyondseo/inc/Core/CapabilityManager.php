<?php
declare(strict_types=1);
/**
 * Capability Manager for rankingCoach SEO Plugin
 *
 * Manages WordPress capabilities for controlling access to plugin features and REST API endpoints.
 *
 * @package RankingCoach\Inc\Core
 */

namespace RankingCoach\Inc\Core;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class CapabilityManager
 *
 * Singleton class that manages custom capabilities for the rankingCoach plugin.
 * Capabilities are assigned to WordPress roles (administrator, editor, author)
 * and used to control access to plugin features and REST API endpoints.
 */
class CapabilityManager {

    /*
    |--------------------------------------------------------------------------
    | Core Capability Constants
    |--------------------------------------------------------------------------
    |
    | These capabilities control access to core plugin functionality
    | such as reading plugin data, account details, and module information.
    |
    */

    /**
     * Capability to import translated categories.
     */
    public const CAPABILITY_IMPORT_CATEGORIES = 'rankingcoach_import_categories';

    /**
     * Capability to read website pages data.
     */
    public const CAPABILITY_READ_WEBSITE_PAGES = 'rankingcoach_read_website_pages';

    /**
     * Capability to read general plugin data.
     */
    public const CAPABILITY_READ_PLUGIN_DATA = 'rankingcoach_read_plugin_data';

    /**
     * Capability to read the list of available modules.
     */
    public const CAPABILITY_READ_MODULES_LIST = 'rankingcoach_read_modules_list';

    /**
     * Capability to read account details from rankingCoach API.
     */
    public const CAPABILITY_READ_ACCOUNT_DETAILS = 'rankingcoach_read_account_details';

    /**
     * Capability to read location keywords.
     */
    public const CAPABILITY_READ_LOCATION_KEYWORDS = 'rankingcoach_read_location_keywords';

    /**
     * Capability to read specific module data.
     */
    public const CAPABILITY_READ_MODULE_DATA = 'rankingcoach_read_module_data';

    /**
     * Capability indicating user has completed onboarding.
     */
    public const CAPABILITY_MADE_ONBOARDING = 'rankingcoach_made_onboarding';

    /*
    |--------------------------------------------------------------------------
    | REST API Endpoint Capability Constants
    |--------------------------------------------------------------------------
    |
    | These capabilities control access to specific REST API endpoint categories.
    | Each endpoint category has its own capability for fine-grained access control.
    |
    */

    /**
     * Capability to access SDK/documentation endpoints.
     * Note: SDK endpoints also require valid SDK token authentication.
     */
    public const CAPABILITY_API_DOCUMENTATION = 'rankingcoach_api_documentation';

    /**
     * Capability to access integration service endpoints.
     * Note: Integration endpoints also require Application Password authentication.
     */
    public const CAPABILITY_API_INTEGRATION = 'rankingcoach_api_integration';

    /**
     * Capability to access sync service endpoints.
     * Note: Sync endpoints also require Application Password authentication.
     */
    public const CAPABILITY_API_SYNC = 'rankingcoach_api_sync';

    /**
     * Capability to access admin management endpoints.
     */
    public const CAPABILITY_API_ADMIN = 'rankingcoach_api_admin';

    /**
     * Capability to access configuration endpoints.
     */
    public const CAPABILITY_API_CONFIG = 'rankingcoach_api_config';

    /**
     * Capability to access meta tags management endpoints.
     */
    public const CAPABILITY_API_METATAGS = 'rankingcoach_api_metatags';

    /**
     * Capability to access content analysis endpoints.
     */
    public const CAPABILITY_API_CONTENT_ANALYSIS = 'rankingcoach_api_content_analysis';

    /**
     * Capability to access onboarding endpoints.
     */
    public const CAPABILITY_API_ONBOARDING = 'rankingcoach_api_onboarding';

    /**
     * Capability to access plugin information endpoints.
     */
    public const CAPABILITY_API_PLUGIN_INFO = 'rankingcoach_api_plugin_info';

    /**
     * Capability to access SEO optimiser endpoints.
     */
    public const CAPABILITY_API_OPTIMISER = 'rankingcoach_api_optimiser';

    /**
     * Capability to access social media management endpoints.
     */
    public const CAPABILITY_API_SOCIAL = 'rankingcoach_api_social';

    /**
     * Capability to manage plugin settings.
     */
    public const CAPABILITY_MANAGE_SETTINGS = 'rankingcoach_manage_settings';

    /**
     * Capability to generate breadcrumbs.
     */
    public const CAPABILITY_READ_BREADCRUMBS = 'rankingcoach_read_breadcrumbs';

    /*
    |--------------------------------------------------------------------------
    | SEO Factor Capability Constants
    |--------------------------------------------------------------------------
    |
    | These capabilities control access to specific SEO analysis factors.
    | Each factor has its own capability for granular permission control.
    |
    */

    public const CAPABILITY_SEO_FACTOR_ASSIGN_KEYWORDS_FACTOR = 'rankingcoach_factor_assign_keywords';
    public const CAPABILITY_SEO_FACTOR_CONTENT_QUALITY_AND_LENGTH_FACTOR = 'rankingcoach_factor_content_quality_and_length';
    public const CAPABILITY_SEO_FACTOR_CONTENT_READABILITY_FACTOR = 'rankingcoach_factor_content_readability';
    public const CAPABILITY_SEO_FACTOR_FIRST_PARAGRAPH_KEYWORD_USAGE_FACTOR = 'rankingcoach_factor_first_paragraph_keyword_usage';
    public const CAPABILITY_SEO_FACTOR_HEADER_TAGS_STRUCTURE_FACTOR = 'rankingcoach_factor_header_tags_structure';
    public const CAPABILITY_SEO_FACTOR_LOCAL_KEYWORDS_IN_CONTENT_FACTOR = 'rankingcoach_factor_local_keywords_in_content';
    public const CAPABILITY_SEO_FACTOR_META_DESCRIPTION_FORMAT_OPTIMIZATION_FACTOR = 'rankingcoach_factor_meta_description_format_optimization';
    public const CAPABILITY_SEO_FACTOR_META_DESCRIPTION_KEYWORDS_FACTOR = 'rankingcoach_factor_meta_description_keywords';
    public const CAPABILITY_SEO_FACTOR_META_TITLE_FORMAT_OPTIMIZATION_FACTOR = 'rankingcoach_factor_meta_title_format_optimization';
    public const CAPABILITY_SEO_FACTOR_META_TITLE_KEYWORDS_FACTOR = 'rankingcoach_factor_meta_title_keywords';
    public const CAPABILITY_SEO_FACTOR_PAGE_CONTENT_KEYWORDS_FACTOR = 'rankingcoach_factor_page_content_keywords';
    public const CAPABILITY_SEO_FACTOR_ANALYZE_BACKLINK_PROFILE_FACTOR = 'rankingcoach_factor_analyze_backlink_profile';
    public const CAPABILITY_SEO_FACTOR_FIX_BROKEN_LINKS_ON_PAGE_FACTOR = 'rankingcoach_factor_fix_broken_links_on_page';
    public const CAPABILITY_SEO_FACTOR_ALT_TEXT_TO_IMAGES_FACTOR = 'rankingcoach_factor_alt_text_to_images';
    public const CAPABILITY_SEO_FACTOR_IMAGE_OPTIMIZATION_FACTOR = 'rankingcoach_factor_image_optimization';
    public const CAPABILITY_SEO_FACTOR_OPTIMIZE_PAGE_SPEED_FACTOR = 'rankingcoach_factor_optimize_page_speed';
    public const CAPABILITY_SEO_FACTOR_OPTIMIZE_URL_STRUCTURE_FACTOR = 'rankingcoach_factor_optimize_url_structure';
    public const CAPABILITY_SEO_FACTOR_SCHEMA_MARKUP_FACTOR = 'rankingcoach_factor_schema_markup';
    public const CAPABILITY_SEO_FACTOR_SEARCH_ENGINE_INDEXATION_FACTOR = 'rankingcoach_factor_search_engine_indexation';
    public const CAPABILITY_SEO_FACTOR_USE_CANONICAL_TAGS_FACTOR = 'rankingcoach_factor_use_canonical_tags';

    /**
     * The singleton instance of the class.
     *
     * @var CapabilityManager|null
     */
    private static ?CapabilityManager $instance = null;

    /**
     * Stores registered capabilities with their descriptions.
     *
     * @var array<string, string>
     */
    private array $registered_caps = [];

    /**
     * Returns the singleton instance of the CapabilityManager class.
     *
     * @since 1.0.0
     * @return CapabilityManager Instance of CapabilityManager.
     */
    public static function instance(): CapabilityManager {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Initializes all plugin capabilities.
     *
     * Registers core capabilities, API endpoint capabilities, and SEO factor capabilities.
     * This method is called during capability assignment to ensure all capabilities
     * are properly registered before being assigned to roles.
     *
     * @since 1.0.0
     * @return void
     */
    private function initializeCapabilities(): void {
        $this->initializeCoreCapabilities();
        $this->initializeApiCapabilities();
        $this->initializeSeoFactorCapabilities();
    }

    /**
     * Initialize core plugin capabilities.
     *
     * @since 1.0.0
     * @return void
     */
    private function initializeCoreCapabilities(): void {
        $this->addCapability(self::CAPABILITY_IMPORT_CATEGORIES, 'Import translated categories');
        $this->addCapability(self::CAPABILITY_READ_WEBSITE_PAGES, 'Read website pages data');
        $this->addCapability(self::CAPABILITY_READ_PLUGIN_DATA, 'Read general plugin data');
        $this->addCapability(self::CAPABILITY_READ_MODULES_LIST, 'Read list of available modules');
        $this->addCapability(self::CAPABILITY_READ_ACCOUNT_DETAILS, 'Read rankingCoach account details');
        $this->addCapability(self::CAPABILITY_READ_LOCATION_KEYWORDS, 'Read location keywords');
        $this->addCapability(self::CAPABILITY_READ_MODULE_DATA, 'Read specific module data');
        $this->addCapability(self::CAPABILITY_MADE_ONBOARDING, 'Access features requiring onboarding completion');
    }

    /**
     * Initialize REST API endpoint capabilities.
     *
     * @since 1.0.0
     * @return void
     */
    private function initializeApiCapabilities(): void {
        $this->addCapability(self::CAPABILITY_API_DOCUMENTATION, 'Access SDK documentation endpoints');
        $this->addCapability(self::CAPABILITY_API_INTEGRATION, 'Access integration service endpoints');
        $this->addCapability(self::CAPABILITY_API_SYNC, 'Access sync service endpoints');
        $this->addCapability(self::CAPABILITY_API_ADMIN, 'Access admin management endpoints');
        $this->addCapability(self::CAPABILITY_API_CONFIG, 'Access configuration endpoints');
        $this->addCapability(self::CAPABILITY_API_METATAGS, 'Access meta tags management endpoints');
        $this->addCapability(self::CAPABILITY_API_CONTENT_ANALYSIS, 'Access content analysis endpoints');
        $this->addCapability(self::CAPABILITY_API_ONBOARDING, 'Access onboarding endpoints');
        $this->addCapability(self::CAPABILITY_API_PLUGIN_INFO, 'Access plugin information endpoints');
        $this->addCapability(self::CAPABILITY_API_OPTIMISER, 'Access SEO optimiser endpoints');
        $this->addCapability(self::CAPABILITY_API_SOCIAL, 'Access social media management endpoints');
        $this->addCapability(self::CAPABILITY_MANAGE_SETTINGS, 'Manage plugin settings');
        $this->addCapability(self::CAPABILITY_READ_BREADCRUMBS, 'Generate and read breadcrumbs');
    }

    /**
     * Initialize SEO factor capabilities.
     *
     * @since 1.0.0
     * @return void
     */
    private function initializeSeoFactorCapabilities(): void {
        $this->addCapability(self::CAPABILITY_SEO_FACTOR_ASSIGN_KEYWORDS_FACTOR, 'Access to Assign Keywords SEO operations');
        $this->addCapability(self::CAPABILITY_SEO_FACTOR_CONTENT_QUALITY_AND_LENGTH_FACTOR, 'Access to Content Quality And Length SEO operations');
        $this->addCapability(self::CAPABILITY_SEO_FACTOR_CONTENT_READABILITY_FACTOR, 'Access to Content Readability SEO operations');
        $this->addCapability(self::CAPABILITY_SEO_FACTOR_FIRST_PARAGRAPH_KEYWORD_USAGE_FACTOR, 'Access to First Paragraph Keyword Usage SEO operations');
        $this->addCapability(self::CAPABILITY_SEO_FACTOR_HEADER_TAGS_STRUCTURE_FACTOR, 'Access to Header Tags Structure SEO operations');
        $this->addCapability(self::CAPABILITY_SEO_FACTOR_LOCAL_KEYWORDS_IN_CONTENT_FACTOR, 'Access to Local Keywords In Content SEO operations');
        $this->addCapability(self::CAPABILITY_SEO_FACTOR_META_DESCRIPTION_FORMAT_OPTIMIZATION_FACTOR, 'Access to Meta Description Format SEO operations');
        $this->addCapability(self::CAPABILITY_SEO_FACTOR_META_DESCRIPTION_KEYWORDS_FACTOR, 'Access to Meta Description Keywords SEO operations');
        $this->addCapability(self::CAPABILITY_SEO_FACTOR_META_TITLE_FORMAT_OPTIMIZATION_FACTOR, 'Access to Meta Title Format SEO operations');
        $this->addCapability(self::CAPABILITY_SEO_FACTOR_META_TITLE_KEYWORDS_FACTOR, 'Access to Meta Title Keywords SEO operations');
        $this->addCapability(self::CAPABILITY_SEO_FACTOR_PAGE_CONTENT_KEYWORDS_FACTOR, 'Access to Page Content Keywords SEO operations');
        $this->addCapability(self::CAPABILITY_SEO_FACTOR_ANALYZE_BACKLINK_PROFILE_FACTOR, 'Access to Backlink Profile Analysis SEO operations');
        $this->addCapability(self::CAPABILITY_SEO_FACTOR_FIX_BROKEN_LINKS_ON_PAGE_FACTOR, 'Access to Broken Links Fix SEO operations');
        $this->addCapability(self::CAPABILITY_SEO_FACTOR_ALT_TEXT_TO_IMAGES_FACTOR, 'Access to Image Alt Text SEO operations');
        $this->addCapability(self::CAPABILITY_SEO_FACTOR_IMAGE_OPTIMIZATION_FACTOR, 'Access to Image Optimization SEO operations');
        $this->addCapability(self::CAPABILITY_SEO_FACTOR_OPTIMIZE_PAGE_SPEED_FACTOR, 'Access to Page Speed Optimization SEO operations');
        $this->addCapability(self::CAPABILITY_SEO_FACTOR_OPTIMIZE_URL_STRUCTURE_FACTOR, 'Access to URL Structure Optimization SEO operations');
        $this->addCapability(self::CAPABILITY_SEO_FACTOR_SCHEMA_MARKUP_FACTOR, 'Access to Schema Markup SEO operations');
        $this->addCapability(self::CAPABILITY_SEO_FACTOR_SEARCH_ENGINE_INDEXATION_FACTOR, 'Access to Search Engine Indexation SEO operations');
        $this->addCapability(self::CAPABILITY_SEO_FACTOR_USE_CANONICAL_TAGS_FACTOR, 'Access to Canonical Tags SEO operations');
    }

    /**
     * Registers a new capability for use within the plugin.
     *
     * @since 1.0.0
     * @param string $capability The capability identifier.
     * @param string $description Human-readable description of the capability.
     * @return void
     */
    public function addCapability(string $capability, string $description): void {
        $this->registered_caps[$capability] = $description;
    }

    /**
     * Resets capabilities by revoking and reassigning them to all roles.
     *
     * Useful for updating capabilities after plugin updates or fixing
     * capability assignment issues.
     *
     * @since 1.0.0
     * @return void
     */
    public function resetAllCapabilities(): void {
        $this->revokeCapabilities();
        $this->assignCapabilities();
    }

    /**
     * Removes all plugin capabilities from WordPress roles.
     *
     * Should be called during plugin uninstallation to clean up.
     *
     * @since 1.0.0
     * @return void
     */
    public function revokeCapabilities(): void {
        $caps_to_remove = $this->getRegisteredCaps(true);
        foreach ($this->getAvailableRoles() as $role_slug => $role_object) {
            $role_object = get_role($role_slug);
            if (!$role_object) {
                continue;
            }

            $this->applyCapabilities($caps_to_remove, 'remove_cap', $role_object);
        }
    }

    /**
     * Retrieves all registered capabilities.
     *
     * @since 1.0.0
     * @param bool $as_keys If true, returns only the capability identifiers as an indexed array.
     * @return array<string, string>|array<int, string> List of registered capabilities.
     */
    public function getRegisteredCaps(bool $as_keys = false): array {
        return $as_keys ? array_keys($this->registered_caps) : $this->registered_caps;
    }

    /**
     * Retrieves the list of WordPress roles that receive plugin capabilities.
     *
     * @since 1.0.0
     * @return array<string, \WP_Role|null> Associative array of role slugs to role objects.
     */
    private function getAvailableRoles(): array {
        return [
            'administrator' => get_role('administrator'),
            'editor'        => get_role('editor'),
            'author'        => get_role('author'),
        ];
    }

    /**
     * Applies a capability action to a WordPress role.
     *
     * @since 1.0.0
     * @param array<int, string> $capabilities List of capability identifiers.
     * @param string $action Action to apply ('add_cap' or 'remove_cap').
     * @param \WP_Role $role WordPress role object.
     * @return void
     */
    private function applyCapabilities(array $capabilities, string $action, \WP_Role $role): void {
        foreach ($capabilities as $cap) {
            $role->$action($cap);
        }
    }

    /**
     * Assigns capabilities to WordPress roles during plugin activation.
     *
     * @since 1.0.0
     * @return void
     */
    public function assignCapabilities(): void {
        $this->initializeCapabilities();

        foreach ($this->getAvailableRoles() as $role_slug => $role_object) {
            $role_object = get_role($role_slug);
            if (!$role_object) {
                continue;
            }

            $this->applyCapabilities($this->getCapabilitiesForRole($role_slug), 'add_cap', $role_object);
        }
    }

    /**
     * Retrieves the list of capabilities assigned to a specific role.
     *
     * @since 1.0.0
     * @param string $role_slug The role identifier (e.g., 'administrator', 'editor', 'author').
     * @return array<int, string> List of capability identifiers for the role.
     */
    private function getCapabilitiesForRole(string $role_slug): array {
        return match ($role_slug) {
            'administrator' => $this->getRegisteredCaps(true),
            'editor' => $this->getEditorCapabilities(),
            'author' => $this->getAuthorCapabilities(),
            default => [],
        };
    }

    /**
     * Get capabilities specific to the Editor role.
     *
     * Editors get most capabilities except administrative ones.
     *
     * @since 1.0.0
     * @return array<int, string> List of capability identifiers.
     */
    private function getEditorCapabilities(): array {
        $all_caps = $this->getRegisteredCaps(true);

        // Remove admin-only capabilities from editors
        $admin_only = [
            self::CAPABILITY_API_ADMIN,
            self::CAPABILITY_API_CONFIG,
            self::CAPABILITY_MANAGE_SETTINGS,
        ];

        return array_diff($all_caps, $admin_only);
    }

    /**
     * Get capabilities specific to the Author role.
     *
     * Authors get limited capabilities focused on content-related features.
     *
     * @since 1.0.0
     * @return array<int, string> List of capability identifiers.
     */
    private function getAuthorCapabilities(): array {
        return [
            self::CAPABILITY_READ_PLUGIN_DATA,
            self::CAPABILITY_READ_MODULES_LIST,
            self::CAPABILITY_API_CONTENT_ANALYSIS,
            self::CAPABILITY_READ_BREADCRUMBS,
            // SEO factor capabilities for content optimization
            self::CAPABILITY_SEO_FACTOR_CONTENT_QUALITY_AND_LENGTH_FACTOR,
            self::CAPABILITY_SEO_FACTOR_CONTENT_READABILITY_FACTOR,
            self::CAPABILITY_SEO_FACTOR_META_TITLE_FORMAT_OPTIMIZATION_FACTOR,
            self::CAPABILITY_SEO_FACTOR_META_DESCRIPTION_FORMAT_OPTIMIZATION_FACTOR,
            self::CAPABILITY_SEO_FACTOR_ALT_TEXT_TO_IMAGES_FACTOR,
            self::CAPABILITY_SEO_FACTOR_IMAGE_OPTIMIZATION_FACTOR,
        ];
    }

    /**
     * Checks if the current user has a specific SEO factor capability.
     *
     * @since 1.0.0
     * @param string $factorName The name of the SEO factor (e.g., 'ASSIGN_KEYWORDS').
     * @return bool True if the user has the capability, false otherwise.
     */
    public function hasFactorCapability(string $factorName): bool {
        $capability = $this->getFactorCapability($factorName);

        if ($capability === null) {
            return false;
        }

        return current_user_can($capability);
    }

    /**
     * Get the capability constant value for a specific SEO factor.
     *
     * @since 1.0.0
     * @param string $factorName The name of the SEO factor.
     * @return string|null The capability identifier or null if not found.
     */
    public function getFactorCapability(string $factorName): ?string {
        $constantName = 'CAPABILITY_SEO_FACTOR_' . strtoupper(preg_replace('/(?<!^)[A-Z]/', '_$0', $factorName));

        if (!defined(self::class . '::' . $constantName)) {
            return null;
        }

        return constant(self::class . '::' . $constantName);
    }

    /**
     * Checks if the current user has a specific core capability.
     *
     * @since 1.0.0
     * @param string $capabilityName The name of the core capability (without prefix).
     * @return bool True if the user has the capability, false otherwise.
     */
    public function hasCoreCapability(string $capabilityName): bool {
        $capability = $this->getCoreCapability($capabilityName);

        if ($capability === null) {
            return false;
        }

        return current_user_can($capability);
    }

    /**
     * Get the capability constant value for a specific core capability.
     *
     * @since 1.0.0
     * @param string $capabilityName The name of the core capability (without prefix).
     * @return string|null The capability identifier or null if not found.
     */
    public function getCoreCapability(string $capabilityName): ?string {
        $constantName = 'CAPABILITY_' . strtoupper($capabilityName);

        if (!defined(self::class . '::' . $constantName)) {
            return null;
        }

        return constant(self::class . '::' . $constantName);
    }

    /**
     * Check if current user has API endpoint capability.
     *
     * @since 1.0.0
     * @param string $endpointType The endpoint type (e.g., 'admin', 'config', 'optimiser').
     * @return bool True if the user has the capability, false otherwise.
     */
    public function hasApiCapability(string $endpointType): bool {
        $constantName = 'CAPABILITY_API_' . strtoupper($endpointType);

        if (!defined(self::class . '::' . $constantName)) {
            return false;
        }

        return current_user_can(constant(self::class . '::' . $constantName));
    }
}
