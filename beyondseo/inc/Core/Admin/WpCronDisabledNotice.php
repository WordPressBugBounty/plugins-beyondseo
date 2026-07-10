<?php
declare(strict_types=1);

namespace RankingCoach\Inc\Core\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use RankingCoach\Inc\Core\Base\BaseConstants;

/**
 * Class WpCronDisabledNotice
 *
 * Displays a dismissible admin notice when DISABLE_WP_CRON is set to true,
 * informing the site administrator that scheduled tasks cannot run and
 * explaining how to resolve the configuration.
 */
class WpCronDisabledNotice
{
    /**
     * Register the admin_notices hook.
     *
     * @return void
     */
    public function init(): void
    {
        add_action('admin_notices', [$this, 'renderNotice']);
    }

    /**
     * Render the WP Cron disabled admin notice.
     *
     * @return void
     */
    public function renderNotice(): void
    {
        if (!get_option(BaseConstants::OPTION_WP_CRON_DISABLED_NOTICE)) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        $docUrl = 'https://developer.wordpress.org/plugins/cron/hooking-wp-cron-into-the-system-task-scheduler/';
        ?>
        <div class="notice notice-warning is-dismissible">
            <p>
                <strong><?php esc_html_e('BeyondSEO: WordPress Cron is disabled', 'beyondseo'); ?></strong>
            </p>
            <p>
                <?php
                printf(
                    /* translators: %s: link to WordPress documentation */
                    esc_html__(
                        'DISABLE_WP_CRON is set to true in your wp-config.php. This prevents the plugin\'s scheduled tasks from running automatically. To fix this, remove or change define(\'DISABLE_WP_CRON\', true); in your wp-config.php, or set up a real server-side cron job. %s',
                        'beyondseo'
                    ),
                    '<a href="' . esc_url($docUrl) . '" target="_blank" rel="noopener noreferrer">' . esc_html__('Learn more', 'beyondseo') . '</a>'
                );
                ?>
            </p>
        </div>
        <?php
    }
}
