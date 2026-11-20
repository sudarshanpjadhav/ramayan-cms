<?php
/**
 * Ramayan CMS — Global Helper Functions
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 *
 * All ram_* shorthand helpers. Must be loaded after the autoloader.
 */

declare(strict_types=1);

use Ram\Hooks\AkashvaniHooks;
use Ram\Database\PatalDB;
use Ram\Cache\AmritCache;
use Ram\Security\SudarshanaXSS;
use Ram\Security\SudarshanaNonce;
use Ram\User\PanchjanyaSession;
use Ram\Core\Ram;

// ══════════════════════════════════════════════════════════════════════════════
// SECTION 1 — OUTPUT ESCAPING (Sudarshana)
// ══════════════════════════════════════════════════════════════════════════════

/**
 * Escape for HTML text content.
 */
function ram_esc_html(mixed $ramVar_value): string
{
    return SudarshanaXSS::ramFn_html($ramVar_value);
}

/**
 * Escape for HTML attribute values.
 */
function ram_esc_attr(mixed $ramVar_value): string
{
    return SudarshanaXSS::ramFn_attr($ramVar_value);
}

/**
 * Escape and validate a URL.
 */
function ram_esc_url(mixed $ramVar_url): string
{
    return SudarshanaXSS::ramFn_url($ramVar_url);
}

/**
 * Encode a value for safe inline JavaScript use.
 */
function ram_esc_js(mixed $ramVar_value): string
{
    return SudarshanaXSS::ramFn_js($ramVar_value);
}

/**
 * Sanitize HTML with an allowed-tag whitelist.
 *
 * @param array<string, string[]>|null $ramVar_allowed
 */
function ram_kses(mixed $ramVar_html, ?array $ramVar_allowed = null): string
{
    return SudarshanaXSS::ramFn_kses($ramVar_html, $ramVar_allowed);
}

/**
 * Sanitize a slug (URL-safe).
 */
function ram_sanitize_slug(string $ramVar_value): string
{
    return SudarshanaXSS::ramFn_slug($ramVar_value);
}

/**
 * Sanitize an email address.
 */
function ram_sanitize_email(string $ramVar_email): string
{
    return SudarshanaXSS::ramFn_email($ramVar_email);
}

/**
 * Sanitize to integer.
 */
function ram_absint(mixed $ramVar_value): int
{
    return abs((int) $ramVar_value);
}

// ══════════════════════════════════════════════════════════════════════════════
// SECTION 2 — HOOK SYSTEM (Akashvani)
// ══════════════════════════════════════════════════════════════════════════════

function akashvani_add_action(string $ramVar_hook, callable $ramVar_cb, int $ramVar_priority = 10, int $ramVar_args = 1): void
{
    AkashvaniHooks::ramFn_addAction($ramVar_hook, $ramVar_cb, $ramVar_priority, $ramVar_args);
}

function akashvani_do_action(string $ramVar_hook, mixed ...$ramVar_args): void
{
    AkashvaniHooks::ramFn_doAction($ramVar_hook, ...$ramVar_args);
}

function akashvani_remove_action(string $ramVar_hook, callable $ramVar_cb, int $ramVar_priority = 10): bool
{
    return AkashvaniHooks::ramFn_removeAction($ramVar_hook, $ramVar_cb, $ramVar_priority);
}

function akashvani_has_action(string $ramVar_hook, callable|false $ramVar_cb = false): bool|int
{
    return AkashvaniHooks::ramFn_hasAction($ramVar_hook, $ramVar_cb);
}

function akashvani_add_filter(string $ramVar_hook, callable $ramVar_cb, int $ramVar_priority = 10, int $ramVar_args = 1): void
{
    AkashvaniHooks::ramFn_addFilter($ramVar_hook, $ramVar_cb, $ramVar_priority, $ramVar_args);
}

function akashvani_apply_filter(string $ramVar_hook, mixed $ramVar_value, mixed ...$ramVar_args): mixed
{
    return AkashvaniHooks::ramFn_applyFilter($ramVar_hook, $ramVar_value, ...$ramVar_args);
}

function akashvani_remove_filter(string $ramVar_hook, callable $ramVar_cb, int $ramVar_priority = 10): bool
{
    return AkashvaniHooks::ramFn_removeFilter($ramVar_hook, $ramVar_cb, $ramVar_priority);
}

function akashvani_has_filter(string $ramVar_hook, callable|false $ramVar_cb = false): bool|int
{
    return AkashvaniHooks::ramFn_hasFilter($ramVar_hook, $ramVar_cb);
}

