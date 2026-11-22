<?php

/**
 * LibreNMS device paths trait to hide private properties from debug output.
 *
 * @package     librenms-device-paths
 * @author      Daryl Peterson <daryl.peterson@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DevicePaths;

use ReflectionProperty;

trait TraitHidePrivates {
    public function __debugInfo() {
        $properties = get_object_vars($this);
        foreach ($properties as $key => $value) {
            if ((new ReflectionProperty($this, $key))->isPrivate()) {
                unset($properties[$key]);
            }
        }
        return $properties;
    }
}
