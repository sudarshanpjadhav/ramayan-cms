<?php
/**
 * Ramayan CMS — Session & User Auth (Panchjanya)
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

namespace Ram\User;

use Ram\Database\PatalDB;
use Ram\Hooks\AkashvaniHooks;

/**
 * PanchjanyaSession — Session management and authentication.
 *
 * Handles PHP session startup with secure settings,
 * login/logout, current-user resolution, and capability checks.
 *
 * Role hierarchy (lowest → highest):
 *   subscriber → contributor → author → editor → admin → super_admin
 */
final class PanchjanyaSession
{
    /** @var array<string, mixed>|null Current logged-in user data (null = not resolved yet) */
    private static ?array $ramVar_currentUser = null;

    /** @var bool Has the current user been resolved from session */
    private static bool $ramVar_resolved = false;

    // ─── Role & Capability Maps ───────────────────────────────────────────────

    /**
     * Default capabilities per role.
     * Higher roles inherit all capabilities of lower roles.
     *
     * @var array<string, string[]>
     */
    
    private static array $ramVar_roleCapabilities = [
        'subscriber'   => ['read'],
        'contributor'  => ['read', 'edit_posts', 'delete_posts'],
        'author'       => ['read', 'edit_posts', 'delete_posts', 'publish_posts', 'upload_files'],
        'editor'       => ['read', 'edit_posts', 'delete_posts', 'publish_posts', 'upload_files',
                           'edit_others_posts', 'delete_others_posts', 'manage_categories',
                           'edit_pages', 'publish_pages', 'delete_pages', 'moderate_comments'],
        'admin'        => ['read', 'edit_posts', 'delete_posts', 'publish_posts', 'upload_files',
                           'edit_others_posts', 'delete_others_posts', 'manage_categories',
                           'edit_pages', 'publish_pages', 'delete_pages', 'moderate_comments',
                           'manage_options', 'activate_plugins', 'install_plugins', 'switch_themes',
                           'manage_users', 'edit_users', 'create_users', 'delete_users',
                           'manage_site'],
        'super_admin'  => ['*'], // Wildcard — all capabilities
    ];

    // ─── Session Boot ─────────────────────────────────────────────────────────

    /**
     * Start the PHP session with secure settings.
     * Safe to call multiple times.
     */
    public static function ramFn_start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // Secure session settings
        $ramVar_secure   = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $ramVar_lifetime = 0; // Expires when browser closes (or use RAM_SESSION_LIFETIME)

