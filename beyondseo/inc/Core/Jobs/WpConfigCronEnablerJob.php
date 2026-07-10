<?php
declare(strict_types=1);

namespace RankingCoach\Inc\Core\Jobs;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use RankingCoach\Inc\Core\Base\BaseConstants;
use RankingCoach\Inc\Core\Helpers\WordpressHelpers;
use Throwable;

/**
 * Class WpConfigCronEnablerJob
 *
 * Monitors the WordPress cron status at runtime. If DISABLE_WP_CRON is set to true,
 * a persistent flag is stored in the options table so that an admin notice can inform
 * the site administrator to resolve the configuration manually.
 */
class WpConfigCronEnablerJob extends ActionSchedulerClass
{
    /** @var string The ActionScheduler hook name for cron enablement */
    protected const ACTION_HOOK = 'rankingcoach_wp_cron_enabler';

    /** @var string The settings option key that controls cron enablement service */
    protected const ENABLE_SETTING_KEY = 'enable_wp_cron_service';

    /** @var int Default check interval in hours (daily) */
    protected const DEFAULT_INTERVAL_HOURS = 24;

    /** @var string Log context for cron enablement operations */
    protected const LOG_CONTEXT = 'wp_cron_enabler';

    /** @var self|null Singleton instance */
    private static ?self $instance = null;

    /**
     * Private constructor to enforce singleton pattern.
     */
    private function __construct()
    {
        parent::__construct();
    }

    /**
     * Get singleton instance.
     *
     * @return self
     */
    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Execute the WordPress cron status check.
     * This method is called by ActionScheduler when the scheduled action runs.
     *
     * @param bool $forceExecute
     * @return void
     */
    public function execute(bool $forceExecute = false): void
    {
        try {
            if (!$this->isJobEnabled()) {
                $this->log_json([
                    'operation_type' => 'wp_cron_enablement',
                    'operation_status' => 'skipped_disabled',
                    'context_entity' => 'wp_cron_enabler_job',
                    'context_type' => 'cron_management',
                    'message' => 'WP Cron enablement service disabled in settings, cleaning up schedule',
                    'timestamp' => current_time('mysql')
                ], static::LOG_CONTEXT);

                $this->unscheduleJob();
                return;
            }

            $cronStatus = $this->getCurrentCronStatus();

            update_option(BaseConstants::OPTION_WP_CRON_LAST_CHECK, current_time('mysql'));

            $this->log_json([
                'operation_type' => 'wp_cron_status_check',
                'operation_status' => 'completed',
                'context_entity' => 'wp_cron_enabler_job',
                'context_type' => 'cron_management',
                'cron_currently_enabled' => $cronStatus['enabled'],
                'disable_wp_cron_defined' => $cronStatus['constant_defined'],
                'disable_wp_cron_value' => $cronStatus['constant_value'],
                'timestamp' => current_time('mysql')
            ], static::LOG_CONTEXT);

            if (!$cronStatus['enabled']) {
                update_option(BaseConstants::OPTION_WP_CRON_DISABLED_NOTICE, true);

                $this->log_json([
                    'operation_type' => 'wp_cron_enablement',
                    'operation_status' => 'notice_set',
                    'context_entity' => 'wp_cron_enabler_job',
                    'context_type' => 'cron_management',
                    'message' => 'WP Cron is disabled via DISABLE_WP_CRON. Admin notice flag set.',
                    'timestamp' => current_time('mysql')
                ], static::LOG_CONTEXT);
            } else {
                delete_option(BaseConstants::OPTION_WP_CRON_DISABLED_NOTICE);

                $this->log_json([
                    'operation_type' => 'wp_cron_enablement',
                    'operation_status' => 'skipped_already_enabled',
                    'context_entity' => 'wp_cron_enabler_job',
                    'context_type' => 'cron_management',
                    'message' => 'WordPress cron is enabled, no action needed',
                    'timestamp' => current_time('mysql')
                ], static::LOG_CONTEXT);
            }

        } catch (Throwable $e) {
            $this->log_json([
                'operation_type' => 'wp_cron_enablement',
                'operation_status' => 'error',
                'context_entity' => 'wp_cron_enabler_job',
                'context_type' => 'cron_management',
                'error_details' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'timestamp' => current_time('mysql')
            ], static::LOG_CONTEXT);
        }
    }

    /**
     * Get current WordPress cron status by analysing runtime constants.
     *
     * @return array Status information including enabled state and constant details
     */
    private function getCurrentCronStatus(): array
    {
        $constantDefined = defined('DISABLE_WP_CRON');
        $constantValue = $constantDefined ? constant('DISABLE_WP_CRON') : null;

        $cronEnabled = !$constantDefined || $constantValue === false;

        return [
            'enabled' => $cronEnabled,
            'constant_defined' => $constantDefined,
            'constant_value' => $constantValue,
        ];
    }

    /**
     * Check additional conditions for cron enablement scheduling.
     *
     * @return bool
     */
    protected function areAdditionalConditionsMet(): bool
    {
        return WordpressHelpers::isOnboardingCompleted();
    }

    /**
     * Get current WordPress cron status for external access.
     *
     * @return array Current cron status information
     */
    public function getCronStatus(): array
    {
        return $this->getCurrentCronStatus();
    }
}
