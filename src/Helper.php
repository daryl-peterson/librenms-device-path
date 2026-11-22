<?php

/**
 * LibreNMS Device Paths Helper.
 *
 * @package     librenms-device-paths
 * @author      Daryl Peterson <daryl.peterson@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DevicePaths;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use function Illuminate\Support\minutes;
use function Illuminate\Support\hours;
use function Illuminate\Support\days;
use DateTimeImmutable;

/**
 * LibreNMS Device Paths Helper.
 *
 * @package     librenms-device-paths
 * @author      Daryl Peterson <daryl.peterson@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class Helper {
    public function __construct() {
        # Code Here
    }

    /**
     * Check if the current user is an admin.
     *
     * @return bool True if the current user is an admin, false otherwise.
     * @since 0.0.1
     */
    public static function isAdmin(): bool {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            return false;
        }
        return true;
    }

    /**
     * Convert minutes to seconds.
     *
     * @param int $value The number of minutes.
     * @return int The equivalent number of seconds.
     * @since 0.0.1
     */
    public static function minutes(int $value): int {
        $result = minutes($value);

        return (int) $result->totalSeconds;
    }

    public static function hours(int $value): int {
        $result = hours($value);

        return (int) $result->totalSeconds;
    }

    /**
     * Convert days to seconds.
     *
     * @param int $value The number of days.
     * @return int The equivalent number of seconds.
     * @since 0.0.1
     */
    public static function days(int $value): int {
        $result = days($value);

        return (int) $result->totalSeconds;
    }


    public static function getDate($format = 'Y-m-d H:i:s'): string {
        $date = new DateTimeImmutable();
        return $date->format($format);
    }

    /**
     * Check if the LibreNMS service is active.
     *
     * @return bool True if the service is active, false otherwise.
     * @since 0.0.1
     */
    public static function isServiceActive() {

        try {
            // Check if the librenms systemd service is active on the host machine
            $isServiceActive = shell_exec('systemctl is-active librenms.service') === "active\n";

            if ($isServiceActive) {
                return true;
            }
            return false;
        } catch (\Throwable $th) {
            Log::error("Error checking if LibreNMS service is active: " . $th->getMessage());
        }
        return false;
    }
}
