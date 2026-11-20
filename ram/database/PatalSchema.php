<?php
/**
 * Ramayan CMS — Schema Migration Runner (Patal)
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

namespace Ram\Database;

/**
 * PatalSchema — Runs database migrations and creates all ram_* tables.
 *
 * Tracks which migrations have run in ram_migrations table.
 * Safe to run multiple times — checks before creating.
 */
final class PatalSchema
{
    /**
     * Run all pending migrations.
     * Creates tables only if they don't already exist.
     *
     * @throws \RuntimeException if DB not connected
     */
    public static function ramFn_migrate(): void
    {
        if (!PatalDB::ramFn_isConnected()) {
            throw new \RuntimeException('PatalSchema: DB not connected. Call PatalDB::ramFn_connect() first.');
        }

        self::ramFn_createMigrationsTable();

        $ramVar_migrations = self::ramFn_getMigrationList();

        foreach ($ramVar_migrations as $ramVar_name => $ramVar_callable) {
            if (!self::ramFn_hasMigrated($ramVar_name)) {
                $ramVar_callable();
                self::ramFn_markMigrated($ramVar_name);
            }
        }
    }

    /**
     * Drop all ram_* tables (use only during re-install).
     */
    public static function ramFn_dropAll(): void
    {
        $ramVar_pdo    = PatalDB::ramFn_getPdo();
        $ramVar_prefix = PatalDB::ramFn_getPrefix();

        $ramVar_pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

        $ramVar_tables = PatalDB::ramFn_selectRaw(
            "SHOW TABLES LIKE ?",
            [$ramVar_prefix . '%']
        );

        foreach ($ramVar_tables as $ramVar_row) {
            $ramVar_table = array_values($ramVar_row)[0];
            $ramVar_pdo->exec("DROP TABLE IF EXISTS `{$ramVar_table}`");
        }

        $ramVar_pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    }

    // ─── Migration registry ───────────────────────────────────────────────────

    /**
     * Return all migrations as an ordered associative array.
     *
     * @return array<string, callable>
     */
    private static function ramFn_getMigrationList(): array
    {
        return [
            '001_create_users_table'       => [self::class, 'ramFn_createUsersTable'],
            '002_create_usermeta_table'    => [self::class, 'ramFn_createUsermetaTable'],
            '003_create_posts_table'       => [self::class, 'ramFn_createPostsTable'],
            '004_create_postmeta_table'    => [self::class, 'ramFn_createPostmetaTable'],
            '005_create_terms_table'       => [self::class, 'ramFn_createTermsTable'],
            '006_create_term_taxonomy'     => [self::class, 'ramFn_createTermTaxonomyTable'],
            '007_create_term_meta'         => [self::class, 'ramFn_createTermMetaTable'],
            '008_create_term_relationships'=> [self::class, 'ramFn_createTermRelationshipsTable'],
            '009_create_options_table'     => [self::class, 'ramFn_createOptionsTable'],
            '010_create_comments_table'    => [self::class, 'ramFn_createCommentsTable'],
            '011_create_commentmeta_table' => [self::class, 'ramFn_createCommentmetaTable'],
            '012_create_sessions_table'    => [self::class, 'ramFn_createSessionsTable'],
            '013_create_cache_table'       => [self::class, 'ramFn_createCacheTable'],
            '014_create_media_table'       => [self::class, 'ramFn_createMediaTable'],
            '015_create_logs_table'        => [self::class, 'ramFn_createLogsTable'],
            '016_create_api_keys_table'    => [self::class, 'ramFn_createApiKeysTable'],
        ];
    }

    // ─── Individual migrations ────────────────────────────────────────────────

