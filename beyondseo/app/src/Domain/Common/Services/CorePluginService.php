<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Common\Services;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class CorePluginService
 * @package BeyondSEO\Domain\Common\Services
 */
class CorePluginService
{

    /**
     * Get the path of the plugin
     *
     * @param string $pluginName
     * @return string
     */
    public static function getPluginPath(string $pluginName = 'beyondseo'): string
    {
        if ($pluginName === 'beyondseo' && defined('RANKINGCOACH_PLUGIN_DIR')) {
            return rtrim(RANKINGCOACH_PLUGIN_DIR, '/\\');
        }

        return rtrim(dirname(RANKINGCOACH_PLUGIN_DIR), '/\\') . '/' . $pluginName;
    }
}