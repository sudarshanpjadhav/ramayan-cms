<?php
/**
 * Ramayan CMS — Theme Engine (Lanka)
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

namespace Ram\Themes;

use Ram\Hooks\AkashvaniHooks;

/**
 * LankaThemes — Manages templates and theme assets.
 *
 * Handles the "Lanka" (Themes) directory.
 * Provides enqueue system for scripts and styles to avoid duplicates.
 */
final class LankaThemes
{
    /** @var array<string, array{src: string, deps: string[], ver: string, footer: bool}> */
    private static array $ramVar_scripts = [];

    /** @var array<string, array{src: string, deps: string[], ver: string, media: string}> */
    private static array $ramVar_styles = [];

    /** @var string Active theme slug */
    private static string $ramVar_activeTheme = 'ram-default';

    /**
     * Boot the theme engine.
     */
    public static function ramFn_boot(): void
    {
        self::$ramVar_activeTheme = ram_get_option('active_theme', 'ram-default');

        // Hook the enqueue printers into head and footer
        AkashvaniHooks::ramFn_addAction('ram_head',   [self::class, 'ramFn_printStyles'],  10);
        AkashvaniHooks::ramFn_addAction('ram_head',   [self::class, 'ramFn_printHeaderScripts'], 11);
        AkashvaniHooks::ramFn_addAction('ram_footer', [self::class, 'ramFn_printFooterScripts'], 10);

        // Load the theme's functions.php if it exists
        $ramVar_funcFile = RAM_THEME_PATH . '/' . self::$ramVar_activeTheme . '/functions.php';
        if (file_exists($ramVar_funcFile)) {
            require_once $ramVar_funcFile;
        }

        AkashvaniHooks::ramFn_doAction('ram_after_theme_setup');
    }

    // ─── Enqueue System ───────────────────────────────────────────────────────

    /**
     * Register and enqueue a CSS stylesheet.
     */
    public static function ramFn_enqueueStyle(
        string $ramVar_handle,
        string $ramVar_src,
        array  $ramVar_deps  = [],
        string $ramVar_ver   = '1.0.0',
        string $ramVar_media = 'all'
    ): void {
        self::$ramVar_styles[$ramVar_handle] = [
            'src'   => $ramVar_src,
            'deps'  => $ramVar_deps,
            'ver'   => $ramVar_ver,
            'media' => $ramVar_media,
        ];
    }

    /**
     * Register and enqueue a JavaScript file.
     */
    public static function ramFn_enqueueScript(
        string $ramVar_handle,
        string $ramVar_src,
        array  $ramVar_deps   = [],
        string $ramVar_ver    = '1.0.0',
        bool   $ramVar_footer = true
    ): void {
        self::$ramVar_scripts[$ramVar_handle] = [
            'src'    => $ramVar_src,
            'deps'   => $ramVar_deps,
            'ver'    => $ramVar_ver,
            'footer' => $ramVar_footer,
        ];
    }

    // ─── Printers ─────────────────────────────────────────────────────────────

    public static function ramFn_printStyles(): void
    {
        foreach (self::$ramVar_styles as $ramVar_handle => $ramVar_s) {
            $ramVar_url = str_contains($ramVar_s['src'], '://') ? $ramVar_s['src'] : ram_site_url($ramVar_s['src']);
            $ramVar_url = ram_esc_url($ramVar_url . '?ver=' . $ramVar_s['ver']);
            echo '<link rel="stylesheet" id="' . ram_esc_attr($ramVar_handle) . '" href="' . $ramVar_url . '" media="' . ram_esc_attr($ramVar_s['media']) . '">' . PHP_EOL;
        }
    }

    public static function ramFn_printHeaderScripts(): void
    {
        self::ramFn_printScripts(false);
    }

    public static function ramFn_printFooterScripts(): void
    {
        self::ramFn_printScripts(true);
    }

    private static function ramFn_printScripts(bool $ramVar_footer): void
    {
        foreach (self::$ramVar_scripts as $ramVar_handle => $ramVar_s) {
            if ($ramVar_s['footer'] !== $ramVar_footer) continue;

            $ramVar_url = str_contains($ramVar_s['src'], '://') ? $ramVar_s['src'] : ram_site_url($ramVar_s['src']);
            $ramVar_url = ram_esc_url($ramVar_url . '?ver=' . $ramVar_s['ver']);
            echo '<script id="' . ram_esc_attr($ramVar_handle) . '" src="' . $ramVar_url . '"></script>' . PHP_EOL;
        }
    }

    // ─── Template Resolution ──────────────────────────────────────────────────

    /**
     * Resolve and return the path to a template file.
     * Implements fallback logic: post-type-slug -> post-type -> index.
     */
    public static function ramFn_resolveTemplate(string $ramVar_type, string $ramVar_slug = ''): string
    {
        $ramVar_themeDir = RAM_THEME_PATH . '/' . self::$ramVar_activeTheme;
        
        $ramVar_candidates = [];
        if ($ramVar_slug !== '') {
            $ramVar_candidates[] = "{$ramVar_type}-{$ramVar_slug}.html.php";
        }
        $ramVar_candidates[] = "{$ramVar_type}.html.php";
        $ramVar_candidates[] = "index.html.php";

        foreach ($ramVar_candidates as $ramVar_file) {
            if (file_exists($ramVar_themeDir . '/' . $ramVar_file)) {
                return $ramVar_themeDir . '/' . $ramVar_file;
            }
        }

        // Final fallback to system default if theme is broken
        return RAM_PATH . '/ram/core/default-template.php';
    }

    /**
     * Get the active theme directory name.
     */
    public static function ramFn_getActiveTheme(): string
    {
        return self::$ramVar_activeTheme;
    }
}