/** Shorthand aliases (like WordPress add_action/add_filter) */
function ram_action(string $ramVar_hook, callable $ramVar_cb, int $ramVar_p = 10, int $ramVar_a = 1): void
{
    AkashvaniHooks::ramFn_addAction($ramVar_hook, $ramVar_cb, $ramVar_p, $ramVar_a);
}

function ram_filter(string $ramVar_hook, callable $ramVar_cb, int $ramVar_p = 10, int $ramVar_a = 1): void
{
    AkashvaniHooks::ramFn_addFilter($ramVar_hook, $ramVar_cb, $ramVar_p, $ramVar_a);
}

function ram_do_action(string $ramVar_hook, mixed ...$ramVar_args): void
{
    AkashvaniHooks::ramFn_doAction($ramVar_hook, ...$ramVar_args);
}

function ram_apply_filters(string $ramVar_hook, mixed $ramVar_value, mixed ...$ramVar_args): mixed
{
    return AkashvaniHooks::ramFn_applyFilter($ramVar_hook, $ramVar_value, ...$ramVar_args);
}

function ram_did_action(string $ramVar_hook): int
{
    return AkashvaniHooks::ramFn_didAction($ramVar_hook);
}

// ══════════════════════════════════════════════════════════════════════════════
// SECTION 3 — OPTIONS (Database key-value store)
// ══════════════════════════════════════════════════════════════════════════════

/** @var array<string, mixed> In-memory options cache */
$GLOBALS['ram_options_cache'] = [];

/**
 * Get a site option from ram_options table.
 *
 * @param string $ramVar_key     Option name
 * @param mixed  $ramVar_default Default if option not found
 */
function ram_get_option(string $ramVar_key, mixed $ramVar_default = null): mixed
{
    // Check in-memory cache
    if (array_key_exists($ramVar_key, $GLOBALS['ram_options_cache'])) {
        return $GLOBALS['ram_options_cache'][$ramVar_key];
    }

    if (!PatalDB::ramFn_isConnected()) {
        return $ramVar_default;
    }

    try {
        $ramVar_row = PatalDB::ramFn_table('options')
            ->ramFn_select(['option_value'])
            ->ramFn_where('option_name', '=', $ramVar_key)
            ->ramFn_first();

        $ramVar_value = $ramVar_row !== null
            ? (@unserialize($ramVar_row['option_value']) ?: $ramVar_row['option_value'])
            : $ramVar_default;

        $GLOBALS['ram_options_cache'][$ramVar_key] = $ramVar_value;
        return $ramVar_value;
    } catch (\Throwable) {
        return $ramVar_default;
    }
}

/**
 * Update or create a site option.
 *
 * @param string $ramVar_key   Option name
 * @param mixed  $ramVar_value Value to store
 */
function ram_update_option(string $ramVar_key, mixed $ramVar_value): bool
{
    if (!PatalDB::ramFn_isConnected()) {
        return false;
    }

    $ramVar_serialized = is_scalar($ramVar_value) ? (string) $ramVar_value : serialize($ramVar_value);

    try {
        PatalDB::ramFn_query(
            "INSERT INTO `" . PatalDB::ramFn_getPrefix() . "options` (option_name, option_value, autoload)
             VALUES (?, ?, 'yes')
             ON DUPLICATE KEY UPDATE option_value = VALUES(option_value)",
            [$ramVar_key, $ramVar_serialized]
        );

        $GLOBALS['ram_options_cache'][$ramVar_key] = $ramVar_value;
        return true;
    } catch (\Throwable) {
        return false;
    }
}

/**
 * Delete a site option.
 */
function ram_delete_option(string $ramVar_key): bool
{
    if (!PatalDB::ramFn_isConnected()) {
        return false;
    }

    try {
        PatalDB::ramFn_table('options')->ramFn_where('option_name', '=', $ramVar_key)->ramFn_delete();
        unset($GLOBALS['ram_options_cache'][$ramVar_key]);
        return true;
    } catch (\Throwable) {
        return false;
    }
}

// ══════════════════════════════════════════════════════════════════════════════
// SECTION 4 — POSTS
// ══════════════════════════════════════════════════════════════════════════════

/**
 * Get a single post by ID.
 *
 * @return array<string, mixed>|null
 */
function ram_get_post(int $ramVar_id): ?array
{
    if (!PatalDB::ramFn_isConnected()) {
        return null;
    }
    try {
        return PatalDB::ramFn_table('posts')->ramFn_find($ramVar_id);
    } catch (\Throwable) {
        return null;
    }
}

/**
 * Get multiple posts.
 *
 * @param array<string, mixed> $ramVar_args Query args:
 *   post_type, status, limit, offset, orderby, order, author_id
 *   Use status='any' to get all statuses, post_type='any' for all types
 * @return array<int, array<string, mixed>>
 */
