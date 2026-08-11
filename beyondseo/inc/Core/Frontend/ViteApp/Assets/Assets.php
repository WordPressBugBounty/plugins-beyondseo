<?php

namespace RankingCoach\Inc\Core\Frontend\ViteApp\Assets;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use RankingCoach\Inc\Core\ChannelFlow\OptionStore;
use RankingCoach\Inc\Core\Frontend\ViteApp\ReactApp;
use RankingCoach\Inc\Core\Helpers\CoreHelper;
use RankingCoach\Inc\Core\Helpers\WordpressHelpers;
use RankingCoach\Inc\Core\Base\BaseConstants;
use RankingCoach\Inc\Core\Plugin\RankingCoachPlugin;
use RankingCoach\Inc\Core\TokensManager;
use RankingCoach\Inc\Exceptions\HttpApiException;
use ReflectionException;
use Throwable;

class Assets {
    use Resolver;

    /**
     * @action elementor/editor/after_enqueue_scripts
     */
    public function frontElementor( string $pages ): void {

        // Enqueue all available styles and scripts
        $dependencies = $this->solveDependencies( ['main.tsx', 'App.tsx'] );
        $dependencies = array_unique($dependencies);
        foreach ( $dependencies as $idx => $dependency ) {
            wp_enqueue_style( 'rankingcoach-vite-app-css-' . $idx, $dependency, [], ReactApp::get()?->config()->get( 'version' ) );
        }

        // Enqueue the main script and localize the data
        wp_enqueue_script( 'rankingcoach-react-vite-script', $this->resolve( 'main.tsx' ), ['wp-i18n'], ReactApp::get()?->config()->get( 'version' ), true );

        // Load translations for the script (must be called AFTER wp_enqueue_script)
        wp_set_script_translations(
            'rankingcoach-react-vite-script',
            'beyondseo',
            plugin_dir_path(RANKINGCOACH_FILE) . 'languages/json'
        );

        $locale = WordpressHelpers::get_wp_locale();
        $localeParts = preg_split('/[_-]/', $locale);
        $countryShortCode = strtoupper($localeParts[1] ?? $localeParts[0]);

        $rankingCoachReactData = [
            'loadNextComponents' => implode(',', ReactApp::$loadComponents),
            'currentUserId' => get_current_user_id(),
            'locale' => $locale,
            'version' => RANKINGCOACH_VERSION,
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'baseurl' => get_site_url(),
            'adminurl' => admin_url( 'admin.php' ),
            'endpoint' => esc_url_raw( rest_url( RANKINGCOACH_REST_API_BASE ) ),
            'appEndpoint' => esc_url_raw( rest_url( RANKINGCOACH_REST_APP_BASE ) ),
            'security' => wp_create_nonce( 'rankingcoach-ajax-nonce' ),
            'restNonce' => ( wp_installing() && ! is_multisite() ) ? '' : wp_create_nonce( 'wp_rest' ),
            'isEditingPost' => false,
            'isAddingPost' => false,
            'currentPostType' => '',
            'currentPostId' => null,
            'isUpVersion' => RankingCoachPlugin::isUpVersion(),
            'subscriptionVersion' => get_option(BaseConstants::OPTION_RANKINGCOACH_SUBSCRIPTION, null),
            'upgradablePlans' => get_option(BaseConstants::OPTION_RANKINGCOACH_UPGRADE_PLANS, null),
            'isActivationCompleted' => WordpressHelpers::isActivationCompleted(),
            'isOnboardingCompleted' => WordpressHelpers::isOnboardingCompleted(),
            'brandName' => RANKINGCOACH_BRAND_NAME,
            'brandSlug' => RANKINGCOACH_BRAND_SLUG,
            'countryShortCode' => $countryShortCode,
            'pluginInformation' => $this->getPluginInformationData()
        ];

        if (WordpressHelpers::is_edit_post_context()) {
            // Get the post ID (if editing an existing post).
            $post_id = WordpressHelpers::sanitize_input( 'GET', 'post' );
            if ( $post_id ) {
                $post_type = get_post_type( $post_id );
            } else {
                $post_type = null;
            }
            // Add context-specific keys to the global object when editing a post or page.
            $rankingCoachReactData['isEditingPost'] = true;
            $rankingCoachReactData['currentPostType'] = $post_type;
            $rankingCoachReactData['currentPostId'] = $post_id;
        }

        // Localize the script with data
        wp_localize_script( 'rankingcoach-react-vite-script', 'rankingCoachReactData', $rankingCoachReactData);
    }

