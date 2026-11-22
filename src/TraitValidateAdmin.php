<?php

/**
 * LibreNMS device paths trait to validate admin users.
 *
 * @package     librenms-device-paths
 * @author      Daryl Peterson <daryl.peterson@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DevicePaths;

/**
 * Laravel imports.
 */

use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Plugin imports.
 */

use DRP\DevicePaths\Helper;

trait TraitValidateAdmin {
    /**
     * Check if the current user is an admin and abort if not.
     *
     * @return void
     *
     * @since 0.0.1
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws HttpResponseException
     *
     */
    public function validateAdmin(): void {
        if (!Helper::isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