function ram_get_posts(array $ramVar_args = []): array
{
    if (!PatalDB::ramFn_isConnected()) {
        return [];
    }

    $ramVar_args = array_merge([
        'post_type'  => 'post',
        'status'   => 'publish',
        'limit'    => 10,
        'offset'   => 0,
        'orderby'  => 'published_at',
        'order'    => 'DESC',
        'author_id' => null,
    ], $ramVar_args);

    $ramVar_args = (array) ram_apply_filters('ram_get_posts_args', $ramVar_args);

    try {
        $ramVar_query = PatalDB::ramFn_table('posts');

        if (isset($ramVar_args['post_type']) && $ramVar_args['post_type'] !== 'any') {
            $ramVar_query->ramFn_where('post_type', '=', $ramVar_args['post_type']);
        }

        if (isset($ramVar_args['status']) && $ramVar_args['status'] !== 'any') {
            $ramVar_query->ramFn_where('status', '=', $ramVar_args['status']);
        }

        $ramVar_query->ramFn_orderBy($ramVar_args['orderby'], $ramVar_args['order'])
            ->ramFn_limit((int) $ramVar_args['limit'])
            ->ramFn_offset((int) $ramVar_args['offset']);

        if ($ramVar_args['author_id'] !== null) {
            $ramVar_query->ramFn_where('author_id', '=', (int) $ramVar_args['author_id']);
        }

        $ramVar_posts = $ramVar_query->ramFn_get();
        return (array) ram_apply_filters('ram_posts_results', $ramVar_posts);
    } catch (\Throwable) {
        return [];
    }
}

/**
 * Get the permalink for a post.
 *
 * @param array<string, mixed>|int $ramVar_post Post array or ID
 */
function ram_get_permalink(array|int $ramVar_post): string
{
    if (is_int($ramVar_post)) {
        $ramVar_post = ram_get_post($ramVar_post) ?? [];
    }

    $ramVar_slug = $ramVar_post['slug'] ?? '';
    $ramVar_url  = defined('RAM_URL') ? RAM_URL : '';

    if ($ramVar_slug === '') {
        return ram_esc_url($ramVar_url);
    }

    $ramVar_link = $ramVar_url . '/' . $ramVar_slug;
    return (string) ram_apply_filters('ram_permalink', ram_esc_url($ramVar_link), $ramVar_post);
}

/**
 * Get a post meta value.
 */
function ram_get_post_meta(int $ramVar_postId, string $ramVar_key, bool $ramVar_single = true): mixed
{
    if (!PatalDB::ramFn_isConnected()) {
        return $ramVar_single ? null : [];
    }

    try {
        $ramVar_rows = PatalDB::ramFn_table('postmeta')
            ->ramFn_where('post_id', '=', $ramVar_postId)
            ->ramFn_where('meta_key', '=', $ramVar_key)
            ->ramFn_get();

        if (empty($ramVar_rows)) {
            return $ramVar_single ? null : [];
        }

        return $ramVar_single
            ? (@unserialize($ramVar_rows[0]['meta_value']) ?: $ramVar_rows[0]['meta_value'])
            : array_map(static fn(array $r): mixed => @unserialize($r['meta_value']) ?: $r['meta_value'], $ramVar_rows);
    } catch (\Throwable) {
        return $ramVar_single ? null : [];
    }
}

/**
 * Update or create a post meta value.
 */
function ram_update_post_meta(int $ramVar_postId, string $ramVar_key, mixed $ramVar_value): bool
{
    if (!PatalDB::ramFn_isConnected()) {
        return false;
    }

    $ramVar_val = is_scalar($ramVar_value) ? (string) $ramVar_value : serialize($ramVar_value);

    try {
        PatalDB::ramFn_table('postmeta')->ramFn_upsert([
            'post_id'  => $ramVar_postId,
            'meta_key' => $ramVar_key,
            'meta_value' => $ramVar_val
        ]);
        return true;
    } catch (\Throwable) {
        return false;
    }
}

/**
 * Insert a new post.
 *
 * @param array<string, mixed> $ramVar_postData Post data:
 *   title, content, slug, status, post_type, author_id, featured_image, etc.
 * @return int|false Post ID on success, false on failure
 */
