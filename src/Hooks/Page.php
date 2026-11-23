<?php

/**
 * Page Hook for Device Paths Plugin
 *
 * @package     librenms-device-paths
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/librenms-device-paths
 * @since       1.0.0
 */

namespace DRP\DevicePaths\Hooks;


use App\Plugins\Hooks\PageHook;
use DRP\DevicePaths\PluginProvider;
use DRP\DevicePaths\PluginData;

/**
 * Page Hook for Device Paths Plugin
 *
 * @package     librenms-device-paths
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/librenms-device-paths
 * @since       1.0.0
 */
class Page extends PageHook {
    public function data(): array {

        return PluginData::getInfo();
    }
}
