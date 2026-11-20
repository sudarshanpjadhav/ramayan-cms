<?php
/**
 * Ramayan CMS — Core Bootstrap (Brahmastra)
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

namespace Ram\Bootstrap;

use Ram\Core\Ram;
use Ram\Core\RamConfig;
use Ram\Router\PushpakRouter;
use Ram\Hooks\AkashvaniHooks;
use Ram\Database\PatalDB;
use Ram\Cache\AmritCache;
use Ram\Security\SudarshanaNonce;
use Ram\Security\SudarshanaXSS;
use Ram\User\PanchjanyaSession;

/**
 * RamBootstrap — Brahmastra (The Ultimate Weapon / Foundation).
 *
 * Orchestrates the full CMS initialization sequence:
 * Config → DB → Cache → Session → Plugins → Init Hook → Route
 */
final class RamBootstrap
{
    /** @var bool Guards against double initialization */
    private static bool $ramVar_initialized = false;

    /**
     * Initialize the entire Ramayan CMS stack.
     * Called once from index.php.
     */
    public static function ramFn_init(bool $ramVar_dispatch = true): void
    {
        if (self::$ramVar_initialized) {
            return;
        }
        self::$ramVar_initialized = true;

        self::ramFn_defineRuntimeConstants();
        self::ramFn_setErrorHandling();

        // Step 1 — Load configuration
        RamConfig::ramFn_load(RAM_PATH . '/ramayan-config.php');

        self::ramFn_bootServices();

        // Step 5 — Fire early hook
        AkashvaniHooks::ramFn_doAction('ram_loaded');

        if ($ramVar_dispatch) {
            // Step 8 — Dispatch request through Pushpak router
            PushpakRouter::ramFn_dispatch();
        }
    }

    private static function ramFn_bootServices(): void
    {
        // 1. Connect to Database (Patal)
        PatalDB::ramFn_connect([
            'host'     => RAM_DB_HOST,
            'dbname'   => RAM_DB_NAME,
            'user'     => RAM_DB_USER,
            'password' => RAM_DB_PASSWORD,
            'charset'  => RAM_DB_CHARSET,
            'prefix'   => RAM_DB_PREFIX,
        ]);

        // 2. Start Cache (Amrit)
        AmritCache::ramFn_init(defined('RAM_CACHE_DRIVER') ? RAM_CACHE_DRIVER : 'file');

        // 3. Start Session (Panchjanya)
        PanchjanyaSession::ramFn_start();

        self::ramFn_loadPlugins();

        // 3.5 Boot the active theme (loads its functions.php + enqueues assets)
        \Ram\Themes\LankaThemes::ramFn_boot();

        // 4. Initialize API (Brahmaand)
        \Ram\Api\BrahmaandAPI::ramFn_init();

        // 5. Initialize SEO (Saraswati)
        \Ram\Seo\SaraswatiSEO::ramFn_init();

        // Step 7 — Fire ram_init (plugins are loaded, router not dispatched yet)
        AkashvaniHooks::ramFn_doAction('ram_init');
    }

    /**
     * Define constants that depend on runtime (URL, environment).
     */
    private static function ramFn_defineRuntimeConstants(): void
    {
        if (!defined('RAM_CONTENT_PATH')) {
            define('RAM_CONTENT_PATH', RAM_PATH . '/vaanar');
        }
        if (!defined('RAM_THEME_PATH')) {
            define('RAM_THEME_PATH', RAM_PATH . '/lanka');
        }
        if (!defined('RAM_UPLOAD_PATH')) {
            define('RAM_UPLOAD_PATH', RAM_PATH . '/uploads');
        }
        if (!defined('RAM_CACHE_PATH')) {
            define('RAM_CACHE_PATH', RAM_PATH . '/cache');
        }
        if (!defined('RAM_ADMIN_PATH')) {
            define('RAM_ADMIN_PATH', RAM_PATH . '/ayodhya');
        }
        if (!defined('RAM_LOG_PATH')) {
            define('RAM_LOG_PATH', RAM_PATH . '/logs');
        }
        if (!defined('RAM_DB_PREFIX')) {
            define('RAM_DB_PREFIX', 'ram_');
        }
        if (!defined('RAM_API_VERSION')) {
            define('RAM_API_VERSION', 'v1');
        }
        if (!defined('RAM_DEBUG')) {
            define('RAM_DEBUG', false);
        }
    }

    /**
     * Configure PHP error handling based on debug mode.
     */
    private static function ramFn_setErrorHandling(): void
    {
        if (RAM_DEBUG) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(0);
            ini_set('display_errors', '0');
            // Register a silent error logger if log path exists
            set_error_handler(static function (
                int $errno,
                string $errstr,
                string $errfile,
                int $errline
            ): bool {
                if (defined('RAM_LOG_PATH') && is_dir(RAM_LOG_PATH)) {
                    $ramVar_logFile = RAM_LOG_PATH . '/error-' . date('Y-m-d') . '.log';
                    $ramVar_msg     = sprintf(
                        "[%s] PHP Error #%d: %s in %s on line %d\n",
                        date('Y-m-d H:i:s'),
                        $errno,
                        $errstr,
                        $errfile,
                        $errline
                    );
                    @file_put_contents($ramVar_logFile, $ramVar_msg, FILE_APPEND | LOCK_EX);
                }
                return true; // Don't execute PHP internal error handler
            });
        }
    }

    /**
     * Load all active plugins from the Vaanar Sena directory.
     * Active plugin list is fetched from ram_options table.
     */
    private static function ramFn_loadPlugins(): void
    {
        // Use helper which reads from DB options or falls back gracefully
        $ramVar_activePlugins = function_exists('ram_get_option')
            ? ram_get_option('active_plugins', [])
            : [];

        if (!is_array($ramVar_activePlugins)) {
            $ramVar_activePlugins = [];
        }

        foreach ($ramVar_activePlugins as $ramVar_pluginFile) {
            // Security: only allow paths relative to vaanar/ with .php extension
            $ramVar_pluginFile = ltrim((string) $ramVar_pluginFile, '/\\');
            if (!str_ends_with($ramVar_pluginFile, '.php')) {
                continue;
            }
            if (str_contains($ramVar_pluginFile, '..')) {
                continue;
            }

            $ramVar_path = RAM_CONTENT_PATH . DIRECTORY_SEPARATOR . $ramVar_pluginFile;

            if (file_exists($ramVar_path)) {
                require_once $ramVar_path;
            }
        }

        AkashvaniHooks::ramFn_doAction('ram_plugins_loaded');
    }
}
