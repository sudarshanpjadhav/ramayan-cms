<?php
/**
 * Ramayan CMS — Plugin Manager (Vaanar)
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

namespace Ram\Plugins;

use Ram\Hooks\AkashvaniHooks;
use Ram\Database\PatalDB;

/**
 * VaanarPlugins — Manages the plugin lifecycle.
 *
 * Scans RAM_CONTENT_PATH/plugins for directories.
 * Plugins are identified by a main .php file with a header comment.
 */
final class VaanarPlugins
{
    /** @var array<string, array<string, mixed>> List of all detected plugins */
    private static array $ramVar_allPlugins = [];

    /** @var string[] List of active plugin slugs */
    private static array $ramVar_activeSlugs = [];

    /**
     * Boot the plugin system.
     * Loads active plugins from the database/options.
     */
    public static function ramFn_boot(): void
    {
        // 1. Get active slugs from options
        $ramVar_active = ram_get_option('active_plugins', '[]');
        self::$ramVar_activeSlugs = is_array($ramVar_active) ? $ramVar_active : json_decode((string)$ramVar_active, true) ?: [];

        // 2. Load active plugins
        foreach (self::$ramVar_activeSlugs as $ramVar_slug) {
            self::ramFn_loadPlugin($ramVar_slug);
        }

        AkashvaniHooks::ramFn_doAction('ram_plugins_loaded');
    }

    /**
     * Load a single plugin by its folder slug.
     */
    public static function ramFn_loadPlugin(string $ramVar_slug): bool
    {
        $ramVar_pluginDir = RAM_CONTENT_PATH . '/' . $ramVar_slug;
        $ramVar_mainFile  = $ramVar_pluginDir . '/' . $ramVar_slug . '.php';

        if (file_exists($ramVar_mainFile)) {
            // Protect plugin context
            (static function() use ($ramVar_mainFile) {
                require_once $ramVar_mainFile;
            })();
            return true;
        }

        return false;
    }

    /**
     * Get a list of all plugins in the vaanar directory.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function ramFn_getList(): array
    {
        if (!empty(self::$ramVar_allPlugins)) {
            return self::$ramVar_allPlugins;
        }

        $ramVar_path = RAM_CONTENT_PATH;
        if (!is_dir($ramVar_path)) {
            return [];
        }

        $ramVar_dirs = scandir($ramVar_path);
        foreach ($ramVar_dirs as $ramVar_dir) {
            if ($ramVar_dir[0] === '.' || !is_dir($ramVar_path . '/' . $ramVar_dir)) {
                continue;
            }

            $ramVar_pluginFile = $ramVar_path . '/' . $ramVar_dir . '/' . $ramVar_dir . '.php';
            if (file_exists($ramVar_pluginFile)) {
                $ramVar_header = self::ramFn_getPluginHeader($ramVar_pluginFile);
                if ($ramVar_header['name'] !== '') {
                    $ramVar_header['slug']   = $ramVar_dir;
                    $ramVar_header['active'] = in_array($ramVar_dir, self::$ramVar_activeSlugs, true);
                    self::$ramVar_allPlugins[$ramVar_dir] = $ramVar_header;
                }
            }
        }

        return self::$ramVar_allPlugins;
    }

    /**
     * Activate a plugin.
     */
    public static function ramFn_activate(string $ramVar_slug): bool
    {
        if (in_array($ramVar_slug, self::$ramVar_activeSlugs, true)) {
            return true;
        }

        // 1. Fire activation hook (requires loading the plugin first)
        self::ramFn_loadPlugin($ramVar_slug);
        AkashvaniHooks::ramFn_doAction('ram_activate_' . $ramVar_slug);

        // 2. Add to active list
        self::$ramVar_activeSlugs[] = $ramVar_slug;
        ram_update_option('active_plugins', self::$ramVar_activeSlugs);

        return true;
    }

    /**
     * Deactivate a plugin.
     */
    public static function ramFn_deactivate(string $ramVar_slug): bool
    {
        $ramVar_index = array_search($ramVar_slug, self::$ramVar_activeSlugs, true);
        if ($ramVar_index === false) {
            return true;
        }

        // 1. Fire deactivation hook
        AkashvaniHooks::ramFn_doAction('ram_deactivate_' . $ramVar_slug);

        // 2. Remove from active list
        unset(self::$ramVar_activeSlugs[$ramVar_index]);
        self::$ramVar_activeSlugs = array_values(self::$ramVar_activeSlugs);
        ram_update_option('active_plugins', self::$ramVar_activeSlugs);

        return true;
    }

    /**
     * Parse the plugin header comment.
     *
     * @return array{name: string, description: string, version: string, author: string, url: string}
     */
    private static function ramFn_getPluginHeader(string $ramVar_file): array
    {
        $ramVar_content = (string) file_get_contents($ramVar_file, false, null, 0, 8192);
        
        $ramVar_map = [
            'name'        => 'Plugin Name',
            'description' => 'Description',
            'version'     => 'Version',
            'author'      => 'Author',
            'url'         => 'Plugin URI',
        ];

        $ramVar_results = [];
        foreach ($ramVar_map as $ramVar_key => $ramVar_label) {
            preg_match('/' . preg_quote($ramVar_label, '/') . ':(.*)$/mi', $ramVar_content, $ramVar_matches);
            $ramVar_results[$ramVar_key] = isset($ramVar_matches[1]) ? trim($ramVar_matches[1]) : '';
        }

        return $ramVar_results;
    }
}
