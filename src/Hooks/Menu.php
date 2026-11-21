<?php

namespace App\Plugins\DevicePaths;

use App\Plugins\DevicePaths\Libs\DevicePaths;
use App\Plugins\Hooks\MenuEntryHook;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/**
 * Class description
 *
 * @category
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2020, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */
class Menu extends MenuEntryHook
{
    const TITLE = 'Device Path';

    public function data(): array
    {
        return [
            'title'=>DevicePaths::TITLE,
        ];
    }
}
