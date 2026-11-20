<?php
/**
 * Ramayan CMS — Config Reader (RamConfig)
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

namespace Ram\Core;

/**
 * RamConfig — Reads and provides access to ramayan-config.php values.
 *
 * Config is stored as PHP constants (RAM_*). This class provides
 * a unified, typed API to read them and set runtime overrides.
 */
final class RamConfig
{
    /** @var array<string, mixed> Runtime overrides on top of constants */
    private static array $ramVar_overrides = [];

    /** @var bool Has the config file been loaded */
    private static bool $ramVar_loaded = false;

    /**
     * Load the config file and validate required constants.
     *
     * @param string $ramVar_configPath Absolute path to ramayan-config.php
     * @throws \RuntimeException If config file is missing or required constants not defined
     */
    public static function ramFn_load(string $ramVar_configPath): void
    {
        if (self::$ramVar_loaded) {
            return;
        }

        if (!file_exists($ramVar_configPath)) {
            throw new \RuntimeException(
                'Ramayan CMS: Configuration file not found at ' . $ramVar_configPath .
                '. Run the Swayamvar installer first.'
            );
        }

        // Actually load the config file (defines the RAM_* constants)
        require_once $ramVar_configPath;

        // Config file already required from index.php, but we validate constants here.
        self::ramFn_validateRequiredConstants();
        self::$ramVar_loaded = true;
    }

    /**
     * Validate that all required RAM_* constants are defined.
     *
     * @throws \RuntimeException If a required constant is missing
     */
    private static function ramFn_validateRequiredConstants(): void
    {
        $ramVar_required = [
            'RAM_DB_HOST',
            'RAM_DB_NAME',
            'RAM_DB_USER',
            'RAM_DB_PASSWORD',
            'RAM_URL',
            'RAM_AUTH_KEY',
            'RAM_NONCE_KEY',
        ];

        foreach ($ramVar_required as $ramVar_const) {
            if (!defined($ramVar_const)) {
                throw new \RuntimeException(
                    "Ramayan CMS: Required constant '{$ramVar_const}' is not defined in ramayan-config.php"
                );
            }
        }
    }

    /**
     * Get a config value by key.
     *
     * Checks runtime overrides first, then falls back to constants,
     * then the default.
     *
     * @param string $ramVar_key     Config key (e.g. 'db.host', 'cache_driver', 'debug')
     * @param mixed  $ramVar_default Value to return if key not found
     * @return mixed
     */
    public static function ramFn_get(string $ramVar_key, mixed $ramVar_default = null): mixed
    {
        // Check runtime overrides first
        if (array_key_exists($ramVar_key, self::$ramVar_overrides)) {
            return self::$ramVar_overrides[$ramVar_key];
        }

        // Map shorthand keys to constant names
        return match($ramVar_key) {
            'db', 'db_config'  => self::ramFn_getDbConfig(),
            'db.host'          => defined('RAM_DB_HOST')     ? RAM_DB_HOST     : $ramVar_default,
            'db.name'          => defined('RAM_DB_NAME')     ? RAM_DB_NAME     : $ramVar_default,
            'db.user'          => defined('RAM_DB_USER')     ? RAM_DB_USER     : $ramVar_default,
            'db.password'      => defined('RAM_DB_PASSWORD') ? RAM_DB_PASSWORD : $ramVar_default,
            'db.charset'       => defined('RAM_DB_CHARSET')  ? RAM_DB_CHARSET  : 'utf8mb4',
            'db.prefix'        => defined('RAM_DB_PREFIX')   ? RAM_DB_PREFIX   : 'ram_',
            'db.host_string'   => defined('RAM_DB_HOST')     ? RAM_DB_HOST     : 'localhost',
            'url'              => defined('RAM_URL')         ? RAM_URL         : $ramVar_default,
            'site_url'         => defined('RAM_SITE_URL')    ? RAM_SITE_URL    : (defined('RAM_URL') ? RAM_URL : $ramVar_default),
            'cache_driver'     => defined('RAM_CACHE_DRIVER')? RAM_CACHE_DRIVER: 'file',
            'cache_ttl'        => defined('RAM_CACHE_TTL')   ? RAM_CACHE_TTL   : 3600,
            'debug'            => defined('RAM_DEBUG')       ? RAM_DEBUG       : false,
            'version'          => defined('RAM_VERSION')     ? RAM_VERSION     : '1.0.0-alpha',
            'active_theme'     => defined('RAM_ACTIVE_THEME')? RAM_ACTIVE_THEME: 'ram-default',
            'auth_key'         => defined('RAM_AUTH_KEY')    ? RAM_AUTH_KEY    : '',
            'nonce_key'        => defined('RAM_NONCE_KEY')   ? RAM_NONCE_KEY   : '',
            'nonce_salt'       => defined('RAM_NONCE_SALT')  ? RAM_NONCE_SALT  : '',
            'db_prefix'        => defined('RAM_DB_PREFIX')   ? RAM_DB_PREFIX   : 'ram_',
            default            => $ramVar_default,
        };
    }

    /**
     * Build and return the DB config array.
     *
     * @return array<string, string>
     */
    private static function ramFn_getDbConfig(): array
    {
        return [
            'host'     => defined('RAM_DB_HOST')     ? RAM_DB_HOST     : 'localhost',
            'dbname'   => defined('RAM_DB_NAME')     ? RAM_DB_NAME     : '',
            'user'     => defined('RAM_DB_USER')     ? RAM_DB_USER     : '',
            'password' => defined('RAM_DB_PASSWORD') ? RAM_DB_PASSWORD : '',
            'charset'  => defined('RAM_DB_CHARSET')  ? RAM_DB_CHARSET  : 'utf8mb4',
            'prefix'   => defined('RAM_DB_PREFIX')   ? RAM_DB_PREFIX   : 'ram_',
        ];
    }

    /**
     * Set a runtime override (does NOT persist to config file).
     *
     * @param string $ramVar_key   Config key
     * @param mixed  $ramVar_value Value to set
     */
    public static function ramFn_set(string $ramVar_key, mixed $ramVar_value): void
    {
        self::$ramVar_overrides[$ramVar_key] = $ramVar_value;
    }

    /**
     * Check if the config has been loaded successfully.
     */
    public static function ramFn_isLoaded(): bool
    {
        return self::$ramVar_loaded;
    }

    /**
     * Get all config as an array (for debugging only).
     *
     * @return array<string, mixed>
     */
    public static function ramFn_all(): array
    {
        return [
            'db'           => self::ramFn_getDbConfig(),
            'url'          => self::ramFn_get('url'),
            'cache_driver' => self::ramFn_get('cache_driver'),
            'debug'        => self::ramFn_get('debug'),
            'version'      => self::ramFn_get('version'),
            'active_theme' => self::ramFn_get('active_theme'),
        ];
    }
}
