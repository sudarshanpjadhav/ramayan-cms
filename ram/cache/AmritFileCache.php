<?php
/**
 * Ramayan CMS — File Cache Driver (Amrit)
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

namespace Ram\Cache;

/**
 * AmritFileCache — Filesystem-based cache driver.
 *
 * Stores serialized values as .cache files in RAM_CACHE_PATH.
 * Works on every cPanel shared hosting — no extra software needed.
 * Supports TTL, cache groups, and atomic writes (via temp + rename).
 */
final class AmritFileCache implements CacheDriverInterface
{
    private string $ramVar_cacheDir;

    public function __construct(?string $ramVar_cacheDir = null)
    {
        $this->ramVar_cacheDir = $ramVar_cacheDir
            ?? (defined('RAM_CACHE_PATH') ? RAM_CACHE_PATH : sys_get_temp_dir() . '/ramayan_cache');

        if (!is_dir($this->ramVar_cacheDir)) {
            @mkdir($this->ramVar_cacheDir, 0755, true);
        }

        // Write .htaccess to block direct HTTP access to cache files
        $ramVar_htaccess = $this->ramVar_cacheDir . '/.htaccess';
        if (!file_exists($ramVar_htaccess)) {
            @file_put_contents($ramVar_htaccess, "Deny from all\n");
        }
    }

    // ─── CacheDriverInterface ─────────────────────────────────────────────────

    /**
     * Store a value in the file cache.
     *
     * @param string $ramVar_key    Namespaced cache key
     * @param mixed  $ramVar_value  Value to store
     * @param int    $ramVar_ttl    Seconds until expiry (0 = no expiry)
     * @param string $ramVar_group  Group name (used for directory organisation)
     */
    public function ramFn_set(string $ramVar_key, mixed $ramVar_value, int $ramVar_ttl, string $ramVar_group): bool
    {
        $ramVar_file    = $this->ramFn_getPath($ramVar_key, $ramVar_group);
        $ramVar_dir     = dirname($ramVar_file);

        if (!is_dir($ramVar_dir)) {
            @mkdir($ramVar_dir, 0755, true);
        }

        $ramVar_expires = $ramVar_ttl > 0 ? time() + $ramVar_ttl : 0;

        $ramVar_data = serialize([
            'expires' => $ramVar_expires,
            'group'   => $ramVar_group,
            'value'   => $ramVar_value,
        ]);

        // Atomic write via temp file + rename
        $ramVar_tmp = $ramVar_file . '.tmp.' . getmypid();
        if (@file_put_contents($ramVar_tmp, $ramVar_data, LOCK_EX) === false) {
            return false;
        }

        return @rename($ramVar_tmp, $ramVar_file);
    }

    /**
     * Retrieve a cached value.
     *
     * @param string $ramVar_key   Namespaced key
     * @param string $ramVar_group Group name
     * @return mixed The cached value, or false if not found or expired
     */
    public function ramFn_get(string $ramVar_key, string $ramVar_group): mixed
    {
        $ramVar_file = $this->ramFn_getPath($ramVar_key, $ramVar_group);

        if (!file_exists($ramVar_file)) {
            return false;
        }

        $ramVar_raw = @file_get_contents($ramVar_file);
        if ($ramVar_raw === false) {
            return false;
        }

        $ramVar_data = @unserialize($ramVar_raw);
        if (!is_array($ramVar_data) || !array_key_exists('value', $ramVar_data)) {
            @unlink($ramVar_file);
            return false;
        }

        // Check expiry
        if ($ramVar_data['expires'] > 0 && time() > $ramVar_data['expires']) {
            @unlink($ramVar_file);
            return false;
        }

        return $ramVar_data['value'];
    }

    /**
     * Delete a specific cache entry.
     */
    public function ramFn_delete(string $ramVar_key, string $ramVar_group): bool
    {
        $ramVar_file = $this->ramFn_getPath($ramVar_key, $ramVar_group);
        if (file_exists($ramVar_file)) {
            return @unlink($ramVar_file);
        }
        return true;
    }

    /**
     * Delete all cache entries in a group.
     */
    public function ramFn_deleteGroup(string $ramVar_group): bool
    {
        $ramVar_groupDir = $this->ramVar_cacheDir
            . DIRECTORY_SEPARATOR
            . $this->ramFn_sanitizeGroup($ramVar_group);

        if (!is_dir($ramVar_groupDir)) {
            return true;
        }

        return $this->ramFn_deleteDirectory($ramVar_groupDir);
    }

