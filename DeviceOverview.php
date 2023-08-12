<?php

namespace App\Plugins\DevicePaths;

use App\Models\Device;
use App\Plugins\DevicePaths\Libs\DevicePaths;
use App\Plugins\Hooks\DeviceOverviewHook;
use Illuminate\Support\Facades\Log;
use stdClass;

/*
GET|HEAD   plugin/settings ............plugin.admin › PluginAdminController
GET|HEAD   plugin/settings/{plugin} ...plugin.settings › PluginSettingsController
POST       plugin/settings/{plugin} ...plugin.update › PluginSettingsController@update
ANY        plugin/v1/{plugin}/{other?} plugin.legacy › PluginLegacyController
GET|HEAD   plugin/{plugin} ............plugin.page › PluginPageController
*/

/**
 * DeviceOverview Hook
 *
 * @category
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2020, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */
class DeviceOverview extends DeviceOverviewHook
{
    public function data(Device $device): array
    {
        $dp = new DevicePaths();
        $dp->checkBuild();

        $rels = $dp->getPath($device->device_id);
        $group = $device->groups()->first();

        $tree = url('/') . '/maps/devicedependency?group=';
        $deviceTree = $tree . '&highlight_node=' . $device->device_id . '&showparentdevicepath=1';
        $groupTree = $tree . $group->id;

        return [
            'title' => DevicePaths::TITLE,
            'device'  => $device,
            'device_path'=>$rels,
            'device_tree'=>$deviceTree,
            'group_tree'=>$groupTree,
        ];
    }
}
