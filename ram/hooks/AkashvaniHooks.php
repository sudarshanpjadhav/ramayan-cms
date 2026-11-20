<?php
/**
 * Ramayan CMS — Hook & Event System (Akashvani)
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

namespace Ram\Hooks;

/**
 * AkashvaniHooks — Action and Filter system (Voice from the Sky).
 *
 * WordPress-compatible hook API with priority-based ordering and
 * named-arg support for PHP 8.1+.
 *
 * Actions: fire and forget (void return)
 * Filters: transform a value (returns modified value)
 */
final class AkashvaniHooks
{
    /** @var array<string, array<int, list<array{callback: callable, accepted_args: int}>>> */
    private static array $ramVar_actions = [];

    /** @var array<string, array<int, list<array{callback: callable, accepted_args: int}>>> */
    private static array $ramVar_filters = [];

    /** @var array<string, int> Count of times each action has fired */
    private static array $ramVar_didAction = [];

    /** @var string|null The hook currently being executed */
    private static ?string $ramVar_currentHook = null;

    // ─── Actions ──────────────────────────────────────────────────────────────

    /**
     * Register an action hook callback.
     *
     * @param string   $ramVar_hook         Hook name
     * @param callable $ramVar_callback     Callback to register
     * @param int      $ramVar_priority     Execution priority (lower = earlier)
     * @param int      $ramVar_acceptedArgs Number of args the callback accepts
     */
    public static function ramFn_addAction(
        string   $ramVar_hook,
        callable $ramVar_callback,
        int      $ramVar_priority     = 10,
        int      $ramVar_acceptedArgs = 1
    ): void {
        self::$ramVar_actions[$ramVar_hook][$ramVar_priority][] = [
            'callback'      => $ramVar_callback,
            'accepted_args' => $ramVar_acceptedArgs,
        ];

        // Keep priorities sorted
        ksort(self::$ramVar_actions[$ramVar_hook]);
    }

    /**
     * Fire all callbacks registered for an action hook.
     *
     * @param string $ramVar_hook Hook name
     * @param mixed  ...$ramVar_args Arguments to pass to callbacks
     */
    public static function ramFn_doAction(string $ramVar_hook, mixed ...$ramVar_args): void
    {
        self::$ramVar_currentHook = $ramVar_hook;
        self::$ramVar_didAction[$ramVar_hook] = (self::$ramVar_didAction[$ramVar_hook] ?? 0) + 1;

        if (empty(self::$ramVar_actions[$ramVar_hook])) {
            self::$ramVar_currentHook = null;
            return;
        }

        foreach (self::$ramVar_actions[$ramVar_hook] as $ramVar_priority => $ramVar_callbacks) {
            foreach ($ramVar_callbacks as $ramVar_cb) {
                $ramVar_pass = array_slice($ramVar_args, 0, $ramVar_cb['accepted_args']);
                call_user_func_array($ramVar_cb['callback'], $ramVar_pass);
            }
        }

        self::$ramVar_currentHook = null;
    }

    /**
     * Remove a specific action callback.
     *
     * @param string   $ramVar_hook     Hook name
     * @param callable $ramVar_callback The exact callable to remove
     * @param int      $ramVar_priority Priority it was registered at
     */
    public static function ramFn_removeAction(
        string   $ramVar_hook,
        callable $ramVar_callback,
        int      $ramVar_priority = 10
    ): bool {
        return self::ramFn_removeHook(self::$ramVar_actions, $ramVar_hook, $ramVar_callback, $ramVar_priority);
    }

    /**
     * Remove all callbacks for an action hook (optionally at a specific priority).
     */
    public static function ramFn_removeAllActions(string $ramVar_hook, int|false $ramVar_priority = false): void
    {
        if ($ramVar_priority === false) {
            unset(self::$ramVar_actions[$ramVar_hook]);
        } else {
            unset(self::$ramVar_actions[$ramVar_hook][$ramVar_priority]);
        }
    }

    /**
     * Check if a hook has been fired and how many times.
     */
    public static function ramFn_didAction(string $ramVar_hook): int
    {
        return self::$ramVar_didAction[$ramVar_hook] ?? 0;
    }

    /**
     * Check if a callback is registered for an action hook.
     */
    public static function ramFn_hasAction(string $ramVar_hook, callable|false $ramVar_callback = false): bool|int
    {
        return self::ramFn_hasHook(self::$ramVar_actions, $ramVar_hook, $ramVar_callback);
    }

    // ─── Filters ─────────────────────────────────────────────────────────────

    /**
     * Register a filter hook callback.
     *
     * @param string   $ramVar_hook         Hook name
     * @param callable $ramVar_callback     Callback to register
     * @param int      $ramVar_priority     Execution priority (lower = earlier)
     * @param int      $ramVar_acceptedArgs Number of args the callback accepts
     */
    public static function ramFn_addFilter(
        string   $ramVar_hook,
        callable $ramVar_callback,
        int      $ramVar_priority     = 10,
        int      $ramVar_acceptedArgs = 1
    ): void {
        self::$ramVar_filters[$ramVar_hook][$ramVar_priority][] = [
            'callback'      => $ramVar_callback,
            'accepted_args' => $ramVar_acceptedArgs,
        ];

        ksort(self::$ramVar_filters[$ramVar_hook]);
    }

