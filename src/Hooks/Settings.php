<?php

namespace DRP\DevicePaths\Hooks;

use App\Plugins\Hooks\SettingsHook;


/**
 * Settings Hook for Device Paths Plugin
 *
 * @package     librenms-device-paths
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/librenms-device-paths
 * @since       0.0.1
 */
class Settings extends SettingsHook {

    /*
    public function data(array $settings): array {
        Log::error('Settings data: ', $settings);
        $result = [
            'info' => PluginData::getInfo(),
            //'plugin'   => PluginData::getPluginName(),
            'settings' => $settings
        ];
        Log::error('Settings result: ', $result);
        return $result;
    }
        */
}
