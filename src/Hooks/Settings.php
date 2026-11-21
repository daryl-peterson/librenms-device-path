<?php

namespace DRP\DevicePaths\Hooks;

use App\Plugins\Hooks\SettingsHook;
use DRP\DevicePaths\PluginProvider;


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
    public function data(array $settings): array {
        return parent::data(PluginProvider::withDefaultSettings($settings));
    }
}