    /**
     * Apply all registered filters to a value.
     *
     * @param string $ramVar_hook  Filter hook name
     * @param mixed  $ramVar_value Initial value to filter
     * @param mixed  ...$ramVar_args Additional context args (passed to callbacks that accept > 1 arg)
     * @return mixed Filtered value
     */
    public static function ramFn_applyFilter(
        string $ramVar_hook,
        mixed  $ramVar_value,
        mixed  ...$ramVar_args
    ): mixed {
        if (empty(self::$ramVar_filters[$ramVar_hook])) {
            return $ramVar_value;
        }

        foreach (self::$ramVar_filters[$ramVar_hook] as $ramVar_priority => $ramVar_callbacks) {
            foreach ($ramVar_callbacks as $ramVar_cb) {
                $ramVar_passArgs = array_merge(
                    [$ramVar_value],
                    array_slice($ramVar_args, 0, max(0, $ramVar_cb['accepted_args'] - 1))
                );
                $ramVar_value = call_user_func_array($ramVar_cb['callback'], $ramVar_passArgs);
            }
        }

        return $ramVar_value;
    }

    /**
     * Remove a specific filter callback.
     */
    public static function ramFn_removeFilter(
        string   $ramVar_hook,
        callable $ramVar_callback,
        int      $ramVar_priority = 10
    ): bool {
        return self::ramFn_removeHook(self::$ramVar_filters, $ramVar_hook, $ramVar_callback, $ramVar_priority);
    }

    /**
     * Remove all filter callbacks for a hook.
     */
    public static function ramFn_removeAllFilters(string $ramVar_hook, int|false $ramVar_priority = false): void
    {
        if ($ramVar_priority === false) {
            unset(self::$ramVar_filters[$ramVar_hook]);
        } else {
            unset(self::$ramVar_filters[$ramVar_hook][$ramVar_priority]);
        }
    }

    /**
     * Check if a filter callback is registered.
     */
    public static function ramFn_hasFilter(string $ramVar_hook, callable|false $ramVar_callback = false): bool|int
    {
        return self::ramFn_hasHook(self::$ramVar_filters, $ramVar_hook, $ramVar_callback);
    }

    // ─── Current hook ─────────────────────────────────────────────────────────

    /**
     * Get the hook currently being executed (or null if none).
     */
    public static function ramFn_currentHook(): ?string
    {
        return self::$ramVar_currentHook;
    }

    /**
     * Check if we are currently inside a specific hook.
     */
    public static function ramFn_doingAction(string $ramVar_hook): bool
    {
        return self::$ramVar_currentHook === $ramVar_hook;
    }

    // ─── Internal helpers ─────────────────────────────────────────────────────

    /**
     * @param array<string, array<int, list<array{callback: callable, accepted_args: int}>>> $ramVar_registry
     */
    private static function ramFn_removeHook(
        array    &$ramVar_registry,
        string   $ramVar_hook,
        callable $ramVar_callback,
        int      $ramVar_priority
    ): bool {
        if (empty($ramVar_registry[$ramVar_hook][$ramVar_priority])) {
            return false;
        }

        foreach ($ramVar_registry[$ramVar_hook][$ramVar_priority] as $ramVar_idx => $ramVar_cb) {
            if ($ramVar_cb['callback'] === $ramVar_callback) {
                unset($ramVar_registry[$ramVar_hook][$ramVar_priority][$ramVar_idx]);
                // Clean up empty arrays
                $ramVar_registry[$ramVar_hook][$ramVar_priority] = array_values(
                    $ramVar_registry[$ramVar_hook][$ramVar_priority]
                );
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, array<int, list<array{callback: callable, accepted_args: int}>>> $ramVar_registry
     */
    private static function ramFn_hasHook(
        array         $ramVar_registry,
        string        $ramVar_hook,
        callable|false $ramVar_callback
    ): bool|int {
        if (empty($ramVar_registry[$ramVar_hook])) {
            return false;
        }

        if ($ramVar_callback === false) {
            return true;
        }

        foreach ($ramVar_registry[$ramVar_hook] as $ramVar_priority => $ramVar_callbacks) {
            foreach ($ramVar_callbacks as $ramVar_cb) {
                if ($ramVar_cb['callback'] === $ramVar_callback) {
                    return $ramVar_priority;
                }
            }
        }

        return false;
    }

    /**
     * Reset all hooks (useful for unit testing).
     */
    public static function ramFn_resetAll(): void
    {
        self::$ramVar_actions  = [];
        self::$ramVar_filters  = [];
        self::$ramVar_didAction = [];
        self::$ramVar_currentHook = null;
    }
}
