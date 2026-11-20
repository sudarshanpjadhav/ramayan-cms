<?php
/**
 * Ramayan CMS — MySQL Cache Driver (Amrit)
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

namespace Ram\Cache;

use Ram\Database\PatalDB;

/**
 * AmritMysqlCache — MySQL-based cache driver.
 * Uses the ram_cache table. Good for multi-server setups.
 */
final class AmritMysqlCache implements CacheDriverInterface
{
    public function ramFn_set(string $ramVar_key, mixed $ramVar_value, int $ramVar_ttl, string $ramVar_group): bool
    {
        $ramVar_expires = $ramVar_ttl > 0 ? date('Y-m-d H:i:s', time() + $ramVar_ttl) : null;
        try {
            PatalDB::ramFn_query(
                "INSERT INTO `" . PatalDB::ramFn_getPrefix() . "cache` (cache_key, cache_group, value, expires_at)
                 VALUES (?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE value = VALUES(value), expires_at = VALUES(expires_at)",
                [$ramVar_key, $ramVar_group, serialize($ramVar_value), $ramVar_expires]
            );
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    public function ramFn_get(string $ramVar_key, string $ramVar_group): mixed
    {
        try {
            $ramVar_row = PatalDB::ramFn_selectOne(
                "SELECT value, expires_at FROM `" . PatalDB::ramFn_getPrefix() . "cache`
                 WHERE cache_key = ? AND cache_group = ?
                   AND (expires_at IS NULL OR expires_at > NOW())
                 LIMIT 1",
                [$ramVar_key, $ramVar_group]
            );

            if ($ramVar_row === null) {
                return false;
            }

            return @unserialize($ramVar_row['value']);
        } catch (\Throwable) {
            return false;
        }
    }

    public function ramFn_delete(string $ramVar_key, string $ramVar_group): bool
    {
        try {
            PatalDB::ramFn_query(
                "DELETE FROM `" . PatalDB::ramFn_getPrefix() . "cache` WHERE cache_key = ? AND cache_group = ?",
                [$ramVar_key, $ramVar_group]
            );
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    public function ramFn_deleteGroup(string $ramVar_group): bool
    {
        try {
            PatalDB::ramFn_query(
                "DELETE FROM `" . PatalDB::ramFn_getPrefix() . "cache` WHERE cache_group = ?",
                [$ramVar_group]
            );
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    public function ramFn_flush(): bool
    {
        try {
            PatalDB::ramFn_query("TRUNCATE TABLE `" . PatalDB::ramFn_getPrefix() . "cache`");
            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
