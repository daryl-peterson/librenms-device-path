<?php

/**
 * LibreNMS Device Paths Plugin Cache.
 *
 * @package     librenms-device-path
 * @author      Daryl Peterson <daryl.peterson@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DevicePaths;

/**
 * Standard PHP imports.
 */

use UnitEnum;
use DateTimeInterface;
use DateInterval;

/**
 * Laravel and application imports.
 */

use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;

/**
 * LibreNMS Device Paths Plugin Cache.
 *
 * @package     librenms-device-path
 * @author      Daryl Peterson <daryl.peterson@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class PluginCache {
    /**
     * Cache tag for the plugin.
     */
    const CACHE_TAG = 'device_paths';

    /**
     * Default time-to-live for cache entries.
     */
    const TTL = 600; // 5 minutes

    /**
     * List of keys that have been set
     *
     * @var array
     */
    private static array $keys = [];

    /**
     * Default cache values for the plugin.
     */
    private static $default = [
        CACHE_DB_PASS => false,
        CACHE_DB_ERROR => null,
        CACHE_DB_MIGRATION_CHECK => false,
    ];

    /**
     * Get all cache entries for the plugin.
     *
     * @return array
     *
     * @since 0.0.1
     */
    public static function all(): array {
        self::init();
        $all = [];
        foreach (self::$keys as $key => $_) {
            $all[$key] = self::get($key, self::$default[$key] ?? null);
        }
        return $all;
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param UnitEnum|string $cacheKey The cache key to retrieve the value for.
     * @param mixed $default The default value to return if the cache key does not exist.
     * @return mixed
     *
     * @since 0.0.1
     */
    public static function get(UnitEnum|string $cacheKey, mixed $default = null): mixed {

        if (Cache::getStore() instanceof TaggableStore) {
            $result = Cache::tags([self::CACHE_TAG])->get($cacheKey, $default);
        } else {
            $result = Cache::get($cacheKey, $default);
        }
        return $result;
    }


    /**
     * Store an item in the cache.
     *
     * @param UnitEnum|string $cacheKey The cache key to store the value for.
     * @param mixed $cacheValue The value to store in the cache.
     * @param DateTimeInterface|DateInterval|int|null $ttl The time-to-live for the cache entry.
     * @return boolean
     *
     * @since 0.0.1
     */
    public static function set(UnitEnum|string $cacheKey, mixed $cacheValue, DateTimeInterface|DateInterval|int|null $ttl = self::TTL): bool {
        self::init();
        if (!key_exists($cacheKey, self::$keys)) {
            self::$keys[$cacheKey] = true;
        }

        if (Cache::getStore() instanceof TaggableStore) {
            return Cache::tags([self::CACHE_TAG])->put($cacheKey, $cacheValue, $ttl);
        } else {
            return Cache::put($cacheKey, $cacheValue, $ttl);
        }
    }

    /**
     * Remove an item from the cache.
     *
     * @param UnitEnum|string $cacheKey The cache key to remove from the cache.
     * @return boolean
     *
     * @since 0.0.1
     */
    public static function forget(UnitEnum|string $cacheKey): bool {
        if (isset(self::$keys[$cacheKey])) {
            unset(self::$keys[$cacheKey]);
        }

        if (Cache::getStore() instanceof TaggableStore) {
            return Cache::tags([self::CACHE_TAG])->forget($cacheKey);
        } else {
            return Cache::forget($cacheKey);
        }
    }

    /**
     * Flush all cache entries for the plugin.
     *
     * @return boolean
     *
     * @since 0.0.1
     */
    public static function flush(): bool {
        if (Cache::getStore() instanceof TaggableStore) {
            return Cache::tags([self::CACHE_TAG])->flush();
        } else {
            return Cache::flush();
        }
    }

    /**
     * Check if a key exists in the cache.
     *
     * @param UnitEnum|string $cacheKey The cache key to check for existence in the cache.
     * @return boolean
     * @since 0.0.1
     */
    public static function has(UnitEnum|string $cacheKey): bool {

        if (Cache::getStore() instanceof TaggableStore) {
            return Cache::tags([self::CACHE_TAG])->has($cacheKey);
        } else {
            return Cache::has($cacheKey);
        }
    }

    /**
     * Get all cache keys for the plugin.
     *
     * @return array
     *
     * @since 0.0.1
     */
    public static function keys(): array {
        self::init();

        return array_keys(self::$keys);
    }

    /**
     * Initialize the cache keys if not already set.
     *
     * @return void
     *
     * @since 0.0.1
     */
    private static function init() {
        if (!is_array(self::$keys) || empty(self::$keys)) {
            self::$keys = self::$default;
        }
    }
}
