<?php
declare( strict_types=1 );

namespace RankingCoach\Inc\Core;

if ( !defined('ABSPATH') ) {
	exit;
}

use BeyondSEODeps\Doctrine\Persistence\Mapping\MappingException;
use Exception;
use RankingCoach\Inc\Core\Base\BaseConstants;
use RankingCoach\Inc\Traits\SingletonTrait;
use RankingCoach\Inc\Core\Base\Traits\RcLoggerTrait;
use stdClass;
use WP_Upgrader;
use function beyondseo_rcdc;

/**
 * Represents a system responsible for automatically updating an application
 * or software to the latest version available.
 *
 * This class is designed to handle the process of checking for updates,
 * downloading new versions, and applying updates in a seamless manner.
 */
class CustomVersionLoader {
    use RcLoggerTrait;
    use SingletonTrait;

	protected string $plugin_file;

    /**
     * Auto_Updater constructor.
     */
	public function __construct() {

        // Delete Symfony cache to reset caching after update
        add_action('upgrader_process_complete', function (WP_Upgrader $upgrader, array $options = []){
            try {
                beyondseo_rcdc();
            } catch (MappingException $e) {
                // Doing nothing if cache clearing fails
            }
        });
        // Hook to update the plugin version option after update completion
        add_action('upgrader_process_complete', [ $this, 'syncPluginVersionOnUpdate'], 10, 2);
	}

    /**
     * Ensures the plugin version in wp_options matches the actual plugin version.
     * This acts as a safety net for cases where update hooks might not fire.
     *
     * @return void
     */
    private function ensurePluginVersionSync(): void {
        try {
            $plugin_data = PluginConfiguration::getInstance()->getPluginData();
            $current_version = $plugin_data['Version'] ?? '';
            $stored_version = get_option(BaseConstants::OPTION_PLUGIN_VERSION, '');

            if (!empty($current_version) && $stored_version !== $current_version) {
                $result = update_option(BaseConstants::OPTION_PLUGIN_VERSION, $current_version);
                if (!$result && get_option(BaseConstants::OPTION_PLUGIN_VERSION) !== $current_version) {
                    $this->log("Failed to update plugin version option to '{$current_version}'", 'ERROR');
                }
            }
        } catch (Exception $e) {
            $this->log('Error ensuring plugin version sync: ' . $e->getMessage(), 'ERROR');
        }
    }

    /**
     * Syncs the plugin version to the database after a plugin update.
     * Includes validation, error handling, and only writes if version changed.
     *
     * @param WP_Upgrader $upgrader The upgrader instance
     * @param array $options Update options
     * @return void
     */
    public function syncPluginVersionOnUpdate(WP_Upgrader $upgrader, array $options = []): void {
        // Only process plugin updates
        if ($options['action'] !== 'update' || $options['type'] !== 'plugin') {
            return;
        }

        // Check if our plugin is being updated
        $plugin_basename = plugin_basename(RANKINGCOACH_PLUGIN_BASENAME);
        if (!isset($options['plugins']) || !in_array($plugin_basename, $options['plugins'], true)) {
            return;
        }

        try {
            // Get the current version from plugin header
            $plugin_data = PluginConfiguration::getInstance()->getPluginData();
            $current_version = $plugin_data['Version'] ?? '';

            if (empty($current_version)) {
                $this->log('Could not determine plugin version after update', 'WARNING');
                return;
            }

            // Get the stored version
            $stored_version = get_option(BaseConstants::OPTION_PLUGIN_VERSION, '');

            // Update if versions don't match
            if ($stored_version !== $current_version) {
                $result = update_option(BaseConstants::OPTION_PLUGIN_VERSION, $current_version);
                if (!$result && get_option(BaseConstants::OPTION_PLUGIN_VERSION) !== $current_version) {
                    $this->log("Failed to save version '{$current_version}'", 'ERROR');
                }
            }

		} catch (Exception $e) {
			$this->log('Error syncing plugin version after update: ' . $e->getMessage(), 'ERROR');
		}
	}
}
