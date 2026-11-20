<?php
/**
 * Ramayan CMS — Sample Configuration File
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 *
 * Instructions:
 *   1. Copy this file to ramayan-config.php
 *   2. Fill in your database credentials
 *   3. Set your site URL
 *   4. Generate a unique AUTH_KEY (use a random 64-char string)
 */

declare(strict_types=1);

// ─── Database Settings ────────────────────────────────────────────────────────
define('RAM_DB_HOST',     'localhost');
define('RAM_DB_NAME',     'your_database_name');
define('RAM_DB_USER',     'your_database_user');
define('RAM_DB_PASSWORD', 'your_database_password');
define('RAM_DB_CHARSET',  'utf8mb4');
define('RAM_DB_PREFIX',   'ram_');

// ─── Site Settings ────────────────────────────────────────────────────────────
// No trailing slash
define('RAM_URL',      'http://localhost/RamayanCMS');
define('RAM_SITE_URL', RAM_URL);

// ─── Security Keys ────────────────────────────────────────────────────────────
// Change these! Use a random 64-character string for each.
define('RAM_AUTH_KEY',         'put-your-unique-phrase-here-change-me-1');
define('RAM_SECURE_AUTH_KEY',  'put-your-unique-phrase-here-change-me-2');
define('RAM_LOGGED_IN_KEY',    'put-your-unique-phrase-here-change-me-3');
define('RAM_NONCE_KEY',        'put-your-unique-phrase-here-change-me-4');
define('RAM_AUTH_SALT',        'put-your-unique-phrase-here-change-me-5');
define('RAM_SECURE_AUTH_SALT', 'put-your-unique-phrase-here-change-me-6');
define('RAM_LOGGED_IN_SALT',   'put-your-unique-phrase-here-change-me-7');
define('RAM_NONCE_SALT',       'put-your-unique-phrase-here-change-me-8');

// ─── Cache Settings ───────────────────────────────────────────────────────────
// Drivers: 'file' | 'mysql' | 'redis'
define('RAM_CACHE_DRIVER', 'file');
define('RAM_CACHE_TTL',    3600);  // Default TTL in seconds

// ─── Path Constants ───────────────────────────────────────────────────────────
define('RAM_CONTENT_PATH', RAM_PATH . '/vaanar');      // Plugins directory
define('RAM_THEME_PATH',   RAM_PATH . '/lanka');       // Themes directory
define('RAM_UPLOAD_PATH',  RAM_PATH . '/uploads');     // User uploads
define('RAM_CACHE_PATH',   RAM_PATH . '/cache');       // Cache storage
define('RAM_ADMIN_PATH',   RAM_PATH . '/ayodhya');     // Admin panel
define('RAM_LOG_PATH',     RAM_PATH . '/logs');        // System logs

// URL equivalents
define('RAM_CONTENT_URL', RAM_URL . '/vaanar');
define('RAM_THEME_URL',   RAM_URL . '/lanka');
define('RAM_UPLOAD_URL',  RAM_URL . '/uploads');
define('RAM_ADMIN_URL',   RAM_URL . '/ayodhya');

// ─── API Settings ─────────────────────────────────────────────────────────────
define('RAM_API_VERSION', 'v1');
define('RAM_API_URL',     RAM_URL . '/api/' . RAM_API_VERSION);

// ─── Debug Mode ───────────────────────────────────────────────────────────────
// Set to true during development — NEVER true on production
define('RAM_DEBUG', false);
define('RAM_DEBUG_LOG', false);

// ─── Active Theme ─────────────────────────────────────────────────────────────
define('RAM_ACTIVE_THEME', 'ram-default');

// ─── Error Handling ───────────────────────────────────────────────────────────
if (defined('RAM_DEBUG') && RAM_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}