    /**
     * Flush ALL cache entries (delete entire cache directory contents).
     */
    public function ramFn_flush(): bool
    {
        if (!is_dir($this->ramVar_cacheDir)) {
            return true;
        }

        $ramVar_items = new \DirectoryIterator($this->ramVar_cacheDir);
        foreach ($ramVar_items as $ramVar_item) {
            if ($ramVar_item->isDot()) {
                continue;
            }
            if ($ramVar_item->isDir()) {
                $this->ramFn_deleteDirectory($ramVar_item->getPathname());
            } elseif ($ramVar_item->isFile() && $ramVar_item->getExtension() === 'cache') {
                @unlink($ramVar_item->getPathname());
            }
        }

        return true;
    }

    // ─── Additional helpers ───────────────────────────────────────────────────

    /**
     * Scan and delete all expired cache files.
     * Call this from a scheduled task / cron for housekeeping.
     */
    public function ramFn_gc(): int
    {
        $ramVar_deleted = 0;

        if (!is_dir($this->ramVar_cacheDir)) {
            return 0;
        }

        $ramVar_iter = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->ramVar_cacheDir, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($ramVar_iter as $ramVar_file) {
            /** @var \SplFileInfo $ramVar_file */
            if ($ramVar_file->getExtension() !== 'cache') {
                continue;
            }

            $ramVar_raw  = @file_get_contents($ramVar_file->getPathname());
            $ramVar_data = $ramVar_raw !== false ? @unserialize($ramVar_raw) : false;

            if (!is_array($ramVar_data) || ($ramVar_data['expires'] > 0 && time() > $ramVar_data['expires'])) {
                @unlink($ramVar_file->getPathname());
                $ramVar_deleted++;
            }
        }

        return $ramVar_deleted;
    }

    /**
     * Get cache file statistics (count, total size in bytes).
     *
     * @return array{count: int, size: int}
     */
    public function ramFn_stats(): array
    {
        $ramVar_count = 0;
        $ramVar_size  = 0;

        if (!is_dir($this->ramVar_cacheDir)) {
            return ['count' => 0, 'size' => 0];
        }

        $ramVar_iter = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->ramVar_cacheDir, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($ramVar_iter as $ramVar_file) {
            /** @var \SplFileInfo $ramVar_file */
            if ($ramVar_file->getExtension() === 'cache') {
                $ramVar_count++;
                $ramVar_size += $ramVar_file->getSize();
            }
        }

        return ['count' => $ramVar_count, 'size' => $ramVar_size];
    }

    // ─── Internal ─────────────────────────────────────────────────────────────

    /**
     * Build the full file path for a cache key.
     *
     * Directory: cache/{group}/{first-2-chars-of-hash}/
     * File:      {hash}.cache
     *
     * Sharding by first 2 chars prevents inode exhaustion on busy caches.
     */
    private function ramFn_getPath(string $ramVar_key, string $ramVar_group): string
    {
        $ramVar_hash  = md5($ramVar_key);
        $ramVar_shard = substr($ramVar_hash, 0, 2);

        return $this->ramVar_cacheDir
            . DIRECTORY_SEPARATOR . $this->ramFn_sanitizeGroup($ramVar_group)
            . DIRECTORY_SEPARATOR . $ramVar_shard
            . DIRECTORY_SEPARATOR . $ramVar_hash . '.cache';
    }

    /**
     * Sanitize a group name for use as a directory name.
     */
    private function ramFn_sanitizeGroup(string $ramVar_group): string
    {
        return preg_replace('/[^a-zA-Z0-9_\-]/', '_', $ramVar_group) ?? 'default';
    }

    /**
     * Recursively delete a directory and all its contents.
     */
    private function ramFn_deleteDirectory(string $ramVar_path): bool
    {
        if (!is_dir($ramVar_path)) {
            return true;
        }

        $ramVar_items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($ramVar_path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($ramVar_items as $ramVar_item) {
            /** @var \SplFileInfo $ramVar_item */
            if ($ramVar_item->isDir()) {
                @rmdir($ramVar_item->getPathname());
            } else {
                @unlink($ramVar_item->getPathname());
            }
        }

        return @rmdir($ramVar_path);
    }
}