    private static function ramFn_createUsersTable(): void
    {
        $ramVar_prefix = PatalDB::ramFn_getPrefix();
        PatalDB::ramFn_getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `{$ramVar_prefix}users` (
                `id`              BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
                `username`        VARCHAR(60)      NOT NULL,
                `email`           VARCHAR(100)     NOT NULL,
                `password_hash`   VARCHAR(255)     NOT NULL,
                `display_name`    VARCHAR(100)     NOT NULL DEFAULT '',
                `first_name`      VARCHAR(60)      NOT NULL DEFAULT '',
                `last_name`       VARCHAR(60)      NOT NULL DEFAULT '',
                `role`            VARCHAR(60)      NOT NULL DEFAULT 'subscriber',
                `status`          ENUM('active','pending','banned','deleted') NOT NULL DEFAULT 'active',
                `registered_at`   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `last_login_at`   DATETIME                  DEFAULT NULL,
                `avatar_url`      VARCHAR(255)     NOT NULL DEFAULT '',
                `website`         VARCHAR(255)     NOT NULL DEFAULT '',
                `bio`             TEXT,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_username` (`username`),
                UNIQUE KEY `uq_email` (`email`),
                KEY `idx_role`   (`role`),
                KEY `idx_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private static function ramFn_createUsermetaTable(): void
    {
        $ramVar_prefix = PatalDB::ramFn_getPrefix();
        PatalDB::ramFn_getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `{$ramVar_prefix}usermeta` (
                `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id`    BIGINT UNSIGNED NOT NULL,
                `meta_key`   VARCHAR(191)    NOT NULL,
                `meta_value` LONGTEXT,
                PRIMARY KEY (`id`),
                KEY `idx_user_id`  (`user_id`),
                KEY `idx_meta_key` (`meta_key`),
                FOREIGN KEY (`user_id`) REFERENCES `{$ramVar_prefix}users`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private static function ramFn_createPostsTable(): void
    {
        $ramVar_prefix = PatalDB::ramFn_getPrefix();
        PatalDB::ramFn_getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `{$ramVar_prefix}posts` (
                `id`              BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
                `author_id`       BIGINT UNSIGNED  NOT NULL DEFAULT 1,
                `title`           TEXT             NOT NULL,
                `slug`            VARCHAR(255)     NOT NULL,
                `content`         LONGTEXT,
                `excerpt`         TEXT,
                `status`          ENUM('publish','draft','pending','private','trash','auto-draft') NOT NULL DEFAULT 'draft',
                `post_type`       VARCHAR(60)      NOT NULL DEFAULT 'post',
                `comment_status`  ENUM('open','closed') NOT NULL DEFAULT 'open',
                `password`        VARCHAR(255)     NOT NULL DEFAULT '',
                `parent_id`       BIGINT UNSIGNED           DEFAULT NULL,
                `menu_order`      INT              NOT NULL DEFAULT 0,
                `featured_image`  BIGINT UNSIGNED           DEFAULT NULL,
                `template`        VARCHAR(100)     NOT NULL DEFAULT '',
                `meta_title`      VARCHAR(255)     NOT NULL DEFAULT '',
                `meta_description`VARCHAR(320)     NOT NULL DEFAULT '',
                `created_at`      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at`      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `published_at`    DATETIME                  DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_slug_type` (`slug`, `post_type`),
                KEY `idx_status`      (`status`),
                KEY `idx_post_type`   (`post_type`),
                KEY `idx_author_id`   (`author_id`),
                KEY `idx_published_at`(`published_at`),
                KEY `idx_parent_id`   (`parent_id`),
                FOREIGN KEY (`author_id`) REFERENCES `{$ramVar_prefix}users`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private static function ramFn_createPostmetaTable(): void
    {
        $ramVar_prefix = PatalDB::ramFn_getPrefix();
        PatalDB::ramFn_getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `{$ramVar_prefix}postmeta` (
                `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `post_id`    BIGINT UNSIGNED NOT NULL,
                `meta_key`   VARCHAR(191)    NOT NULL,
                `meta_value` LONGTEXT,
                PRIMARY KEY (`id`),
                KEY `idx_post_id`  (`post_id`),
                KEY `idx_meta_key` (`meta_key`),
                FOREIGN KEY (`post_id`) REFERENCES `{$ramVar_prefix}posts`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private static function ramFn_createTermsTable(): void
    {
        $ramVar_prefix = PatalDB::ramFn_getPrefix();
        PatalDB::ramFn_getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `{$ramVar_prefix}terms` (
                `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name`       VARCHAR(200)    NOT NULL,
                `slug`       VARCHAR(200)    NOT NULL,
                `term_group` BIGINT          NOT NULL DEFAULT 0,
                PRIMARY KEY (`id`),
                KEY `idx_slug` (`slug`),
                KEY `idx_name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private static function ramFn_createTermTaxonomyTable(): void
    {
        $ramVar_prefix = PatalDB::ramFn_getPrefix();
        PatalDB::ramFn_getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `{$ramVar_prefix}term_taxonomy` (
                `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `term_id`     BIGINT UNSIGNED NOT NULL,
                `taxonomy`    VARCHAR(32)     NOT NULL,
                `description` LONGTEXT,
                `parent_id`   BIGINT UNSIGNED NOT NULL DEFAULT 0,
                `count`       BIGINT          NOT NULL DEFAULT 0,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_term_taxonomy` (`term_id`, `taxonomy`),
                KEY `idx_taxonomy` (`taxonomy`),
                FOREIGN KEY (`term_id`) REFERENCES `{$ramVar_prefix}terms`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private static function ramFn_createTermMetaTable(): void
    {
        $ramVar_prefix = PatalDB::ramFn_getPrefix();
        PatalDB::ramFn_getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `{$ramVar_prefix}term_meta` (
                `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `term_id`    BIGINT UNSIGNED NOT NULL,
                `meta_key`   VARCHAR(191)    NOT NULL,
                `meta_value` LONGTEXT,
                PRIMARY KEY (`id`),
                KEY `idx_term_id`  (`term_id`),
                KEY `idx_meta_key` (`meta_key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private static function ramFn_createTermRelationshipsTable(): void
    {
        $ramVar_prefix = PatalDB::ramFn_getPrefix();
        PatalDB::ramFn_getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `{$ramVar_prefix}term_relationships` (
                `object_id`        BIGINT UNSIGNED NOT NULL DEFAULT 0,
                `term_taxonomy_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
                `term_order`       INT             NOT NULL DEFAULT 0,
                PRIMARY KEY (`object_id`, `term_taxonomy_id`),
                KEY `idx_term_taxonomy_id` (`term_taxonomy_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private static function ramFn_createOptionsTable(): void
    {
        $ramVar_prefix = PatalDB::ramFn_getPrefix();
        PatalDB::ramFn_getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `{$ramVar_prefix}options` (
                `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `option_name`  VARCHAR(191)    NOT NULL,
                `option_value` LONGTEXT,
                `autoload`     ENUM('yes','no') NOT NULL DEFAULT 'yes',
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_option_name` (`option_name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private static function ramFn_createCommentsTable(): void
    {
        $ramVar_prefix = PatalDB::ramFn_getPrefix();
        PatalDB::ramFn_getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `{$ramVar_prefix}comments` (
                `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `post_id`       BIGINT UNSIGNED NOT NULL,
                `author_id`     BIGINT UNSIGNED          DEFAULT NULL,
                `author_name`   VARCHAR(100)    NOT NULL DEFAULT '',
                `author_email`  VARCHAR(100)    NOT NULL DEFAULT '',
                `author_ip`     VARCHAR(45)     NOT NULL DEFAULT '',
                `author_url`    VARCHAR(255)    NOT NULL DEFAULT '',
                `content`       TEXT            NOT NULL,
                `status`        ENUM('approved','pending','spam','trash') NOT NULL DEFAULT 'pending',
                `parent_id`     BIGINT UNSIGNED NOT NULL DEFAULT 0,
                `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_post_id`  (`post_id`),
                KEY `idx_status`   (`status`),
                KEY `idx_parent_id`(`parent_id`),
                FOREIGN KEY (`post_id`) REFERENCES `{$ramVar_prefix}posts`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private static function ramFn_createCommentmetaTable(): void
    {
        $ramVar_prefix = PatalDB::ramFn_getPrefix();
        PatalDB::ramFn_getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `{$ramVar_prefix}commentmeta` (
                `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `comment_id`  BIGINT UNSIGNED NOT NULL,
                `meta_key`    VARCHAR(191)    NOT NULL,
                `meta_value`  LONGTEXT,
                PRIMARY KEY (`id`),
                KEY `idx_comment_id` (`comment_id`),
                KEY `idx_meta_key`   (`meta_key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private static function ramFn_createSessionsTable(): void
    {
        $ramVar_prefix = PatalDB::ramFn_getPrefix();
        PatalDB::ramFn_getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `{$ramVar_prefix}sessions` (
                `id`         VARCHAR(128)    NOT NULL,
                `user_id`    BIGINT UNSIGNED          DEFAULT NULL,
                `data`       LONGTEXT,
                `ip`         VARCHAR(45)     NOT NULL DEFAULT '',
                `user_agent` VARCHAR(255)    NOT NULL DEFAULT '',
                `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `expires_at` DATETIME        NOT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_user_id`    (`user_id`),
                KEY `idx_expires_at` (`expires_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private static function ramFn_createCacheTable(): void
    {
        $ramVar_prefix = PatalDB::ramFn_getPrefix();
        PatalDB::ramFn_getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `{$ramVar_prefix}cache` (
                `cache_key`  VARCHAR(191) NOT NULL,
                `cache_group`VARCHAR(64)  NOT NULL DEFAULT 'default',
                `value`      LONGTEXT     NOT NULL,
                `expires_at` DATETIME              DEFAULT NULL,
                PRIMARY KEY (`cache_key`, `cache_group`),
                KEY `idx_expires_at` (`expires_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private static function ramFn_createMediaTable(): void
    {
        $ramVar_prefix = PatalDB::ramFn_getPrefix();
        PatalDB::ramFn_getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `{$ramVar_prefix}media` (
                `id`           BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
                `uploader_id`  BIGINT UNSIGNED  NOT NULL,
                `filename`     VARCHAR(255)     NOT NULL,
                `filepath`     VARCHAR(500)     NOT NULL,
                `fileurl`      VARCHAR(500)     NOT NULL,
                `filetype`     VARCHAR(100)     NOT NULL,
                `filesize`     BIGINT UNSIGNED  NOT NULL DEFAULT 0,
                `title`        VARCHAR(255)     NOT NULL DEFAULT '',
                `alt_text`     VARCHAR(255)     NOT NULL DEFAULT '',
                `caption`      TEXT,
                `description`  TEXT,
                `width`        INT UNSIGNED              DEFAULT NULL,
                `height`       INT UNSIGNED              DEFAULT NULL,
                `created_at`   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_uploader_id` (`uploader_id`),
                KEY `idx_filetype`    (`filetype`),
                FOREIGN KEY (`uploader_id`) REFERENCES `{$ramVar_prefix}users`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private static function ramFn_createLogsTable(): void
    {
        $ramVar_prefix = PatalDB::ramFn_getPrefix();
        PatalDB::ramFn_getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `{$ramVar_prefix}logs` (
                `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id`    BIGINT UNSIGNED          DEFAULT NULL,
                `action`     VARCHAR(191)    NOT NULL,
                `object_type`VARCHAR(60)     NOT NULL DEFAULT '',
                `object_id`  BIGINT UNSIGNED          DEFAULT NULL,
                `message`    TEXT,
                `ip`         VARCHAR(45)     NOT NULL DEFAULT '',
                `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_user_id`    (`user_id`),
                KEY `idx_action`     (`action`),
                KEY `idx_created_at` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private static function ramFn_createApiKeysTable(): void
    {
        $ramVar_prefix = PatalDB::ramFn_getPrefix();
        PatalDB::ramFn_getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `{$ramVar_prefix}api_keys` (
                `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id`     BIGINT UNSIGNED NOT NULL,
                `name`        VARCHAR(100)    NOT NULL,
                `api_key`     VARCHAR(64)     NOT NULL,
                `permissions` VARCHAR(255)    NOT NULL DEFAULT 'read',
                `last_used`   DATETIME                 DEFAULT NULL,
                `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `expires_at`  DATETIME                 DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_api_key` (`api_key`),
                KEY `idx_user_id` (`user_id`),
                FOREIGN KEY (`user_id`) REFERENCES `{$ramVar_prefix}users`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    // ─── Migration tracking ────────────────────────────────────────────────────

    private static function ramFn_createMigrationsTable(): void
    {
        $ramVar_prefix = PatalDB::ramFn_getPrefix();
        PatalDB::ramFn_getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `{$ramVar_prefix}migrations` (
                `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `migration`  VARCHAR(191) NOT NULL,
                `ran_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_migration` (`migration`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private static function ramFn_hasMigrated(string $ramVar_name): bool
    {
        $ramVar_prefix = PatalDB::ramFn_getPrefix();
        $ramVar_row    = PatalDB::ramFn_selectOne(
            "SELECT id FROM `{$ramVar_prefix}migrations` WHERE migration = ? LIMIT 1",
            [$ramVar_name]
        );
        return $ramVar_row !== null;
    }

    private static function ramFn_markMigrated(string $ramVar_name): void
    {
        $ramVar_prefix = PatalDB::ramFn_getPrefix();
        PatalDB::ramFn_query(
            "INSERT IGNORE INTO `{$ramVar_prefix}migrations` (migration) VALUES (?)",
            [$ramVar_name]
        );
    }
}
