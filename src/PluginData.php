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

use App\Models\Plugin as PluginModel;
use DRP\DevicePaths\PluginConst;
use DRP\DevicePaths\Log;
use DRP\DevicePaths\PluginSettings;

/**
 * LibreNMS Device Paths Plugin.
 *
 * @package     librenms-device-path
 * @author      Daryl Peterson <daryl.peterson@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.2
 */
class PluginData {

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
     * }
     *
     * @version 0.0.1
     */
    public static function getInfo() {

        $result = array(
            'info' => [
                'name'     => PluginConst::PLUGIN_NAME,
                'title'    => PluginConst::PLUGIN_TITLE,
                'author'   => PluginConst::PLUGIN_AUTHOR,
                'image'    => PluginConst::PLUGIN_AUTHOR_IMAGE,
                'ver'      => PluginConst::PLUGIN_VER,
                'build'    => PluginConst::PATH_BUILD_TIME
            ],
            'settings' => self::getSettings()
        );
        Log::debug('Plugin info: ', $result);
        return $result;
    }

    /**
     * Get plugin object model.
     *
     * @return PluginModel|null
     * @version 0.0.1
     */
    public static function getPluginModel(): PluginModel|null {
        $result = PluginModel::versionTwo()->where('plugin_name', PluginConst::PLUGIN_NAME)->first();

        // Check if the plugin exists in the database.
        if (is_null($result)) {
            Log::error('Plugin not found: ' . PluginConst::PLUGIN_NAME);
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
        $settings = $obj->settings;
        return $settings ?? [];
    }

    public static function getPluginName(): string {
        return PluginConst::PLUGIN_NAME;
    }
}