    /**
     * @action admin_enqueue_scripts
     * @throws Throwable if script localization fails
     */
    public function front( string $pages ): void {
        // Prevent duplicate execution
        static $executed = [];
        
        if ( isset( $executed[$pages] ) ) {
            return;
        }
        
        // avoid it to load the assets in other pages
        if ( ! in_array( $pages, ALLOWED_RANKINGCOACH_PAGES ) ) {
            return;
        }
        
        // Mark as executed for this page
        $executed[$pages] = true;

        // Enqueue all available styles and scripts
        $dependencies = $this->solveDependencies( ['main.tsx', 'App.tsx'] );
        $dependencies = array_unique($dependencies);
        foreach ( $dependencies as $idx => $dependency ) {
            wp_enqueue_style( 'rankingcoach-vite-app-css-' . $idx, $dependency, [], ReactApp::get()?->config()->get( 'version' ) );
        }

        // Enqueue the editor script only if we are in the context of adding or editing a post
        if(WordpressHelpers::is_add_post_context() || WordpressHelpers::is_edit_post_context()) {
            wp_enqueue_script('rankingcoach-react-editor-script', plugin_dir_url(RANKINGCOACH_PLUGIN_ADMIN_DIR) . 'Admin/assets/js/admin-editor.js', ['wp-blocks', 'wp-editor', 'wp-data'], RANKINGCOACH_VERSION, true);
        }

        // Enqueue the main script and localize the data
        wp_enqueue_script( 'rankingcoach-react-vite-script', $this->resolve( 'main.tsx' ), ['wp-i18n'], ReactApp::get()?->config()->get( 'version' ), true );

        // Load translations for the script (must be called AFTER wp_enqueue_script)
        wp_set_script_translations(
            'rankingcoach-react-vite-script',
            'beyondseo',
            plugin_dir_path(RANKINGCOACH_FILE) . 'languages/json'
        );

        $locale = WordpressHelpers::get_wp_locale();
        $localeParts = preg_split('/[_-]/', $locale);
        $countryShortCode = strtoupper($localeParts[1] ?? $localeParts[0]);
        $channel = (new OptionStore())->getChannel();

        $registrationOptions = [
            'ionos'      => ['showEmail' => false, 'showActivation' => true],
            'direct'     => ['showEmail' => true,  'showActivation' => true],
            'extendify'  => ['showEmail' => true,  'showActivation' => true],
        ];

        $activeRegistrationOptions = $registrationOptions[$channel] ?? ['showEmail' => true, 'showActivation' => true];
        $showEmail = $this->boolToJs($activeRegistrationOptions['showEmail']);
        $showActivation = $this->boolToJs($activeRegistrationOptions['showActivation']);

        $rankingCoachReactData = [
            'loadNextComponents' => implode(',', ReactApp::$loadComponents),
            'currentUserId' => get_current_user_id(),
            'locale' => $locale,
            'version' => RANKINGCOACH_VERSION,
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'baseurl' => get_site_url(),
            'adminurl' => admin_url( 'admin.php' ),
            'endpoint' => esc_url_raw( rest_url( RANKINGCOACH_REST_API_BASE ) ),
            'appEndpoint' => esc_url_raw( rest_url( RANKINGCOACH_REST_APP_BASE ) ),
            'security' => wp_create_nonce( 'rankingcoach-ajax-nonce' ),
            'restNonce' => ( wp_installing() && ! is_multisite() ) ? '' : wp_create_nonce( 'wp_rest' ),
            'isEditingPost' => false,
            'currentPostType' => '',
            'currentPostId' => null,
            'isUpVersion' => RankingCoachPlugin::isUpVersion(),
            'subscriptionVersion' => get_option(BaseConstants::OPTION_RANKINGCOACH_SUBSCRIPTION, null),
            'upgradablePlans' => get_option(BaseConstants::OPTION_RANKINGCOACH_UPGRADE_PLANS, null),
            'isActivationCompleted' => WordpressHelpers::isActivationCompleted(),
            'isOnboardingCompleted' => $this->boolToJs(WordpressHelpers::isOnboardingCompleted()),
            'brandName' => RANKINGCOACH_BRAND_NAME,
            'brandSlug' => RANKINGCOACH_BRAND_SLUG,
            'iframeMapUrl' => esc_url_raw($this->generateMapUrl()),
            'countryShortCode' => $countryShortCode,
            'channel' => $channel,
            'registrationShowEmail' => $showEmail,
            'registrationShowActivation' => $showActivation,
            'pluginInformation' => $this->getPluginInformationData(),
        ];

        $post_id = WordpressHelpers::sanitize_input('GET', 'post');
        $post_type = $post_id ? get_post_type($post_id) : null;

        if (WordpressHelpers::is_edit_post_context()) {
            // Get the post ID (if editing an existing post).
            // Add context-specific keys to the global object when editing a post or page.
            $rankingCoachReactData['isEditingPost'] = true;
            $rankingCoachReactData['isAddingPost'] = false;
            $rankingCoachReactData['currentPostType'] = $post_type;
            $rankingCoachReactData['currentPostId'] = $post_id;
        }

        if(WordpressHelpers::is_add_post_context()) {
            // Add context-specific keys to the global object when adding a new post or page.
            $rankingCoachReactData['isAddingPost'] = true;
            $rankingCoachReactData['currentPostType'] = $post_type;
        }

        // Localize the script with data
	    wp_localize_script( 'rankingcoach-react-vite-script', 'rankingCoachReactData', $rankingCoachReactData);
    }

