<?php
declare(strict_types=1);

namespace RankingCoach\Inc\Core\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use RankingCoach\Inc\Core\Helpers\CoreHelper;
use RankingCoach\Inc\Core\Helpers\WordpressHelpers;
use RankingCoach\Inc\Core\Settings\SettingsManager;

/**
 * Dismissible admin notice linking to the setup wizard. Shown on the Dashboard, the Plugins page
 * and the plugin's own pages while the wizard is neither completed nor dismissed and the site is
 * not connected yet. Dismissing stores setup_wizard.dismissed, the same flag the wizard's X sets.
 */
class SetupWizardNotice
{
    private const NOTICE_ID = 'rankingcoach-setup-wizard-notice';

    private const DISMISS_ACTION = 'rankingcoach_dismiss_setup_wizard_notice';

    public function init(): void
    {
        add_action('admin_notices', [$this, 'renderNotice']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueDismissScript']);
        add_action('wp_ajax_' . self::DISMISS_ACTION, [$this, 'handleDismiss']);
    }

    private function shouldShow(): bool
    {
        global $pagenow;

        if (is_network_admin() || !current_user_can('manage_options')) {
            return false;
        }

        $page = (string) WordpressHelpers::sanitize_input('GET', 'page');
        $onDashboard = $pagenow === 'index.php' && $page === '';
        $onPlugins = $pagenow === 'plugins.php';
        $onPluginPage = str_starts_with($page, 'rankingcoach-') && $page !== AdminManager::PAGE_SETUP_WIZARD;
        if (!$onDashboard && !$onPlugins && !$onPluginPage) {
            return false;
        }

        if (CoreHelper::isOnboarded()) {
            return false;
        }

        $wizard = (array) SettingsManager::instance()->get_option('setup_wizard', []);
        return empty($wizard['completed']) && empty($wizard['dismissed']);
    }

    public function renderNotice(): void
    {
        if (!$this->shouldShow()) {
            return;
        }
        ?>
        <div id="<?php echo esc_attr(self::NOTICE_ID); ?>"
             class="notice notice-info is-dismissible"
             data-security="<?php echo esc_attr(wp_create_nonce(self::DISMISS_ACTION)); ?>">
            <p>
                <?php
                printf(
                    /* translators: %s: plugin brand name wrapped in bold tags */
                    esc_html__('Finish setting up %s: the setup wizard checks your pages and configures the SEO basics in a few minutes.', 'beyondseo'),
                    '<b>' . esc_html(RANKINGCOACH_BRAND_NAME) . '</b>'
                );
                ?>
            </p>
            <p>
                <a class="button button-primary" href="<?php echo esc_url(AdminManager::getPageUrl(AdminManager::PAGE_SETUP_WIZARD)); ?>">
                    <?php esc_html_e('Open setup wizard', 'beyondseo'); ?>
                </a>
            </p>
        </div>
        <?php
    }

    public function enqueueDismissScript(): void
    {
        if (!$this->shouldShow()) {
            return;
        }

        $script = "
            ;(function($) {
                $( '#" . self::NOTICE_ID . "' ).on( 'click', '.notice-dismiss', function() {
                    let notice = $( this ).parent();

                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: '" . self::DISMISS_ACTION . "',
                            security: notice.data('security') || ''
                        },
                        error: function() {
                            console.log('Error dismissing notification');
                        }
                    });
                });
            })(jQuery);";

        wp_register_script('rankingcoach-setup-wizard-notice-dismiss', '', ['jquery'], RANKINGCOACH_VERSION, true);
        wp_enqueue_script('rankingcoach-setup-wizard-notice-dismiss');
        wp_add_inline_script('rankingcoach-setup-wizard-notice-dismiss', $script);
    }

    public function handleDismiss(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'message' => __('You do not have sufficient permissions to perform this action.', 'beyondseo'),
            ]);
            return;
        }

        $nonce = WordpressHelpers::sanitize_input('POST', 'security');
        if (!wp_verify_nonce($nonce, self::DISMISS_ACTION)) {
            wp_send_json_error([
                'message' => __('Security check failed. Please try again.', 'beyondseo'),
            ]);
            return;
        }

        $settings = SettingsManager::instance();
        $wizard = (array) $settings->get_option('setup_wizard', []);
        $settings->update_option('setup_wizard', [
            'completed' => !empty($wizard['completed']),
            'dismissed' => true,
        ]);

        wp_send_json_success([
            'message' => __('Notification dismissed successfully.', 'beyondseo'),
        ]);
    }
}
