<?php

namespace DRP\DevicePaths\Hooks;

use DRP\DevicePaths\PluginProvider;
use App\Plugins\Hooks\MenuEntryHook;


/**
 * Menu Hook for Device Paths Plugin
 *
 * @package     librenms-device-paths
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/librenms-device-paths
 * @since       1.0.0
 */
class Menu extends MenuEntryHook {
    const TITLE = 'Device Path';

    public function data(): array {
        return [
            'title' => PluginProvider::TITLE,
        ];
    }
}
