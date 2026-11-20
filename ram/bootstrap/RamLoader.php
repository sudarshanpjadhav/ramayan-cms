<?php
/**
 * Ramayan CMS — Autoloader (PSR-4 Style)
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

namespace Ram\Bootstrap;

/**
 * RamLoader — PSR-4 compatible class autoloader.
 *
 * Maps the Ram\ namespace to the ram/ directory.
 * No Composer required — pure PHP autoloader.
 */
final class RamLoader
{
    /** @var array<string, string> Namespace prefix → base directory map */
    private static array $ramVar_prefixes = [];

    /** @var bool Whether the autoloader is registered */
    private static bool $ramVar_registered = false;

    /**
     * Register the autoloader with spl_autoload_register.
     */
    public static function ramFn_register(): void
    {
        if (self::$ramVar_registered) {
            return;
        }

        // Map the Ram\ namespace to ram/ directory
        self::ramFn_addNamespace('Ram\\', RAM_PATH . '/ram/');

        spl_autoload_register([self::class, 'ramFn_loadClass'], true, true);
        self::$ramVar_registered = true;
    }

    /**
     * Add a namespace-to-directory mapping.
     *
     * @param string $ramVar_prefix    The namespace prefix (e.g. "Ram\\")
     * @param string $ramVar_baseDir   Absolute base directory for the namespace
     */
    public static function ramFn_addNamespace(string $ramVar_prefix, string $ramVar_baseDir): void
    {
        // Normalize namespace prefix
        $ramVar_prefix = trim($ramVar_prefix, '\\') . '\\';
        // Normalize base directory
        $ramVar_baseDir = rtrim($ramVar_baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!isset(self::$ramVar_prefixes[$ramVar_prefix])) {
            self::$ramVar_prefixes[$ramVar_prefix] = [];
        }

        self::$ramVar_prefixes[$ramVar_prefix][] = $ramVar_baseDir;
    }

    /**
     * Resolve a class name to a file path and require it.
     *
     * @param string $ramVar_class  Fully-qualified class name
     */
    public static function ramFn_loadClass(string $ramVar_class): bool
    {
        // Walk the namespace from most-specific to least-specific.
        $ramVar_currentPrefix = $ramVar_class;

        while (($ramVar_pos = strrpos($ramVar_currentPrefix, '\\')) !== false) {
            $ramVar_currentPrefix = substr($ramVar_currentPrefix, 0, $ramVar_pos + 1);
            $ramVar_relativeClass = substr($ramVar_class, strlen($ramVar_currentPrefix));

            if (isset(self::$ramVar_prefixes[$ramVar_currentPrefix])) {
                foreach (self::$ramVar_prefixes[$ramVar_currentPrefix] as $ramVar_baseDir) {
                    $ramVar_file = $ramVar_baseDir
                        . str_replace('\\', DIRECTORY_SEPARATOR, $ramVar_relativeClass)
                        . '.php';

                    if (self::ramFn_requireFile($ramVar_file)) {
                        return true;
                    }
                }
            }

            $ramVar_currentPrefix = rtrim($ramVar_currentPrefix, '\\');
        }

        return false;
    }

    /**
     * Require a file if it exists.
     *
     * @param string $ramVar_file Absolute file path
     */
    private static function ramFn_requireFile(string $ramVar_file): bool
    {
        if (file_exists($ramVar_file)) {
            require_once $ramVar_file;
            return true;
        }
        return false;
    }
}

// ─── Self-register immediately on include ─────────────────────────────────────
RamLoader::ramFn_register();
