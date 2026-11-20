<?php
/**
 * Ramayan CMS — Nonce System (Sudarshana)
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

namespace Ram\Security;

/**
 * SudarshanaNonce — CSRF / request-verification nonce system.
 *
 * Nonces are time-limited, action-bound tokens that verify a request
 * originated from a trusted Ramayan CMS page, not a forged external one.
 *
 * Nonces expire in two "ticks" of RAM_NONCE_TICK_SECONDS (default 12h each),
 * giving a window of 12–24 hours — same approach as WordPress.
 *
 * Usage:
 *   $nonce = SudarshanaNonce::ramFn_create('save_post_42');
 *   // In form: <input type="hidden" name="_ram_nonce" value="<?= $nonce ?>">
 *   // On submit:
 *   SudarshanaNonce::ramFn_verify($_POST['_ram_nonce'], 'save_post_42'); // throws on fail
 *   // or:
 *   $valid = SudarshanaNonce::ramFn_check($_POST['_ram_nonce'], 'save_post_42');
 */
final class SudarshanaNonce
{
    /**
     * Nonce tick window in seconds (half the total validity window).
     * Default: 12 hours = 43200 seconds.
     * Override by defining RAM_NONCE_TICK before including this file.
     */
    private const RAM_NONCE_TICK = 43200; // 12 hours

    // ─── Create ──────────────────────────────────────────────────────────────

    /**
     * Generate a nonce for a given action.
     *
     * @param string $ramVar_action A unique string describing the action (e.g. 'delete_post_42')
     * @return string  10-character nonce token (URL-safe base64 fragment)
     */
    public static function ramFn_create(string $ramVar_action): string
    {
        $ramVar_tick   = self::ramFn_currentTick();
        $ramVar_uid    = self::ramFn_getUserId();
        $ramVar_secret = self::ramFn_getSecret();

        $ramVar_hash = hash_hmac(
            'sha256',
            implode('|', [$ramVar_tick, $ramVar_action, $ramVar_uid]),
            $ramVar_secret
        );

        // Return first 10 hex chars (40 bits of entropy — same as WP nonces)
        return substr($ramVar_hash, 0, 10);
    }

    // ─── Verify (throws on failure) ───────────────────────────────────────────

    /**
     * Verify a nonce. Throws SecurityException on failure.
     *
     * @param string $ramVar_nonce  Submitted nonce value
     * @param string $ramVar_action Action the nonce was created for
     * @throws \RuntimeException   If the nonce is invalid or expired
     */
    public static function ramFn_verify(string $ramVar_nonce, string $ramVar_action): void
    {
        if (!self::ramFn_check($ramVar_nonce, $ramVar_action)) {
            throw new \RuntimeException(
                'Sudarshana: Invalid or expired nonce for action "' . $ramVar_action . '".'
            );
        }
    }

    // ─── Check (returns bool) ─────────────────────────────────────────────────

    /**
     * Check a nonce without throwing. Returns true/false.
     *
     * Checks the current tick AND the previous tick (so nonces are valid
     * for up to 24 hours total: 12h in current window + 12h in previous).
     *
     * @param string $ramVar_nonce  Submitted nonce
     * @param string $ramVar_action Expected action
     */
    public static function ramFn_check(string $ramVar_nonce, string $ramVar_action): bool
    {
        if (!is_string($ramVar_nonce) || strlen($ramVar_nonce) !== 10) {
            return false;
        }

        $ramVar_uid    = self::ramFn_getUserId();
        $ramVar_secret = self::ramFn_getSecret();

        // Check current tick
        $ramVar_currentTick = self::ramFn_currentTick();
        $ramVar_expected    = substr(
            hash_hmac('sha256', implode('|', [$ramVar_currentTick, $ramVar_action, $ramVar_uid]), $ramVar_secret),
            0, 10
        );

        if (hash_equals($ramVar_expected, $ramVar_nonce)) {
            return true;
        }

        // Check previous tick (for nonces created near the boundary)
        $ramVar_prevTick = $ramVar_currentTick - 1;
        $ramVar_expected = substr(
            hash_hmac('sha256', implode('|', [$ramVar_prevTick, $ramVar_action, $ramVar_uid]), $ramVar_secret),
            0, 10
        );

        return hash_equals($ramVar_expected, $ramVar_nonce);
    }

    // ─── Field helpers ────────────────────────────────────────────────────────

    /**
     * Output a hidden nonce input field for use in HTML forms.
     *
     * @param string $ramVar_action   Action name
     * @param string $ramVar_fieldName Form field name (default: _ram_nonce)
     */
    public static function ramFn_field(string $ramVar_action, string $ramVar_fieldName = '_ram_nonce'): string
    {
        $ramVar_nonce = self::ramFn_create($ramVar_action);
        $ramVar_name  = htmlspecialchars($ramVar_fieldName, ENT_QUOTES, 'UTF-8');
        $ramVar_val   = htmlspecialchars($ramVar_nonce,     ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="' . $ramVar_name . '" value="' . $ramVar_val . '">';
    }

    /**
     * Output a nonce as a URL query parameter.
     *
     * @param string $ramVar_url    Base URL
     * @param string $ramVar_action Action name
     * @param string $ramVar_param  Query param name (default: _ram_nonce)
     */
    public static function ramFn_url(
        string $ramVar_url,
        string $ramVar_action,
        string $ramVar_param = '_ram_nonce'
    ): string {
        $ramVar_nonce = self::ramFn_create($ramVar_action);
        $ramVar_sep   = str_contains($ramVar_url, '?') ? '&' : '?';
        return $ramVar_url . $ramVar_sep . urlencode($ramVar_param) . '=' . urlencode($ramVar_nonce);
    }

    // ─── Internal ─────────────────────────────────────────────────────────────

    /**
     * Get the current "tick" number (integer division of time by tick window).
     */
    private static function ramFn_currentTick(): int
    {
        $ramVar_tick = defined('RAM_NONCE_TICK') ? (int) RAM_NONCE_TICK : self::RAM_NONCE_TICK;
        return (int) ceil(time() / $ramVar_tick);
    }

    /**
     * Get the authenticated user ID (0 if not logged in).
     */
    private static function ramFn_getUserId(): int
    {
        // Session user ID if set
        if (isset($_SESSION['ram_user_id'])) {
            return (int) $_SESSION['ram_user_id'];
        }
        return 0;
    }

    /**
     * Get the HMAC secret key from config.
     * Falls back to session ID + AUTH_KEY for robustness.
     */
    private static function ramFn_getSecret(): string
    {
        $ramVar_key  = defined('RAM_NONCE_KEY')  ? RAM_NONCE_KEY  : '';
        $ramVar_salt = defined('RAM_NONCE_SALT') ? RAM_NONCE_SALT : '';
        $ramVar_sid  = session_id() ?: '';
        return $ramVar_key . $ramVar_salt . $ramVar_sid;
    }
}
