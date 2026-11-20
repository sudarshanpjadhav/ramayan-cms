<?php
/**
 * Ramayan CMS — Redis Cache Driver (Amrit)
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

namespace Ram\Cache;

/**
 * AmritRedisCache — Optional Redis cache driver.
 * Requires PhpRedis extension. Gracefully degrades to file cache if unavailable.
 */
final class AmritRedisCache implements CacheDriverInterface
{
    private \Redis $ramVar_redis;

    public function __construct()
    {
        $this->ramVar_redis = new \Redis();
        $ramVar_host = defined('RAM_REDIS_HOST') ? RAM_REDIS_HOST : '127.0.0.1';
        $ramVar_port = defined('RAM_REDIS_PORT') ? (int) RAM_REDIS_PORT : 6379;
        $this->ramVar_redis->connect($ramVar_host, $ramVar_port);

        if (defined('RAM_REDIS_PASSWORD') && RAM_REDIS_PASSWORD !== '') {
            $this->ramVar_redis->auth(RAM_REDIS_PASSWORD);
        }
    }

    public function ramFn_set(string $ramVar_key, mixed $ramVar_value, int $ramVar_ttl, string $ramVar_group): bool
    {
        $ramVar_serialized = serialize($ramVar_value);
        if ($ramVar_ttl > 0) {
            return (bool) $this->ramVar_redis->setex($ramVar_key, $ramVar_ttl, $ramVar_serialized);
        }
        return (bool) $this->ramVar_redis->set($ramVar_key, $ramVar_serialized);
    }

    public function ramFn_get(string $ramVar_key, string $ramVar_group): mixed
    {
        $ramVar_raw = $this->ramVar_redis->get($ramVar_key);
        if ($ramVar_raw === false) {
            return false;
        }
        return @unserialize($ramVar_raw);
    }

    public function ramFn_delete(string $ramVar_key, string $ramVar_group): bool
    {
        return (bool) $this->ramVar_redis->del($ramVar_key);
    }

    public function ramFn_deleteGroup(string $ramVar_group): bool
    {
        $ramVar_keys = $this->ramVar_redis->keys($ramVar_group . ':*');
        if (!empty($ramVar_keys)) {
            $this->ramVar_redis->del($ramVar_keys);
        }
        return true;
    }

    public function ramFn_flush(): bool
    {
        return (bool) $this->ramVar_redis->flushDb();
    }
}
