<?php

namespace DRP\DevicePaths;

/**
 * Laravel and application imports.
 */


use App\Models\Device;
use App\Models\Plugin;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use LibreNMS\Interfaces\Plugins\Hooks\DeviceOverviewHook;
use LibreNMS\Interfaces\Plugins\Hooks\MenuEntryHook as MenuEntryHookInterface;
use LibreNMS\Interfaces\Plugins\Hooks\SettingsHook as SettingsHookInterface;
use LibreNMS\Interfaces\Plugins\Hooks\SinglePageHook;
use LibreNMS\Interfaces\Plugins\PluginManagerInterface;

/**
 * Plugin imports.
 */

use DRP\DevicePaths\Hooks\DeviceOverview;
use DRP\DevicePaths\Hooks\Menu;
use DRP\DevicePaths\Hooks\Page;
use DRP\DevicePaths\Hooks\Settings;
use DRP\DevicePaths\Log;


/**
 * LibreNMS Device Paths Plugin Service Provider.
 *
 * @package     librenms-device-paths
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class PluginProvider extends ServiceProvider {

    const PATH_KEY        = 'DEVP_';
    const PATH_BUILD      = 'DEVP_BUILD';
    const BUILD_INTERVAL  = 12;
    const PATH_BUILD_TIME = 'DEVP_BUILD_TIME';

    private const DEFAULT_SETTINGS = [
        'interval' => self::BUILD_INTERVAL,
        'force_build' => null,
    ];

    /**
     * Register the plugin services.
     *
     * @since 0.0.1
     */
    public function register(): void {
        require_once __DIR__ . '/constants.php';
    }

    public function boot(PluginManagerInterface $manager): void {
        try {

            $pluginName = PLUGIN_NAME;

            /*
            * Compatibility view path.
            *
            * LibreNMS local plugins commonly reference views like:
            * device-backups::resources.views.page
            *
            * Package views can also be referenced as:
            * device-backups::page
            */
            $paths = [
                __DIR__ . '/..',
                __DIR__ . '/../resources/views',
            ];
            $this->loadViewsFrom($paths, $pluginName);
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

            $pluginManager = $this->app->make(PluginManagerInterface::class);
            $pluginManager->publishHook($pluginName, MenuEntryHookInterface::class, Menu::class);
            $pluginManager->publishHook($pluginName, SinglePageHook::class, Page::class);
            $pluginManager->publishHook($pluginName, SettingsHookInterface::class, Settings::class);

            $pluginManager->publishHook($pluginName, DeviceOverviewHook::class, DeviceOverview::class);
        } catch (\Throwable $th) {
            Log::error("Error booting plugin: " . $th->getMessage());
        }
    }

    public static function withDefaultSettings(array $settings): array {
        $settings['interval'] ??= self::DEFAULT_SETTINGS['interval'];

        return $settings + self::DEFAULT_SETTINGS;
    }

    /**
     * Get plugin information.
     *
     * @return array{name: string, title: string, author: string, image: string, build: string, ver: string, settings: string, plugin: Plugin|null}
     *
     * @version 1.0.0
     * @todo Something awesome
     */
    public static function getInfo() {
        return array(
            'name'     => PLUGIN_NAME,
            'title'    => PLUGIN_TITLE,
            'author'   => PLUGIN_AUTHOR,
            'image'    => PLUGIN_AUTHOR_IMAGE,
            'build'    => self::PATH_BUILD_TIME,
            'ver'      => PLUGIN_VER,
            'settings' => route('plugin.settings', PLUGIN_NAME),
            'plugin'   => self::getPlugin(),
        );
    }

    /**
     * Build path for each device.
     *
     * @return void
     *
     * @version 1.0.0
     */
    public function doBuild() {

        try {
            $plugin = self::getPlugin();
            if ($plugin === null || ! $plugin->plugin_active) {
                return;
            }

            $start = microtime(true);
            $settings = self::withDefaultSettings((array) $plugin->settings);

            foreach (Device::get() as $device) {
                $this->getPath($device->device_id, true);
            }

            $stop     = microtime(true);
            $duration = $stop - $start;

            $interval = (int) $settings['interval'];

            Cache::add(self::PATH_BUILD, PLUGIN_VER, now()->addHours($interval));
            Cache::forever(self::PATH_BUILD_TIME, $duration);

            $settings['force_build'] = null;
            $plugin->settings        = $settings;
            $plugin->save();
        } catch (\Throwable $th) {
            Log::error('PATH BUILD ERROR: ' . $th->getMessage(), array('exception' => $th));
        }
    }

    /**
     * Make sure build is complete.
     *
     * @version 1.0.0
     * @return void
     */
    public function checkBuild() {
        $build = Cache::get(self::PATH_BUILD);

        $plugin = self::getPlugin();
        if ($plugin?->plugin_active && ($build === null || ! empty($plugin->settings['force_build']))) {
            $this->doBuild();
        }
    }

    /**
     * Get local device name.
     *
     * @param stdClass $record
     * @return string
     */
    public function getLocalName(stdClass $record): string {
        $host_name = $record->local_hostname ?? '';
        $sys_name  = $record->local_sysName ?? '';

        if (strlen($sys_name) > strlen($host_name)) {
            return $sys_name;
        }

        return $host_name;
    }

    /**
     * Get device path
     *
     * @param integer $id The ID of the device.
     * @param boolean $force Regenerate the path if true.
     * @return array
     *
     * @version 1.0.0
     */
    public function getPath(int $id, bool $force = false): array {
        $keyCache = self::PATH_KEY . $id;

        if (! $force) {
            $path = Cache::get($keyCache);
            if (is_array($path)) {
                return $path;
            }
        }

        $relations = DB::table('device_relationships', 'M')
            ->join('devices as D1', 'M.child_device_id', '=', 'D1.device_id', 'inner')
            ->join('devices as D2', 'M.parent_device_id', '=', 'D2.device_id', 'inner')
            ->select('M.child_device_id AS local_device_id', 'D1.os AS local_os', 'D1.hostname AS local_hostname', 'D1.sysName AS local_sysName', 'M.parent_device_id AS remote_device_id', 'D2.os AS remote_os', 'D2.hostname AS remote_hostname', 'D2.sysName AS remote_sysName')
            ->orderBy('D1.device_id')
            ->get();

        $sorted = array();
        $path   = array();

        foreach ($relations as $item) {
            $key            = $item->local_device_id;
            $sorted[$key] = $item;
        }

        $visited = [];
        while (isset($sorted[$id]) && ! isset($visited[$id])) {
            $visited[$id] = true;
            $name = $this->getLocalName($sorted[$id]);
            $path[$name] = $id;

            $id = $sorted[$id]->remote_device_id;
        }

        Cache::forever($keyCache, $path);

        return $path;
    }

    /**
     * Get plugin object model.
     *
     * @return Plugin|null
     *
     * @version 1.0.0
     */
    private static function getPlugin(): ?Plugin {
        return Plugin::versionTwo()->where('plugin_name', PLUGIN_NAME)->first();
    }
}