function ram_insert_post(array $ramVar_postData): int|false
{
    if (!PatalDB::ramFn_isConnected()) {
        return false;
    }

    $ramVar_postData = array_merge([
        'title'      => '',
        'content'   => '',
        'slug'      => ram_sanitize_slug($ramVar_postData['title'] ?? ''),
        'status'    => 'draft',
        'post_type' => 'post',
        'author_id' => ram_get_current_user_id() ?: 1,
        'featured_image' => null,
        'excerpt'   => null,
    ], $ramVar_postData);

    $ramVar_postData['created_at'] = date('Y-m-d H:i:s');
    $ramVar_postData['updated_at'] = date('Y-m-d H:i:s');

    if ($ramVar_postData['status'] === 'publish' && empty($ramVar_postData['published_at'])) {
        $ramVar_postData['published_at'] = date('Y-m-d H:i:s');
    }

    try {
        $ramVar_id = PatalDB::ramFn_table('posts')->ramFn_insert($ramVar_postData);
        error_log("ramFn_insert returned: " . var_export($ramVar_id, true));
        if ($ramVar_id === false) {
            // Try raw insert
            $pdo = PatalDB::ramFn_getPdo();
            $stmt = $pdo->prepare("INSERT INTO ram_posts (author_id, title, slug, content, status, post_type, featured_image, created_at, updated_at, published_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?)");
            $result = $stmt->execute([
                $ramVar_postData['author_id'],
                $ramVar_postData['title'],
                $ramVar_postData['slug'],
                $ramVar_postData['content'],
                $ramVar_postData['status'],
                $ramVar_postData['post_type'],
                $ramVar_postData['featured_image'],
                $ramVar_postData['published_at'] ?? null
            ]);
            if ($result) {
                return (int) $pdo->lastInsertId();
            }
        }
        return (int) $ramVar_id;
    } catch (\Throwable $e) {
        error_log("ram_insert_post error: " . $e->getMessage());
        return false;
    }
}

/**
 * Update an existing post.
 *
 * @param int $ramVar_postId Post ID
 * @param array<string, mixed> $ramVar_postData Post data to update
 * @return bool True on success, false on failure
 */
function ram_update_post(int $ramVar_postId, array $ramVar_postData): bool
{
    if (!PatalDB::ramFn_isConnected()) {
        return false;
    }

    $ramVar_postData['updated_at'] = date('Y-m-d H:i:s');

    if (isset($ramVar_postData['status']) && $ramVar_postData['status'] === 'publish') {
        $ramVar_post = ram_get_post($ramVar_postId);
        if (empty($ramVar_post['published_at'])) {
            $ramVar_postData['published_at'] = date('Y-m-d H:i:s');
        }
    }

    try {
        PatalDB::ramFn_table('posts')->ramFn_where('id', '=', $ramVar_postId)->ramFn_update($ramVar_postData);
        return true;
    } catch (\Throwable) {
        return false;
    }
}

/**
 * Delete a post.
 *
 * @param int $ramVar_postId Post ID
 * @param bool $ramVar_force Force delete (bypass trash)
 * @return bool True on success, false on failure
 */
function ram_delete_post(int $ramVar_postId, bool $ramVar_force = false): bool
{
    if (!PatalDB::ramFn_isConnected()) {
        return false;
    }

    try {
        $ramVar_post = ram_get_post($ramVar_postId);
        if (!$ramVar_post) {
            return false;
        }

        if ($ramVar_force) {
            PatalDB::ramFn_table('posts')->ramFn_where('id', '=', $ramVar_postId)->ramFn_delete();
        } else {
            PatalDB::ramFn_table('posts')->ramFn_where('id', '=', $ramVar_postId)->ramFn_update(['status' => 'trash']);
        }
        return true;
    } catch (\Throwable) {
        return false;
    }
}

/**
 * Trash or untrash a post.
 *
 * @param int $ramVar_postId Post ID
 * @return bool
 */
function ram_trash_post(int $ramVar_postId): bool
{
    return ram_update_post($ramVar_postId, ['status' => 'trash']);
}

/**
 * Publish a draft post.
 *
 * @param int $ramVar_postId Post ID
 * @return bool
 */
function ram_publish_post(int $ramVar_postId): bool
{
    return ram_update_post($ramVar_postId, ['status' => 'publish']);
}

/**
 * Get all categories.
 *
 * @return array<int, array<string, mixed>>
 */
