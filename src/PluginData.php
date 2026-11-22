<?php

/**
 * * LibreNMS Device Paths Plugin.
 *
 * @package     librenms-device-path
 * @author      Daryl Peterson <daryl.peterson@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DevicePaths;

/**
 * Laravel and application imports.
 */

use App\Models\Plugin as PluginModel;

/**
 * Plugin imports.
 */

use DRP\DevicePaths\Log;
use DRP\DevicePaths\PluginSettings;
use DRP\DevicePaths\PluginCache;

/**
 * LibreNMS Device Paths Plugin.
 *
 * @package     librenms-device-path
 * @author      Daryl Peterson <daryl.peterson@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class PluginData {

    /**
     * Constructor.
     *
     * @since 0.0.1
     */
    public function __construct() {
        # Code Here
    }


    /**
     * Get plugin information.
     *
     * @return array{
     *  name: '',
     *  title: '',
     *  author: '',
     *  ver: '',
     *  image: '',
     *  settings: array,
     *  dbStatus: array{
     *    ready: bool,
     *    error: string|null
     *  },
     * }
     *
     * @version 0.0.1
     */
    public static function getInfo() {
        //$dbError = PluginCache::get(CACHE_DB_ERROR);

        $result = array(
            'name'     => PLUGIN_NAME,
            'title'    => PLUGIN_TITLE,
            'author'   => PLUGIN_AUTHOR,
            'ver'      => PLUGIN_VER,
            'image'    => 'https://avatars.githubusercontent.com/u/13834451?s=400&u=ff8417db6126da8d9ff82822ea0be5897ad744b3&v=4',
            'settings' => self::getSettings(),
            /*
            'dbStatus'  => [
                'ready' => !$dbError,
                'error' => $dbError
            ],
            */
        );

        return $result;
    }

    /**
     * Get plugin object model.
     *
     * @return PluginModel|null
     * @version 0.0.1
     */
    public static function getPluginModel(): PluginModel|null {
        $result = PluginModel::versionTwo()->where('plugin_name', PLUGIN_NAME)->first();

        // Check if the plugin exists in the database.
        if (is_null($result)) {
            Log::error('Plugin not found: ' . PLUGIN_NAME);
            return null;
        }
        return $result;
    }

    /**
     * Get plugin settings.
     *
     * @return array
     * @version 0.0.1
     */
    public static function getSettings(): array {
        $obj = new PluginSettings();
        $settings = $obj->all();
        return $settings ?? [];
    }

    public static function getPluginName(): string {
        return PLUGIN_NAME;
    }
}