    private function boolToJs(bool $value): string {
        return $value ? 'true' : 'false';
    }

    /** Languages supported, each with its default locale */
    private const MAP_LANGUAGES = [
        'de' => 'de-de',
        'en' => 'en-us',
        'es' => 'es-es',
        'fr' => 'fr-fr',
        'it' => 'it-it',
        'nl' => 'nl-nl',
        'pl' => 'pl-pl',
        'pt' => 'pt-pt',
    ];

    private const DEFAULT_MAP_LOCALE = 'en-us';

    /**
     * Build the `pluginInformation` payload for the localized `rankingCoachReactData`
     * window object.
     *
     * The React app (metabox, settings and connect/upsell areas) reads plugin
     * information from this window object and only falls back to the
     * `pluginInformation` REST endpoint when it is absent. Exposing it here makes
     * those areas autonomous and removes an API round-trip on page load.
     *
     * The payload is assembled entirely from WordPress options / `inc` helpers via
     * CoreHelper::getPluginInformation() — it does not touch the app/src (DDD/Symfony)
     * layer. On any failure we return null so asset loading never breaks; the React
     * app then transparently falls back to the API call.
     *
     * @return array|null
     */
    private function getPluginInformationData(): ?array {
        try {
            return CoreHelper::getPluginInformation();
        } catch ( Throwable $e ) {
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( '[rankingcoach] Failed to build pluginInformation for window: ' . $e->getMessage() );
            }
            return null;
        }
    }

    /**
     * Convert a WordPress locale (en_US, de_DE_formal, ...) to a map URL locale,
     * falling back to en-us when the language is not supported.
     *
     * @param string $locale
     * @return string
     */
    private function formatLocaleForMapUrl(string $locale): string {
        $parts = preg_split('/[_-]/', strtolower(trim($locale))) ?: [];
        $lang = $parts[0] ?? '';
        $region = $parts[1] ?? '';

        if (!isset(self::MAP_LANGUAGES[$lang])) {
            return self::DEFAULT_MAP_LOCALE;
        }

        if (preg_match('/^[a-z]{2}$/', $region)) {
            return $lang . '-' . $region;
        }

        return self::MAP_LANGUAGES[$lang];
    }

    /**
     * Iframe MAP URL Generator
     * @param bool $reload
     * @return string
     * @throws HttpApiException
     * @throws ReflectionException
     */
    private function generateMapUrl(bool $reload = false): string {
        // Load the configuration
        $config = require RANKINGCOACH_PLUGIN_DIR . 'inc/Core/Api/externalIntegrations.php';
        $rawLocale = WordpressHelpers::get_wp_locale() ?? 'en_US';
        $locale = $this->formatLocaleForMapUrl($rawLocale);

        /** @var TokensManager $tokensManager */
        $tokensManager = TokensManager::getInstance();
        $accessToken = $tokensManager->getStoredAccessToken();
        if($reload) {
            $refreshToken = $tokensManager->getStoredRefreshToken();
            if(!empty($refreshToken)) {
                $tokensManager->generateAndSaveAccessToken($refreshToken);
            }
            $accessToken = $tokensManager->getStoredAccessToken();
        }

        $projectBase = $config[RankingCoachPlugin::isProductionMode() ? 'liveEnv' : 'devEnv'];
        $projectId = WordpressHelpers::getProjectId();
        return sprintf( $config['iframeMapUrl'], $projectBase, $projectId, $accessToken, $projectBase, $locale );
    }
}
