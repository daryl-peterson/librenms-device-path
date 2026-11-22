<?php

namespace DRP\DevicePaths\Controllers;

/**
 * Laravel imports.
 */

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * Plugin imports.
 */

use DRP\DevicePaths\Log;
use DRP\DevicePaths\TraitHidePrivates;
use DRP\DevicePaths\PluginData;
use DRP\DevicePaths\PluginSettings;
use DRP\DevicePaths\TraitValidateAdmin;
use const DRP\DevicePaths\PLUGIN_NAME;

/**
 * LibreNMS Device Paths Plugin Controller
 *
 * @package     librenms-device-paths
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class PluginController extends Controller {
    use TraitHidePrivates;
    use TraitValidateAdmin;

    private array $info;
    private string $plugin;

    /**
     * Constructor.
     *
     * @since 0.0.1
     */
    public function __construct() {
        $this->info = PluginData::getInfo();
        $this->plugin = PLUGIN_NAME;
    }


    public function index(): View {

        return view("$this->plugin::page");
    }


    /**
     * Settings page.
     *
     * @return View
     * @since 0.0.1
     */
    public function settings(): View {
        $this->validateAdmin();

        return view("$this->plugin::settings", [
            'info' => $this->info,
            'plugin' => $this->plugin,
            'settings' => PluginSettings::forEditing($this->info['settings']),
        ]);
    }
}