        session_set_cookie_params([
            'lifetime' => $ramVar_lifetime,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $ramVar_secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        ini_set('session.use_strict_mode',   '1');
        ini_set('session.use_only_cookies',  '1');
        ini_set('session.cookie_httponly',   '1');
        ini_set('session.gc_maxlifetime',    (string)(defined('RAM_SESSION_LIFETIME') ? RAM_SESSION_LIFETIME : 86400));

        session_name('ram_session');
        session_start();

        // Regenerate session ID periodically to prevent fixation
        if (!isset($_SESSION['ram_session_started'])) {
            session_regenerate_id(true);
            $_SESSION['ram_session_started'] = time();
        } elseif ((time() - ($_SESSION['ram_session_started'] ?? 0)) > 1800) {
            // Regenerate every 30 minutes
            session_regenerate_id(true);
            $_SESSION['ram_session_started'] = time();
        }
    }

    // ─── Login / Logout ───────────────────────────────────────────────────────

    /**
     * Authenticate a user by email/username and password.
     *
     * @param string $ramVar_login    Email address or username
     * @param string $ramVar_password Plain-text password
     * @param bool   $ramVar_remember Whether to set a persistent cookie
     * @return array<string, mixed>|null User data on success, null on failure
     */
    public static function ramFn_login(
        string $ramVar_login,
        string $ramVar_password,
        bool   $ramVar_remember = false
    ): ?array {
        // Rate-limit check (basic — proper rate limiting is SudarshanaFirewall)
        $ramVar_attemptsKey = 'login_attempts_' . md5($_SERVER['REMOTE_ADDR'] ?? '');
        $ramVar_attempts    = (int) ($_SESSION[$ramVar_attemptsKey] ?? 0);

        if ($ramVar_attempts >= 5) {
            $ramVar_lockoutKey  = 'login_lockout_' . md5($_SERVER['REMOTE_ADDR'] ?? '');
            $ramVar_lockoutTime = (int) ($_SESSION[$ramVar_lockoutKey] ?? 0);
            if (time() - $ramVar_lockoutTime < 300) {
                return null; // 5-minute lockout
            }
            // Lockout expired — reset
            $_SESSION[$ramVar_attemptsKey] = 0;
        }

        // Look up user
        if (!PatalDB::ramFn_isConnected()) {
            return null;
        }

        $ramVar_user = PatalDB::ramFn_table('users')
            ->ramFn_where('email', '=', $ramVar_login)
            ->ramFn_orWhere('username', '=', $ramVar_login)
            ->ramFn_where('status', '=', 'active')
            ->ramFn_first();

        if ($ramVar_user === null) {
            $_SESSION[$ramVar_attemptsKey] = $ramVar_attempts + 1;
            return null;
        }

        // Verify password
        if (!password_verify($ramVar_password, $ramVar_user['password_hash'])) {
            $_SESSION[$ramVar_attemptsKey] = $ramVar_attempts + 1;

            if ($ramVar_attempts + 1 >= 5) {
                $ramVar_lockoutKey              = 'login_lockout_' . md5($_SERVER['REMOTE_ADDR'] ?? '');
                $_SESSION[$ramVar_lockoutKey]   = time();
            }

            return null;
        }

        // Rehash if needed (PHP algorithm upgrade)
        if (password_needs_rehash($ramVar_user['password_hash'], PASSWORD_BCRYPT)) {
            $ramVar_newHash = password_hash($ramVar_password, PASSWORD_BCRYPT, ['cost' => 12]);
            PatalDB::ramFn_table('users')
                ->ramFn_where('id', '=', (int) $ramVar_user['id'])
                ->ramFn_update(['password_hash' => $ramVar_newHash]);
        }

        // Store in session
        $_SESSION['ram_user_id']   = (int) $ramVar_user['id'];
        $_SESSION['ram_user_role'] = $ramVar_user['role'];
        unset($_SESSION[$ramVar_attemptsKey]);

        // Older installations may not have the newer profile/login tracking columns yet.
        try {
            PatalDB::ramFn_table('users')
                ->ramFn_where('id', '=', (int) $ramVar_user['id'])
                ->ramFn_update(['last_login_at' => date('Y-m-d H:i:s')]);
        } catch (\Throwable) {
            // Keep login working even when optional columns are missing.
        }

        // Strip password hash from returned data
        unset($ramVar_user['password_hash']);
        self::$ramVar_currentUser = $ramVar_user;
        self::$ramVar_resolved    = true;

        AkashvaniHooks::ramFn_doAction('ram_login', $ramVar_user);

        return $ramVar_user;
    }

    /**
     * Log out the current user.
     */
    public static function ramFn_logout(): void
    {
        $ramVar_user = self::ramFn_currentUser();

        AkashvaniHooks::ramFn_doAction('ram_logout', $ramVar_user);

        // Clear session data
        $_SESSION = [];

        // Destroy the session cookie
        if (ini_get('session.use_cookies')) {
            $ramVar_params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 3600,
                $ramVar_params['path'],
                $ramVar_params['domain'],
                $ramVar_params['secure'],
                $ramVar_params['httponly']
            );
        }

        session_destroy();

        self::$ramVar_currentUser = null;
        self::$ramVar_resolved    = false;
    }

    // ─── Current User ─────────────────────────────────────────────────────────

    /**
     * Get the currently authenticated user data.
     *
     * @return array<string, mixed>|null User data or null if not logged in
     */
    public static function ramFn_currentUser(): ?array
    {
        if (self::$ramVar_resolved) {
            return self::$ramVar_currentUser;
        }

        self::$ramVar_resolved = true;

        if (empty($_SESSION['ram_user_id'])) {
            self::$ramVar_currentUser = null;
            return null;
        }

        if (!PatalDB::ramFn_isConnected()) {
            return null;
        }

        try {
            $ramVar_user = PatalDB::ramFn_table('users')
                ->ramFn_where('id', '=', (int) $_SESSION['ram_user_id'])
                ->ramFn_where('status', '=', 'active')
                ->ramFn_first();

            if (is_array($ramVar_user)) {
                unset($ramVar_user['password_hash']);
            }

            self::$ramVar_currentUser = $ramVar_user ?? null;
        } catch (\Throwable) {
            self::$ramVar_currentUser = null;
        }

        return self::$ramVar_currentUser;
    }

    /**
     * Check if a user is currently logged in.
     */
    public static function ramFn_isLoggedIn(): bool
    {
        return self::ramFn_currentUser() !== null;
    }

    /**
     * Get the current user ID (0 if not logged in).
     */
    public static function ramFn_currentUserId(): int
    {
        $ramVar_user = self::ramFn_currentUser();
        return $ramVar_user !== null ? (int) $ramVar_user['id'] : 0;
    }

