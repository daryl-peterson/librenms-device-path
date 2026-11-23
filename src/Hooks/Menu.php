<?php

namespace DRP\DevicePaths\Hooks;

use App\Plugins\Hooks\MenuEntryHook;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use DRP\DevicePaths\PluginData;


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
    public function authorize(?Authenticatable $user): bool {
        return $user instanceof User && $user->hasRole('admin');
    }

    public function data(array $settings = []): array {
        $result = PluginData::getInfo();
        return $result;
    }
}
