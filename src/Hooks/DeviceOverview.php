<?php

namespace DRP\DevicePaths\Hooks;

use App\Models\Device;
use App\Plugins\Hooks\DeviceOverviewHook;
use DRP\DevicePaths\PluginProvider;


/**
 * DeviceOverview Hook
 *
 * @package     librenms-device-paths
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/librenms-device-paths
 * @since       0.0.1
 */
class DeviceOverview extends DeviceOverviewHook {

    /**
     * Get device data record.
     *
     * @param Device $device
     * @return array
     *
     * @version 1.0.1
     */
    public function data(Device $device): array {
        $dp = new PluginProvider(app());
        $dp->checkBuild();


        $rels = $dp->getPath($device->device_id);
        $group = $device->groups()->first();

        $tree = url('/') . '/maps/devicedependency?group=';
        $deviceTree = $tree . '&highlight_node=' . $device->device_id . '&showparentdevicepath=1';
        $groupTree = $group === null ? null : $tree . $group->id;

        return [
            'title' => PLUGIN_TITLE,
            'device'  => $device,
            'device_path' => $rels,
            'device_tree' => $deviceTree,
            'group_tree' => $groupTree,
        ];
    }
}
