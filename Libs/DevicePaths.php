<?php

namespace App\Plugins\DevicePaths\Libs;

use App\Models\Device;
use App\Models\Plugin;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use stdClass;

/**
 * Create list of all device parents
 *
 * @category
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2020, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @version     0.0.1
 */
class DevicePaths
{
    const PLUGIN = 'DevicePaths';
    const TITLE = 'Device Path';
    const AUTHOR = 'Daryl Peterson';
    const PATH_KEY = 'DEVP_';
    const PATH_BUILD = 'DEVP_BUILD';
    const BUILD_INTERVAL = 12;
    const PATH_BUILD_TIME = 'DEVP_BUILD_TIME';
    const VER = '0.0.1';

    public $startTime;
    public $stopTime;

    public function __construct()
    {
        $plugin = self::getPlugin();
        $exist = true;
        if (! isset($plugin->settings)) {
            $exist = false;
        }

        $settings = $plugin->settings;
        if (! isset($settings['interval'])) {
            $exist = false;
        }

        if (! $exist) {
            $plugin->settings = [
                'interval'=>self::BUILD_INTERVAL,
                'force_build'=>null,
            ];

            $plugin->save();
        }
    }

    public static function getInfo()
    {
        return [
            'name'=>self::PLUGIN,
            'title'=> self::TITLE,
            'author'=>self::AUTHOR,
            'build'=>self::PATH_BUILD_TIME,
            'ver'=>self::VER,
            'settings'=>route('plugin.settings', self::PLUGIN),
            'plugin'=>self::getPlugin(),
        ];
    }

    public function doBuild()
    {
        $start = microtime(true);
        $devices = Device::get();
        $plugin = self::getPlugin();
        $settings = $plugin->settings;

        foreach ($devices as $device) {
            $this->getPath($device->device_id, true);
        }

        $stop = microtime(true);
        $duration = $stop - $start;

        Cache::add(self::PATH_BUILD, self::VER, now()->addHours($settings['interval']));
        Cache::forever(self::PATH_BUILD_TIME, $duration);

        $settings['force_build'] = null;
        $plugin->settings = $settings;
        $plugin->save();

        Log::error('PATH BUILD : ' . $duration, $plugin->settings);
    }

    public function checkBuild()
    {
        $build = Cache::get(self::PATH_BUILD);

        if (! isset($build)) {
            Log::error('DO BUILD 1');
            $this->doBuild();
        }

        $plugin = self::getPlugin();
        if ($plugin->settings['force_build']) {
            Log::error('DO BUILD 2', [$plugin]);
            $this->doBuild();
        }
    }

    public function getLocalName(stdClass $record):string
    {
        if (! isset($record->local_hostname)) {
            $host_name = '';
        }
        if (! isset($record->local_sysName)) {
            $sys_name = '';
        }
        $host_name = $record->local_hostname;
        $sys_name = $record->local_sysName;

        if (strlen($sys_name) > strlen($host_name)) {
            return $sys_name;
        }

        return $host_name;
    }

    public function getPath(int $id, bool $force = false)
    {
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

        $sorted = [];
        $path = [];

        foreach ($relations as $item) {
            $key = $item->local_device_id;
            $sorted[$key] = $item;
        }

        $done = false;
        while (! $done) {
            if (! isset($sorted[$id])) {
                $done = true;
                break;
            }

            $name = $this->getLocalName($sorted[$id]);
            $path[$name] = $id;

            $id = $sorted[$id]->remote_device_id;
        }

        Cache::forever($keyCache, $path);

        return $path;
    }

    private static function getPlugin()
    {
        return Plugin::where('plugin_name', self::PLUGIN)->first();
    }
}
