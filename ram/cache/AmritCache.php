<?php
/**
 * Ramayan CMS — Cache Interface (Amrit)
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

namespace Ram\Cache;

/**
 * AmritCache — The immortal nectar. Universal cache API.
 *
 * Acts as a static facade over a driver (file, mysql, redis).
 * Driver is selected at boot time from RAM_CACHE_DRIVER config.
 *
 * Usage:
 *   AmritCache::ramFn_set('key', $value, 3600);
 *   AmritCache::ramFn_get('key');
 *   AmritCache::ramFn_delete('key');
 *   AmritCache::ramFn_flush();
 */
final class AmritCache
{
    /** @var CacheDriverInterface|null The active cache driver */
    private static ?CacheDriverInterface $ramVar_driver = null;

    /** @var string Current driver name */
    private static string $ramVar_driverName = 'file';

    /**
     * Initialize the cache system with the given driver.
     *
     * @param string $ramVar_driverName 'file' | 'mysql' | 'redis'
     */
    public static function ramFn_init(string $ramVar_driverName = 'file'): void
    {
        self::$ramVar_driverName = $ramVar_driverName;

        self::$ramVar_driver = match ($ramVar_driverName) {
            'file'  => new AmritFileCache(),
            'mysql' => self::ramFn_buildMysqlDriver(),
            'redis' => self::ramFn_buildRedisDriver(),
            default => new AmritFileCache(),
        };
    }

    /**
     * Get the currently active driver.
     *
     * @throws \RuntimeException if not initialized
     */
    public static function ramFn_driver(): CacheDriverInterface
    {
        if (self::$ramVar_driver === null) {
            // Auto-init with file driver as safe fallback
            self::ramFn_init('file');
        }
        return self::$ramVar_driver;
    }

    // ─── API methods (delegate to driver) ────────────────────────────────────

    /**
     * Store a value in the cache.
     *
     * @param string      $ramVar_key    Cache key
     * @param mixed       $ramVar_value  Value to store (will be serialized)
     * @param int         $ramVar_ttl    TTL in seconds (0 = no expiry)
     * @param string      $ramVar_group  Cache group for bulk invalidation
     */
    public static function ramFn_set(
        string $ramVar_key,
        mixed  $ramVar_value,
        int    $ramVar_ttl   = 3600,
        string $ramVar_group = 'default'
    ): bool {
        return self::ramFn_driver()->ramFn_set(
            self::ramFn_buildKey($ramVar_key, $ramVar_group),
            $ramVar_value,
            $ramVar_ttl,
            $ramVar_group
        );
    }

    /**
     * Get a cached value.
     *
     * @param string $ramVar_key   Cache key
     * @param string $ramVar_group Cache group
     * @return mixed The cached value, or false if not found / expired
     */
    public static function ramFn_get(string $ramVar_key, string $ramVar_group = 'default'): mixed
    {
        return self::ramFn_driver()->ramFn_get(
            self::ramFn_buildKey($ramVar_key, $ramVar_group),
            $ramVar_group
        );
    }

    /**
     * Check if a cache key exists and is not expired.
     */
    public static function ramFn_has(string $ramVar_key, string $ramVar_group = 'default'): bool
    {
        return self::ramFn_get($ramVar_key, $ramVar_group) !== false;
    }

    /**
     * Delete a specific cache entry.
     */
    public static function ramFn_delete(string $ramVar_key, string $ramVar_group = 'default'): bool
    {
        return self::ramFn_driver()->ramFn_delete(
            self::ramFn_buildKey($ramVar_key, $ramVar_group),
            $ramVar_group
        );
    }

    /**
     * Delete all cache entries in a group.
     */
    public static function ramFn_deleteGroup(string $ramVar_group): bool
    {
        return self::ramFn_driver()->ramFn_deleteGroup($ramVar_group);
    }

