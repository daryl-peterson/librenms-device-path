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

    // override the data function to add additional data to be accessed in the view
    // inside the blade, all variables will be named based on the key in the returned array
    public function data(array $settings = []): array {
        // inject settings and count how many we have so we can display it in the menu

        return [
            'info' => PluginData::getInfo(),
        ];
    }
}
