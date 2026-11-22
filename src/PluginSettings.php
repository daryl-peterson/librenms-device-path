<?php

/**
 * LibreNMS Device Paths Plugin Settings.
 *
 * @package     librenms-device-path
 * @author      Daryl Peterson <daryl.peterson@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DevicePaths;

use Throwable;
use App\Models\Plugin;
use DRP\DevicePaths\Log;

/**
 * LibreNMS Device Paths Plugin Settings.
 *
 * @package     librenms-device-path
 * @author      Daryl Peterson <daryl.peterson@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class PluginSettings {

    /**
     * Import settings for the Device Paths plugin.
     *
     * @since 0.0.1
     */
    public array $settings;

    /**
     * The plugin instance.
     *
     * @since 0.0.1
     */
    public Plugin|null $plugin = null;

    /**
     * Device Paths plugin settings constructor.
     *
     * @since 0.0.1
     */
    public function __construct() {
        $this->plugin = PluginData::getPluginModel();
        $this->settings = (array) $this->plugin?->settings;
        Log::info("Plugin settings loaded.", ['settings' => $this->settings]);
    }

    public static function hasSavedDatabase(array $settings): bool {
        return is_array($settings['database_connection'] ?? null);
    }

    /**
     * Get all plugin settings.
     *
     * @return array
     *
     * @since 0.0.1
     */
    public function all(): array {
        return $this->settings;
    }

    /**
     * Get a specific plugin setting.
     *
     * @param string $key The setting key to retrieve.
     * @param mixed $default The default value to return if the setting key does not exist.
     * @return mixed
     *
     * @since 0.0.1
     */
    public function get(string $key, $default = null) {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Set a plugin setting.
     *
     * @param string $key The setting key to set.
     * @param mixed $value The value to set for the specified setting key.
     * @return bool
     *
     * @since 0.0.1
     */
    public function set(string $key, $value): bool {
        try {
            $this->settings[$key] = $value;

            if (is_null($this->plugin)) {
                return false;
            }

            $this->plugin->settings = $this->settings;
            return $this->plugin->save();
        } catch (Throwable $th) {
            Log::error("Error setting plugin setting: " . $th->getMessage());
            return false;
        }
    }

    /**
     * Check if a plugin setting exists.
     *
     * @param string $key The setting key to check for existence.
     * @return bool
     *
     * @since 0.0.1
     */
    public function has(string $key): bool {
        return isset($this->settings[$key]);
    }

    /**
     * Reset all plugin settings.
     *
     * @return void
     *
     * @since 0.0.1
     */
    public function reset() {
        $this->settings = [];
        if (!is_null($this->plugin)) {
            $this->plugin->settings = $this->settings;
            $this->plugin->save();
        }
    }

    /**
     * Delete a specific plugin setting.
     *
     * @param string $key The setting key to delete.
     * @return bool
     *
     * @since 0.0.1
     */
    public function delete(string $key): bool {
        try {
            if (isset($this->settings[$key])) {
                unset($this->settings[$key]);
            }

            if (is_null($this->plugin)) {
                return false;
            }

            $this->plugin->settings = $this->settings;
            return $this->plugin->save();
        } catch (Throwable $th) {
            Log::error("Error deleting plugin setting: " . $th->getMessage());
            return false;
        }
    }
}
