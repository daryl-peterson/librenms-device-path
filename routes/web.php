<?php

/**
 * LibreNMS Device Backups Web Routes.
 *
 * @package     librenms-device-backups
 * @author      Daryl Peterson <@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       1.0.0
 */

use Illuminate\Support\Facades\Route;

/**
 * Plugin imports.
 */


use DRP\DevicePaths\Controllers\PluginController;
use const DRP\DevicePaths\PLUGIN_NAME;

$plugin = PLUGIN_NAME;


/**
 * Settings route
 */
Route::middleware(['web'])
    ->get("plugin/settings/$plugin", [PluginController::class, 'settings'])
    ->name("$plugin.settings");
