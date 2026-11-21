<?php

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

namespace DRP\DevicePaths;

use App\Models\Device;
use App\Models\Plugin;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use LibreNMS\Interfaces\Plugins\Hooks\DeviceOverviewHook;
use LibreNMS\Interfaces\Plugins\Hooks\MenuEntryHook;
use LibreNMS\Interfaces\Plugins\Hooks\PortTabHook;
use LibreNMS\Interfaces\Plugins\Hooks\SettingsHook;
use LibreNMS\Interfaces\Plugins\Hooks\SinglePageHook;
use LibreNMS\Interfaces\Plugins\PluginManagerInterface;
use stdClass;

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

    const PLUGIN          = 'DevicePaths';
    const TITLE           = 'Device Path';
    const AUTHOR          = 'Daryl Peterson';
    const PATH_KEY        = 'DEVP_';
    const PATH_BUILD      = 'DEVP_BUILD';
    const BUILD_INTERVAL  = 12;
    const PATH_BUILD_TIME = 'DEVP_BUILD_TIME';
    const VER             = '0.0.1';

    private const DEFAULT_SETTINGS = [
        'interval' => self::BUILD_INTERVAL,
        'force_build' => null,
    ];

    public function boot(PluginManagerInterface $manager): void {
        $manager->publishHook(self::PLUGIN, DeviceOverviewHook::class, Hooks\DeviceOverview::class);
        $manager->publishHook(self::PLUGIN, MenuEntryHook::class, Hooks\Menu::class);
        $manager->publishHook(self::PLUGIN, SinglePageHook::class, Hooks\Page::class);
        $manager->publishHook(self::PLUGIN, PortTabHook::class, Hooks\PortTab::class);
        $manager->publishHook(self::PLUGIN, SettingsHook::class, Hooks\Settings::class);

        if ($manager->pluginEnabled(self::PLUGIN)) {
            $this->loadViewsFrom(dirname(__DIR__), self::PLUGIN);
        }
    }

    public static function withDefaultSettings(array $settings): array {
        $settings['interval'] ??= self::DEFAULT_SETTINGS['interval'];

        return $settings + self::DEFAULT_SETTINGS;
    }

    /**
     * Get plugin information.
     *
     * @return array{name: 'DevicePaths', title: 'Device Path', author: 'Daryl Peterson', build: 'DEVP_BUILD_TIME', ver: '0.0.1', settings: mixed, plugin: mixed}
     *
     * @version 1.0.0
     * @todo Something awesome
     */
    public static function getInfo() {
        return array(
            'name'     => self::PLUGIN,
            'title'    => self::TITLE,
            'author'   => self::AUTHOR,
            'build'    => self::PATH_BUILD_TIME,
            'ver'      => self::VER,
            'settings' => route('plugin.settings', self::PLUGIN),
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

            Cache::add(self::PATH_BUILD, self::VER, now()->addHours($interval));
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
        return Plugin::versionTwo()->where('plugin_name', self::PLUGIN)->first();
    }
}