    /**
     * Get the current user role (null if not logged in).
     */
    public static function ramFn_currentUserRole(): ?string
    {
        $ramVar_user = self::ramFn_currentUser();
        return $ramVar_user !== null ? (string) $ramVar_user['role'] : null;
    }

    // ─── Capability Checks ────────────────────────────────────────────────────

    /**
     * Check if the current user has a specific capability.
     *
     * @param string $ramVar_capability Capability slug (e.g. 'edit_posts', 'manage_options')
     */
    public static function ramFn_currentUserCan(string $ramVar_capability): bool
    {
        $ramVar_user = self::ramFn_currentUser();
        if ($ramVar_user === null) {
            return false;
        }
        return self::ramFn_userCan((int) $ramVar_user['id'], $ramVar_capability, $ramVar_user['role']);
    }

    /**
     * Check if a specific user has a capability.
     *
     * @param int    $ramVar_userId     User ID to check
     * @param string $ramVar_capability Capability slug
     * @param string|null $ramVar_role  Role (if already known, avoids DB lookup)
     */
    public static function ramFn_userCan(int $ramVar_userId, string $ramVar_capability, ?string $ramVar_role = null): bool
    {
        if ($ramVar_userId <= 0) {
            return false;
        }

        // Resolve role if not provided
        if ($ramVar_role === null) {
            try {
                $ramVar_user = PatalDB::ramFn_table('users')
                    ->ramFn_select(['role'])
                    ->ramFn_where('id', '=', $ramVar_userId)
                    ->ramFn_first();
                $ramVar_role = $ramVar_user['role'] ?? 'subscriber';
            } catch (\Throwable) {
                return false;
            }
        }

        // Allow filters to override capability checks
        $ramVar_result = AkashvaniHooks::ramFn_applyFilter(
            'ram_user_can',
            null,
            $ramVar_userId,
            $ramVar_capability,
            $ramVar_role
        );

        if ($ramVar_result !== null) {
            return (bool) $ramVar_result;
        }

        // Super admin has all capabilities
        if ($ramVar_role === 'super_admin') {
            return true;
        }

        $ramVar_caps = self::$ramVar_roleCapabilities[$ramVar_role] ?? [];

        // Check wildcard
        if (in_array('*', $ramVar_caps, true)) {
            return true;
        }

        return in_array($ramVar_capability, $ramVar_caps, true);
    }

    // ─── Password Utilities ───────────────────────────────────────────────────

    /**
     * Hash a plain-text password using bcrypt.
     *
     * @param string $ramVar_password Plain-text password
     * @return string Bcrypt hash
     */
    public static function ramFn_hashPassword(string $ramVar_password): string
    {
        return password_hash($ramVar_password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verify a plain-text password against a stored hash.
     */
    public static function ramFn_checkPassword(string $ramVar_password, string $ramVar_hash): bool
    {
        return password_verify($ramVar_password, $ramVar_hash);
    }

    /**
     * Generate a cryptographically secure random token.
     *
     * @param int $ramVar_length Token length in characters (hex encoded, so actual bytes = length / 2)
     */
    public static function ramFn_generateToken(int $ramVar_length = 32): string
    {
        return bin2hex(random_bytes(max(1, (int) ($ramVar_length / 2))));
    }

    // ─── Session Utilities ────────────────────────────────────────────────────

    /**
     * Set a session value.
     *
     * @param string $ramVar_key   Session key
     * @param mixed  $ramVar_value Value to store
     */
    public static function ramFn_setSession(string $ramVar_key, mixed $ramVar_value): void
    {
        $_SESSION['ram_' . $ramVar_key] = $ramVar_value;
    }

    /**
     * Get a session value.
     *
     * @param string $ramVar_key     Session key
     * @param mixed  $ramVar_default Default if key not found
     */
    public static function ramFn_getSession(string $ramVar_key, mixed $ramVar_default = null): mixed
    {
        return $_SESSION['ram_' . $ramVar_key] ?? $ramVar_default;
    }

    /**
     * Delete a session key.
     */
    public static function ramFn_forgetSession(string $ramVar_key): void
    {
        unset($_SESSION['ram_' . $ramVar_key]);
    }

    /**
     * Get and delete a session value (flash message pattern).
     */
    public static function ramFn_flashSession(string $ramVar_key, mixed $ramVar_default = null): mixed
    {
        $ramVar_value = self::ramFn_getSession($ramVar_key, $ramVar_default);
        self::ramFn_forgetSession($ramVar_key);
        return $ramVar_value;
    }
}
