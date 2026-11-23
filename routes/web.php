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

use App\Http\Controllers\PluginSettingsController;
use Illuminate\Support\Facades\Route;
use DRP\DevicePaths\Controllers\PluginController;
use DRP\DevicePaths\PluginConst;

$plugin = PluginConst::PLUGIN_NAME;


/**
 * Settings routes
 */
Route::middleware(['web', 'auth', 'can:plugin.admin'])->group(function () use ($plugin): void {
    Route::get("plugin/$plugin/settings", [PluginController::class, 'settings'])
        ->name("$plugin.settings");

    Route::post('plugin/{plugin:plugin_name}/settings', [PluginSettingsController::class, 'update'])
        ->where('plugin', $plugin)
        ->name("$plugin.update");
});
