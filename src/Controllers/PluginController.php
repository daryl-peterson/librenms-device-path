<?php

namespace DRP\DevicePaths\Controllers;


use Illuminate\Routing\Controller;
use Illuminate\View\View;
use DRP\DevicePaths\PluginConst;
use DRP\DevicePaths\TraitHidePrivates;
use DRP\DevicePaths\PluginData;
use DRP\DevicePaths\TraitValidateAdmin;

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
        $this->plugin = PluginConst::PLUGIN_NAME;
    }


    public function index(): View {
        return view("$this->plugin::page", $this->info);
    }


    /**
     * Settings page.
     *
     * @return View
     * @since 0.0.1
     */
    public function settings(): View {
        $this->validateAdmin();
        return view("$this->plugin::settings", $this->info);
    }
}
