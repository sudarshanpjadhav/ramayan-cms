<?php
/**
 * Ramayan CMS — Cache Driver Interface
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

namespace Ram\Cache;

/**
 * CacheDriverInterface — Contract for all Amrit cache drivers.
 */
interface CacheDriverInterface
{
    public function ramFn_set(string $ramVar_key, mixed $ramVar_value, int $ramVar_ttl, string $ramVar_group): bool;
    public function ramFn_get(string $ramVar_key, string $ramVar_group): mixed;
    public function ramFn_delete(string $ramVar_key, string $ramVar_group): bool;
    public function ramFn_deleteGroup(string $ramVar_group): bool;
    public function ramFn_flush(): bool;
}
