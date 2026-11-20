<?php
/**
 * Ramayan CMS — Central CMS Singleton (Ram)
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

namespace Ram\Core;

/**
 * Ram — The Central CMS Class.
 *
 * Everything flows through Ram. Singleton pattern ensures one instance.
 * Stores shared state: current request, response, active theme, version info.
 */
final class Ram
{
    /** @var Ram|null Singleton instance */
    private static ?Ram $ramVar_instance = null;

    /** @var array<string, mixed> Runtime registry for shared state */
    private array $ramVar_registry = [];

    /** @var string Active theme slug */
    private string $ramVar_activeTheme = 'ram-default';

    /** @var bool Whether we are in the admin context */
    private bool $ramVar_isAdmin = false;

    /** @var bool Whether we are in an API context */
    private bool $ramVar_isApi = false;

    /** @var string Current page type: 'single'|'archive'|'page'|'home'|'404'|'search' */
    private string $ramVar_pageType = 'home';

    // ── Private constructor (singleton) ───────────────────────────────────────

    private function __construct()
    {
        // Detect context from URI
        $ramVar_uri = $_SERVER['REQUEST_URI'] ?? '/';
        $this->ramVar_isAdmin = str_contains($ramVar_uri, '/ayodhya');
        $this->ramVar_isApi   = str_contains($ramVar_uri, '/api/');

        // First try constant, then database option
        if (defined('RAM_ACTIVE_THEME')) {
            $this->ramVar_activeTheme = RAM_ACTIVE_THEME;
        } else {
            // Try to get from database (lazy load to avoid DB calls before init)
            $this->ramVar_activeTheme = 'ram-default'; // fallback
        }
    }

    // ── Singleton access ──────────────────────────────────────────────────────

    /**
     * Get the single Ram instance (creates if not exists).
     */
    public static function ramFn_getInstance(): static
    {
        if (self::$ramVar_instance === null) {
            self::$ramVar_instance = new self();
            
            // Try to load active theme from database after object creation
            if (!defined('RAM_ACTIVE_THEME') && class_exists('Ram\Database\PatalDB')) {
                try {
                    if (\Ram\Database\PatalDB::ramFn_isConnected()) {
                        $theme = \Ram\Database\PatalDB::ramFn_selectOne("SELECT option_value FROM ram_options WHERE option_name = 'active_theme'");
                        if (!empty($theme['option_value'])) {
                            self::$ramVar_instance->ramVar_activeTheme = $theme['option_value'];
                        }
                    }
                } catch (\Throwable $e) {
                    // Ignore errors, use default
                }
            }
        }
        return self::$ramVar_instance;
    }

    /** Prevent cloning */
    private function __clone() {}

    // ── Version ───────────────────────────────────────────────────────────────

    /**
     * Get the Ramayan CMS version string.
     */
    public function ramFn_version(): string
    {
        return defined('RAM_VERSION') ? RAM_VERSION : '1.0.0-alpha';
    }

    // ── Registry (key–value store for shared state) ───────────────────────────

    /**
     * Store a value in the shared registry.
     *
     * @param string $ramVar_key   Registry key
     * @param mixed  $ramVar_value Value to store
     */
    public function ramFn_set(string $ramVar_key, mixed $ramVar_value): void
    {
        $this->ramVar_registry[$ramVar_key] = $ramVar_value;
    }

    /**
     * Retrieve a value from the shared registry.
     *
     * @param string $ramVar_key     Registry key
     * @param mixed  $ramVar_default Default if key not found
     * @return mixed
     */
    public function ramFn_get(string $ramVar_key, mixed $ramVar_default = null): mixed
    {
        return $this->ramVar_registry[$ramVar_key] ?? $ramVar_default;
    }

    /**
     * Check if a registry key exists.
     */
    public function ramFn_has(string $ramVar_key): bool
    {
        return array_key_exists($ramVar_key, $this->ramVar_registry);
    }

    /**
     * Remove a registry key.
     */
    public function ramFn_forget(string $ramVar_key): void
    {
        unset($this->ramVar_registry[$ramVar_key]);
    }

    // ── Context detection ─────────────────────────────────────────────────────

    /**
     * Is the current request to the admin panel (Ayodhya)?
     */
    public function ramFn_isAdmin(): bool
    {
        return $this->ramVar_isAdmin;
    }

    /**
     * Is the current request to the REST API (Brahmaand)?
     */
    public function ramFn_isApi(): bool
    {
        return $this->ramVar_isApi;
    }

    /**
     * Set the admin context flag (called by ayodhya/index.php).
     */
    public function ramFn_setAdmin(bool $ramVar_isAdmin): void
    {
        $this->ramVar_isAdmin = $ramVar_isAdmin;
    }

    // ── Active Theme ──────────────────────────────────────────────────────────

    /**
     * Get the active theme slug.
     */
    public function ramFn_getActiveTheme(): string
    {
        return $this->ramVar_activeTheme;
    }

    /**
     * Set the active theme slug.
     */
    public function ramFn_setActiveTheme(string $ramVar_slug): void
    {
        $this->ramVar_activeTheme = $ramVar_slug;
    }

    /**
     * Get the absolute filesystem path to the active theme directory.
     */
    public function ramFn_getThemePath(): string
    {
        // Try to get from database if not already set
        $themeSlug = $this->ramVar_activeTheme;
        
        if (empty($themeSlug) || $themeSlug === 'ram-default') {
            try {
                if (class_exists('Ram\Database\PatalDB') && \Ram\Database\PatalDB::ramFn_isConnected()) {
                    $theme = \Ram\Database\PatalDB::ramFn_selectOne("SELECT option_value FROM ram_options WHERE option_name = 'active_theme'");
                    if (!empty($theme['option_value'])) {
                        $themeSlug = $theme['option_value'];
                    }
                }
            } catch (\Throwable) {}
        }
        
        return (defined('RAM_THEME_PATH') ? RAM_THEME_PATH : RAM_PATH . '/lanka')
            . DIRECTORY_SEPARATOR . $themeSlug;
    }

    /**
     * Get the URL to the active theme directory.
     */
    public function ramFn_getThemeUrl(): string
    {
        return (defined('RAM_THEME_URL') ? RAM_THEME_URL : RAM_URL . '/lanka')
            . '/' . $this->ramVar_activeTheme;
    }

    // ── Page type ─────────────────────────────────────────────────────────────

    /**
     * Set the resolved page type for the current request.
     */
    public function ramFn_setPageType(string $ramVar_type): void
    {
        $this->ramVar_pageType = $ramVar_type;
    }

    /**
     * Get the resolved page type.
     */
    public function ramFn_getPageType(): string
    {
        return $this->ramVar_pageType;
    }

    // ── Debug helpers ─────────────────────────────────────────────────────────

    /**
     * Dump the registry (debug only — never call in production).
     */
    public function ramFn_debugRegistry(): array
    {
        return $this->ramVar_registry;
    }
}