    /**
     * Flush ALL cache (all groups, all keys).
     */
    public static function ramFn_flush(): bool
    {
        return self::ramFn_driver()->ramFn_flush();
    }

    /**
     * Get or compute a value (cache-aside pattern).
     *
     * @template T
     * @param string   $ramVar_key      Cache key
     * @param int      $ramVar_ttl      TTL in seconds
     * @param callable(): T $ramVar_callback Callable to compute the value if not cached
     * @param string   $ramVar_group    Cache group
     * @return T
     */
    public static function ramFn_remember(
        string   $ramVar_key,
        int      $ramVar_ttl,
        callable $ramVar_callback,
        string   $ramVar_group = 'default'
    ): mixed {
        $ramVar_cached = self::ramFn_get($ramVar_key, $ramVar_group);

        if ($ramVar_cached !== false) {
            return $ramVar_cached;
        }

        $ramVar_value = $ramVar_callback();
        self::ramFn_set($ramVar_key, $ramVar_value, $ramVar_ttl, $ramVar_group);
        return $ramVar_value;
    }

    /**
     * Get or compute a value — caches forever (TTL=0).
     *
     * @template T
     * @param callable(): T $ramVar_callback
     * @return T
     */
    public static function ramFn_rememberForever(
        string   $ramVar_key,
        callable $ramVar_callback,
        string   $ramVar_group = 'default'
    ): mixed {
        return self::ramFn_remember($ramVar_key, 0, $ramVar_callback, $ramVar_group);
    }

    /**
     * Increment a numeric cached value.
     */
    public static function ramFn_increment(string $ramVar_key, int $ramVar_amount = 1, string $ramVar_group = 'default'): int|false
    {
        $ramVar_current = self::ramFn_get($ramVar_key, $ramVar_group);
        if ($ramVar_current === false) {
            self::ramFn_set($ramVar_key, $ramVar_amount, 0, $ramVar_group);
            return $ramVar_amount;
        }
        $ramVar_new = (int) $ramVar_current + $ramVar_amount;
        self::ramFn_set($ramVar_key, $ramVar_new, 0, $ramVar_group);
        return $ramVar_new;
    }

    /**
     * Decrement a numeric cached value.
     */
    public static function ramFn_decrement(string $ramVar_key, int $ramVar_amount = 1, string $ramVar_group = 'default'): int|false
    {
        return self::ramFn_increment($ramVar_key, -$ramVar_amount, $ramVar_group);
    }

    /**
     * Get the name of the active driver.
     */
    public static function ramFn_getDriverName(): string
    {
        return self::$ramVar_driverName;
    }

    // ─── Internal helpers ─────────────────────────────────────────────────────

    /**
     * Build a namespaced cache key.
     */
    private static function ramFn_buildKey(string $ramVar_key, string $ramVar_group): string
    {
        // Sanitize: remove whitespace, limit length
        $ramVar_key   = preg_replace('/\s+/', '_', $ramVar_key) ?? $ramVar_key;
        $ramVar_group = preg_replace('/\s+/', '_', $ramVar_group) ?? $ramVar_group;
        return substr($ramVar_group . ':' . $ramVar_key, 0, 191);
    }

    // ─── Driver builders ─────────────────────────────────────────────────────

    private static function ramFn_buildMysqlDriver(): CacheDriverInterface
    {
        // MySQL driver requires PatalDB to be connected
        if (\Ram\Database\PatalDB::ramFn_isConnected()) {
            return new AmritMysqlCache();
        }
        // Fallback to file if DB not ready
        return new AmritFileCache();
    }

    private static function ramFn_buildRedisDriver(): CacheDriverInterface
    {
        // Redis driver requires PhpRedis or Predis — graceful fallback
        if (class_exists('Redis')) {
            try {
                return new AmritRedisCache();
            } catch (\Throwable) {
                // Redis connection failed — fall back to file
            }
        }
        return new AmritFileCache();
    }
}