function ram_get_categories(): array
{
    if (!PatalDB::ramFn_isConnected()) {
        return [];
    }
    try {
        return PatalDB::ramFn_selectRaw("
            SELECT t.*, tt.taxonomy, tt.description, tt.count 
            FROM ram_terms t 
            INNER JOIN ram_term_taxonomy tt ON t.id = tt.term_id 
            WHERE tt.taxonomy = 'category' 
            ORDER BY t.name ASC
        ");
    } catch (\Throwable) {
        return [];
    }
}

/**
 * Get all tags.
 *
 * @return array<int, array<string, mixed>>
 */
function ram_get_tags(): array
{
    if (!PatalDB::ramFn_isConnected()) {
        return [];
    }
    try {
        return PatalDB::ramFn_selectRaw("
            SELECT t.*, tt.taxonomy, tt.description, tt.count 
            FROM ram_terms t 
            INNER JOIN ram_term_taxonomy tt ON t.id = tt.term_id 
            WHERE tt.taxonomy = 'post_tag' 
            ORDER BY t.name ASC
        ");
    } catch (\Throwable) {
        return [];
    }
}

/**
 * Get terms for a post.
 *
 * @param int $ramVar_postId Post ID
 * @param string $ramVar_taxonomy Taxonomy name
 * @return array<int, array<string, mixed>>
 */
function ram_get_post_terms(int $ramVar_postId, string $ramVar_taxonomy = 'category'): array
{
    if (!PatalDB::ramFn_isConnected()) {
        return [];
    }
    try {
        return PatalDB::ramFn_selectRaw("
            SELECT t.*, tt.taxonomy 
            FROM ram_term_relationships tr
            INNER JOIN ram_term_taxonomy tt ON tr.term_taxonomy_id = tt.id
            INNER JOIN ram_terms t ON tt.term_id = t.id
            WHERE tr.object_id = ? AND tt.taxonomy = ?
        ", [$ramVar_postId, $ramVar_taxonomy]);
    } catch (\Throwable) {
        return [];
    }
}

/**
 * Set terms for a post.
 *
 * @param int $ramVar_postId Post ID
 * @param array<int> $ramVar_termIds Term IDs
 * @param string $ramVar_taxonomy Taxonomy name
 * @return bool
 */
function ram_set_post_terms(int $ramVar_postId, array $ramVar_termIds, string $ramVar_taxonomy = 'category'): bool
{
    if (!PatalDB::ramFn_isConnected()) {
        return false;
    }
    try {
        PatalDB::ramFn_query("
            DELETE tr FROM ram_term_relationships tr
            INNER JOIN ram_term_taxonomy tt ON tr.term_taxonomy_id = tt.id
            WHERE tr.object_id = ? AND tt.taxonomy = ?
        ", [$ramVar_postId, $ramVar_taxonomy]);

        foreach ($ramVar_termIds as $ramVar_termId) {
            $ramVar_rows = PatalDB::ramFn_selectRaw("SELECT id FROM ram_term_taxonomy WHERE term_id = ? AND taxonomy = ?", 
                [$ramVar_termId, $ramVar_taxonomy]);
            if (!empty($ramVar_rows)) {
                PatalDB::ramFn_table('term_relationships')->ramFn_insert([
                    'object_id' => $ramVar_postId,
                    'term_taxonomy_id' => $ramVar_rows[0]['id'],
                    'term_order' => 0
                ]);
            }
}
        return true;
    } catch (\Throwable) {
        return false;
    }
}

/**
 * Create a new category.
 *
 * @param string $ramVar_name Category name
 * @param string $ramVar_description Category description
 * @return int|false Category ID on success, false on failure
 */
function ram_create_category(string $ramVar_name, string $ramVar_description = ''): int|false
{
    if (!PatalDB::ramFn_isConnected()) {
        return false;
    }
    $ramVar_slug = ram_sanitize_slug($ramVar_name);
    try {
        $ramVar_termId = PatalDB::ramFn_table('terms')->ramFn_insert([
            'name' => $ramVar_name,
            'slug' => $ramVar_slug
        ]);
        if ($ramVar_termId) {
            PatalDB::ramFn_table('term_taxonomy')->ramFn_insert([
                'term_id' => $ramVar_termId,
                'taxonomy' => 'category',
                'description' => $ramVar_description,
                'count' => 0
            ]);
        }
        return (int) $ramVar_termId;
    } catch (\Throwable) {
        return false;
    }
}

/**
 * Create a new tag.
 *
 * @param string $ramVar_name Tag name
 * @param string $ramVar_description Tag description
 * @return int|false Tag ID on success, false on failure
 */
function ram_create_tag(string $ramVar_name, string $ramVar_description = ''): int|false
{
    if (!PatalDB::ramFn_isConnected()) {
        return false;
    }
    $ramVar_slug = ram_sanitize_slug($ramVar_name);
    try {
        $ramVar_termId = PatalDB::ramFn_table('terms')->ramFn_insert([
            'name' => $ramVar_name,
            'slug' => $ramVar_slug
        ]);
        if ($ramVar_termId) {
            PatalDB::ramFn_table('term_taxonomy')->ramFn_insert([
                'term_id' => $ramVar_termId,
                'taxonomy' => 'post_tag',
                'description' => $ramVar_description,
                'count' => 0
            ]);
        }
        return (int) $ramVar_termId;
    } catch (\Throwable) {
        return false;
    }
}

// ══════════════════════════════════════════════════════════════════════════════
// SECTION 5 — USER
// ══════════════════════════════════════════════════════════════════════════════
// SECTION 5 — USER
// ══════════════════════════════════════════════════════════════════════════════
// SECTION 5 — USER
// ══════════════════════════════════════════════════════════════════════

/**
 * Get the current logged-in user.
 *
 * @return array<string, mixed>|null
 */
function ram_current_user(): ?array
{
    return PanchjanyaSession::ramFn_currentUser();
}

/**
 * Check if a user is logged in.
 */
function ram_is_logged_in(): bool
{
    return PanchjanyaSession::ramFn_isLoggedIn();
}

/**
 * Get the current user ID (0 if not logged in).
 */
function ram_get_current_user_id(): int
{
    return PanchjanyaSession::ramFn_currentUserId();
}

/**
 * Check if the current user has a capability.
 */
function ram_current_user_can(string $ramVar_capability): bool
{
    return PanchjanyaSession::ramFn_currentUserCan($ramVar_capability);
}

/**
 * Get user meta value.
 *
 * @param int    $ramVar_userId  User ID
 * @param string $ramVar_key     Meta key
 * @param bool   $ramVar_single  Return single value vs array
 */
function ram_get_user_meta(int $ramVar_userId, string $ramVar_key, bool $ramVar_single = true): mixed
{
    if (!PatalDB::ramFn_isConnected()) {
        return $ramVar_single ? null : [];
    }

    try {
        $ramVar_rows = PatalDB::ramFn_table('usermeta')
            ->ramFn_where('user_id', '=', $ramVar_userId)
            ->ramFn_where('meta_key', '=', $ramVar_key)
            ->ramFn_get();

        if (empty($ramVar_rows)) {
            return $ramVar_single ? null : [];
        }

        return $ramVar_single
            ? (@unserialize($ramVar_rows[0]['meta_value']) ?: $ramVar_rows[0]['meta_value'])
            : array_map(static fn(array $r): mixed => @unserialize($r['meta_value']) ?: $r['meta_value'], $ramVar_rows);
    } catch (\Throwable) {
        return $ramVar_single ? null : [];
    }
}

/**
 * Update or create a user meta value.
 */
function ram_update_user_meta(int $ramVar_userId, string $ramVar_key, mixed $ramVar_value): bool
{
    if (!PatalDB::ramFn_isConnected()) {
        return false;
    }

    $ramVar_val = is_scalar($ramVar_value) ? (string) $ramVar_value : serialize($ramVar_value);

    try {
        PatalDB::ramFn_query(
            "INSERT INTO `" . PatalDB::ramFn_getPrefix() . "usermeta` (user_id, meta_key, meta_value) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE meta_value = VALUES(meta_value)",
            [$ramVar_userId, $ramVar_key, $ramVar_val]
        );
        return true;
    } catch (\Throwable) {
        return false;
    }
}

// ══════════════════════════════════════════════════════════════════════════════
// SECTION 6 — CACHE
// ══════════════════════════════════════════════════════════════════════════════

function ram_cache_get(string $ramVar_key, string $ramVar_group = 'default'): mixed
{
    return AmritCache::ramFn_get($ramVar_key, $ramVar_group);
}

function ram_cache_set(string $ramVar_key, mixed $ramVar_value, int $ramVar_ttl = 3600, string $ramVar_group = 'default'): bool
{
    return AmritCache::ramFn_set($ramVar_key, $ramVar_value, $ramVar_ttl, $ramVar_group);
}

function ram_cache_delete(string $ramVar_key, string $ramVar_group = 'default'): bool
{
    return AmritCache::ramFn_delete($ramVar_key, $ramVar_group);
}

function ram_cache_flush(): bool
{
    return AmritCache::ramFn_flush();
}

// ══════════════════════════════════════════════════════════════════════════════
// SECTION 7 — NONCE
// ══════════════════════════════════════════════════════════════════════════════

function ram_create_nonce(string $ramVar_action): string
{
    return SudarshanaNonce::ramFn_create($ramVar_action);
}

function ram_verify_nonce(string $ramVar_nonce, string $ramVar_action): bool
{
    return SudarshanaNonce::ramFn_check($ramVar_nonce, $ramVar_action);
}

function ram_nonce_field(string $ramVar_action, string $ramVar_name = '_ram_nonce'): string
{
    return SudarshanaNonce::ramFn_field($ramVar_action, $ramVar_name);
}

function ram_nonce_url(string $ramVar_url, string $ramVar_action): string
{
    return SudarshanaNonce::ramFn_url($ramVar_url, $ramVar_action);
}

// ══════════════════════════════════════════════════════════════════════════════
// SECTION 8 — HTTP / REDIRECTS
// ══════════════════════════════════════════════════════════════════════════════

/**
 * Safe redirect helper.
 *
 * @param string $ramVar_url    Destination URL
 * @param int    $ramVar_status HTTP status code (301 or 302)
 */
function ram_redirect(string $ramVar_url, int $ramVar_status = 302): never
{
    $ramVar_url = ram_esc_url($ramVar_url);

    if (!headers_sent()) {
        http_response_code($ramVar_status);
        header('Location: ' . $ramVar_url);
    } else {
        echo '<script>window.location=' . ram_esc_js($ramVar_url) . ';</script>';
    }

    exit;
}

/**
 * Send a JSON response and exit.
 *
 * @param mixed $ramVar_data    Data to encode
 * @param int   $ramVar_status  HTTP status code
 */
function ram_send_json(mixed $ramVar_data, int $ramVar_status = 200): never
{
    if (!headers_sent()) {
        http_response_code($ramVar_status);
        header('Content-Type: application/json; charset=UTF-8');
    }
    echo json_encode($ramVar_data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

function ram_send_json_success(mixed $ramVar_data = null, int $ramVar_status = 200): never
{
    ram_send_json(['success' => true, 'data' => $ramVar_data], $ramVar_status);
}

function ram_send_json_error(string $ramVar_message = 'Error', int $ramVar_status = 400): never
{
    ram_send_json(['success' => false, 'message' => $ramVar_message], $ramVar_status);
}

// ══════════════════════════════════════════════════════════════════════════════
// SECTION 9 — TEMPLATE HELPERS
// ══════════════════════════════════════════════════════════════════════════════

/**
 * Get the active theme URL (no trailing slash).
 */
function ram_get_theme_url(): string
{
    return Ram::ramFn_getInstance()->ramFn_getThemeUrl();
}

/**
 * Get the active theme path (filesystem).
 */
function ram_get_theme_path(): string
{
    return Ram::ramFn_getInstance()->ramFn_getThemePath();
}

/**
 * Output the site URL.
 */
function ram_site_url(string $ramVar_path = ''): string
{
    $ramVar_base = defined('RAM_URL') ? RAM_URL : '';
    return ram_esc_url($ramVar_base . ($ramVar_path !== '' ? '/' . ltrim($ramVar_path, '/') : ''));
}

/**
 * Get the admin URL.
 */
function ram_admin_url(string $ramVar_path = ''): string
{
    $ramVar_base = defined('RAM_ADMIN_URL') ? RAM_ADMIN_URL : (ram_site_url('ayodhya'));
    return ram_esc_url($ramVar_base . ($ramVar_path !== '' ? '/' . ltrim($ramVar_path, '/') : ''));
}

/**
 * Get the uploads URL.
 */
function ram_upload_url(string $ramVar_path = ''): string
{
    $ramVar_base = defined('RAM_UPLOAD_URL') ? RAM_UPLOAD_URL : (ram_site_url('uploads'));
    return ram_esc_url($ramVar_base . ($ramVar_path !== '' ? '/' . ltrim($ramVar_path, '/') : ''));
}

/**
 * Normalize a stored media URL into a browsable absolute URL.
 *
 * The database historically stored root-relative paths such as
 * "/uploads/file.png", which break when the CMS is installed in a
 * subdirectory (e.g. /rmcms). This resolves them against RAM_URL so the
 * media library renders images correctly regardless of install location.
 */
function ram_media_url(string $ramVar_url): string
{
    $ramVar_url = trim($ramVar_url);
    if ($ramVar_url === '') {
        return '';
    }

    // Already absolute.
    if (preg_match('#^https?://#i', $ramVar_url)) {
        return $ramVar_url;
    }

    $ramVar_base = defined('RAM_URL') ? rtrim(RAM_URL, '/') : '';

    // Root-relative: "/uploads/file.png" → "<RAM_URL>/uploads/file.png"
    if (str_starts_with($ramVar_url, '/')) {
        return $ramVar_base . $ramVar_url;
    }

    // Bare relative path: "2026/11/file.png" → "<RAM_URL>/uploads/2026/11/file.png"
    return $ramVar_base . '/uploads/' . ltrim($ramVar_url, '/');
}

/**
 * Fire the ram_head action (call inside <head>).
 */
function ram_head(): void
{
    AkashvaniHooks::ramFn_doAction('ram_head');
}

/**
 * Fire the ram_footer action (call before </body>).
 */
function ram_footer(): void
{
    AkashvaniHooks::ramFn_doAction('ram_footer');
}

/**
 * Get a template part from the active theme.
 *
 * @param string      $ramVar_slug Template slug (e.g. 'parts/header')
 * @param string|null $ramVar_name Optional variant name (e.g. 'minimal')
 */
function ram_get_template_part(string $ramVar_slug, ?string $ramVar_name = null): void
{
    $ramVar_themePath = Ram::ramFn_getInstance()->ramFn_getThemePath();

    $ramVar_candidates = [];
    if ($ramVar_name !== null) {
        $ramVar_candidates[] = $ramVar_themePath . '/' . $ramVar_slug . '-' . $ramVar_name . '.html.php';
    }
    $ramVar_candidates[] = $ramVar_themePath . '/' . $ramVar_slug . '.html.php';
    $ramVar_candidates[] = $ramVar_themePath . '/' . $ramVar_slug . '.php';

    foreach ($ramVar_candidates as $ramVar_file) {
        if (file_exists($ramVar_file)) {
            require $ramVar_file;
            return;
        }
    }
}

/**
 * Get the theme header.
 */
function ram_get_header(?string $ramVar_name = null): void
{
    \Ram\Template\ChitrakootEngine::ramFn_getHeader($ramVar_name);
}

/**
 * Get the theme footer.
 */
function ram_get_footer(?string $ramVar_name = null): void
{
    \Ram\Template\ChitrakootEngine::ramFn_getFooter($ramVar_name);
}

// ══════════════════════════════════════════════════════════════════════════════
// SECTION 10 — PLUGIN HOOKS
// ══════════════════════════════════════════════════════════════════════════════

/**
 * Register a plugin activation hook.
 *
 * @param string   $ramVar_file     The plugin's __FILE__
 * @param callable $ramVar_callback Function to run on activation
 */
function ram_register_activation_hook(string $ramVar_file, callable $ramVar_callback): void
{
    $ramVar_slug = basename(dirname($ramVar_file));
    AkashvaniHooks::ramFn_addAction('ram_activate_' . $ramVar_slug, $ramVar_callback);
}

/**
 * Register a plugin deactivation hook.
 */
function ram_register_deactivation_hook(string $ramVar_file, callable $ramVar_callback): void
{
    $ramVar_slug = basename(dirname($ramVar_file));
    AkashvaniHooks::ramFn_addAction('ram_deactivate_' . $ramVar_slug, $ramVar_callback);
}

/**
 * Add a custom meta box to the admin editor.
 */
function ram_add_meta_box(string $ramVar_id, string $ramVar_title, callable $ramVar_callback, string $ramVar_context = 'sidebar'): void
{
    if (!class_exists('Ram\Ayodhya\MetaBoxes', false)) {
        require_once RAM_PATH . '/ayodhya/meta-boxes.php';
    }
    \Ram\Ayodhya\MetaBoxes::ramFn_add($ramVar_id, $ramVar_title, $ramVar_callback, $ramVar_context);
}

// ══════════════════════════════════════════════════════════════════════════════
// SECTION 11 — DEBUGGING
// ══════════════════════════════════════════════════════════════════════════════

/**
 * Debug dump (only outputs when RAM_DEBUG is true).
 *
 * @param mixed ...$ramVar_vars Variables to dump
 */
function ram_dump(mixed ...$ramVar_vars): void
{
    if (!defined('RAM_DEBUG') || !RAM_DEBUG) {
        return;
    }

    echo '<pre style="background:#111;color:#0f0;padding:1rem;margin:1rem;border-radius:8px;font-size:12px;overflow:auto;z-index:9999;position:relative;">';
    foreach ($ramVar_vars as $ramVar_var) {
        var_dump($ramVar_var);
    }
    echo '</pre>';
}

/**
 * Write a message to the Ramayan error log.
 *
 * @param string $ramVar_message Log message
 * @param string $ramVar_level   'info' | 'warning' | 'error'
 */
function ram_log(string $ramVar_message, string $ramVar_level = 'info'): void
{
    if (!defined('RAM_LOG_PATH') || !is_dir(RAM_LOG_PATH)) {
        return;
    }

    $ramVar_logFile = RAM_LOG_PATH . '/ram-' . date('Y-m-d') . '.log';
    $ramVar_entry   = sprintf(
        "[%s] [%s] %s\n",
        date('Y-m-d H:i:s'),
        strtoupper($ramVar_level),
        $ramVar_message
    );

    @file_put_contents($ramVar_logFile, $ramVar_entry, FILE_APPEND | LOCK_EX);
}
