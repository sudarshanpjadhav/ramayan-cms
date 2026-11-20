<?php
/**
 * Ramayan CMS — URL Router (Pushpak)
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

namespace Ram\Router;

use Ram\Core\RamRequest;
use Ram\Core\RamResponse;
use Ram\Hooks\AkashvaniHooks;

/**
 * PushpakRouter — Routes HTTP requests to registered handlers.
 * Supports named route params (:id, :slug), all HTTP methods,
 * subdirectory installs, and CMS content fallback routing.
 */
final class PushpakRouter
{
    /** @var array<int, array{method: string, pattern: string, handler: callable|array<mixed>, name: string|null}> */
    private static array $ramVar_routes = [];

    private static ?RamRequest $ramVar_request = null;

    // ─── Route Registration ───────────────────────────────────────────────────

    public static function ramFn_get(string $ramVar_pattern, callable|array $ramVar_handler, ?string $ramVar_name = null): void
    {
        self::ramFn_addRoute('GET', $ramVar_pattern, $ramVar_handler, $ramVar_name);
    }

    public static function ramFn_post(string $ramVar_pattern, callable|array $ramVar_handler, ?string $ramVar_name = null): void
    {
        self::ramFn_addRoute('POST', $ramVar_pattern, $ramVar_handler, $ramVar_name);
    }

    public static function ramFn_put(string $ramVar_pattern, callable|array $ramVar_handler, ?string $ramVar_name = null): void
    {
        self::ramFn_addRoute('PUT', $ramVar_pattern, $ramVar_handler, $ramVar_name);
    }

    public static function ramFn_patch(string $ramVar_pattern, callable|array $ramVar_handler, ?string $ramVar_name = null): void
    {
        self::ramFn_addRoute('PATCH', $ramVar_pattern, $ramVar_handler, $ramVar_name);
    }

    public static function ramFn_delete(string $ramVar_pattern, callable|array $ramVar_handler, ?string $ramVar_name = null): void
    {
        self::ramFn_addRoute('DELETE', $ramVar_pattern, $ramVar_handler, $ramVar_name);
    }

    /** @param string[] $ramVar_methods */
    public static function ramFn_match(array $ramVar_methods, string $ramVar_pattern, callable|array $ramVar_handler, ?string $ramVar_name = null): void
    {
        foreach ($ramVar_methods as $ramVar_method) {
            self::ramFn_addRoute(strtoupper($ramVar_method), $ramVar_pattern, $ramVar_handler, $ramVar_name);
        }
    }

    private static function ramFn_addRoute(string $ramVar_method, string $ramVar_pattern, callable|array $ramVar_handler, ?string $ramVar_name): void
    {
        self::$ramVar_routes[] = compact('ramVar_method', 'ramVar_pattern', 'ramVar_handler', 'ramVar_name');
    }

    // ─── Dispatch ─────────────────────────────────────────────────────────────

    public static function ramFn_dispatch(): void
    {
        self::$ramVar_request = RamRequest::ramFn_fromGlobals();
        $ramVar_uri    = self::ramFn_normalizePath(self::$ramVar_request->ramFn_getPath());
        $ramVar_method = self::$ramVar_request->ramFn_getMethod();

        AkashvaniHooks::ramFn_doAction('ram_before_dispatch', self::$ramVar_request);

        // Try registered routes
        foreach (self::$ramVar_routes as $ramVar_route) {
            if ($ramVar_route['ramVar_method'] !== $ramVar_method) {
                continue;
            }

            $ramVar_regex = self::ramFn_buildRegex($ramVar_route['ramVar_pattern']);

            if (preg_match($ramVar_regex, $ramVar_uri, $ramVar_matches)) {
                $ramVar_params = array_filter($ramVar_matches, 'is_string', ARRAY_FILTER_USE_KEY);
                self::$ramVar_request->ramFn_setRouteParams($ramVar_params);

                AkashvaniHooks::ramFn_doAction('ram_route_matched', $ramVar_route, self::$ramVar_request);

                $ramVar_response = call_user_func($ramVar_route['ramVar_handler'], self::$ramVar_request);

                if ($ramVar_response instanceof RamResponse) {
                    $ramVar_response->ramFn_send();
                }

                AkashvaniHooks::ramFn_doAction('ram_after_dispatch', self::$ramVar_request);
                return;
            }
        }

        // Fallback: CMS content routing
        self::ramFn_dispatchContent($ramVar_uri, $ramVar_method);
    }

    // ─── CMS Content Routing ──────────────────────────────────────────────────

    private static function ramFn_dispatchContent(string $ramVar_uri, string $ramVar_method): void
    {
        if (!in_array($ramVar_method, ['GET', 'HEAD'], true)) {
            (new RamResponse('Method Not Allowed', 405))->ramFn_send();
            return;
        }

        // Allow plugins/themes to intercept
        $ramVar_handled = (bool) AkashvaniHooks::ramFn_applyFilter('ram_content_route', false, $ramVar_uri, self::$ramVar_request);
        if ($ramVar_handled) {
            return;
        }

        self::ramFn_resolveCmsContent($ramVar_uri);
    }

    private static function ramFn_resolveCmsContent(string $ramVar_uri): void
    {
        $ramVar_ram = \Ram\Core\Ram::ramFn_getInstance();

        if ($ramVar_uri === '/' || $ramVar_uri === '') {
            $ramVar_ram->ramFn_setPageType('home');
            AkashvaniHooks::ramFn_doAction('ram_render_home', self::$ramVar_request);
            self::ramFn_renderTemplate('home');
            return;
        }

        // Try DB lookup
        if (class_exists(\Ram\Database\PatalDB::class)) {
            try {
                $ramVar_slug = trim($ramVar_uri, '/');
                $ramVar_post = \Ram\Database\PatalDB::ramFn_table('posts')
                    ->ramFn_where('slug', '=', $ramVar_slug)
                    ->ramFn_where('status', '=', 'publish')
                    ->ramFn_first();

                if ($ramVar_post !== null) {
                    $ramVar_ram->ramFn_set('current_post', $ramVar_post);
                    $ramVar_type = ($ramVar_post['post_type'] ?? 'post') === 'page' ? 'page' : 'single';
                    $ramVar_ram->ramFn_setPageType($ramVar_type);
                    AkashvaniHooks::ramFn_doAction('ram_render_' . $ramVar_type, $ramVar_post, self::$ramVar_request);
                    self::ramFn_renderTemplate($ramVar_type);
                    return;
                }
            } catch (\Throwable) {
                // DB unavailable — fall through
            }
        }

        // 404
        http_response_code(404);
        $ramVar_ram->ramFn_setPageType('404');
        AkashvaniHooks::ramFn_doAction('ram_render_404', self::$ramVar_request);
        self::ramFn_renderTemplate('404');
    }

    private static function ramFn_renderTemplate(string $ramVar_type): void
    {
        $ramVar_themePath  = \Ram\Core\Ram::ramFn_getInstance()->ramFn_getThemePath();
        
        // Map content types to template files
        $ramVar_candidates = match ($ramVar_type) {
            'single'  => ['single.html.php', 'index.html.php'],
            'page'    => ['page.html.php',   'index.html.php'],
            'archive' => ['archive.html.php','index.html.php'],
            '404'     => ['404.html.php',    'index.html.php'],
            'search'  => ['search.html.php',  'index.html.php'],
            default   => ['index.html.php'],
        };

        foreach ($ramVar_candidates as $ramVar_tpl) {
            $ramVar_file = $ramVar_themePath . DIRECTORY_SEPARATOR . $ramVar_tpl;
            if (file_exists($ramVar_file)) {
                require $ramVar_file;
                return;
            }
        }

        // Minimal fallback when no theme is installed
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Ramayan CMS</title></head>';
        echo '<body style="font-family:sans-serif;text-align:center;padding:4rem;background:#0f172a;color:#f8fafc;">';
        echo '<h1>🏹 Ramayan CMS</h1>';
        echo '<p>Theme not found. Active theme path: ' . $ramVar_themePath . '</p>';
        echo '<p><a href="/rmcms/index.php?page=themes" style="color:#0ea5e9;">Go to Themes</a></p>';
        echo '</body></html>';
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Compile a route pattern to a named-capture regex.
     * :param → (?P<param>[^/]+)
     */
    private static function ramFn_buildRegex(string $ramVar_pattern): string
    {
        // Add base prefix for subdirectory installs
        $ramVar_base = self::ramFn_getBase();
        if ($ramVar_base !== '') {
            $ramVar_pattern = rtrim($ramVar_base, '/') . $ramVar_pattern;
        }

        // Split on named params
        $ramVar_regex = preg_replace_callback(
            '/:([a-zA-Z_][a-zA-Z0-9_]*)(\?)?/',
            static fn(array $m): string => isset($m[2]) && $m[2] === '?'
                ? '(?P<' . $m[1] . '>[^/]*)?'
                : '(?P<' . $m[1] . '>[^/]+)',
            preg_quote($ramVar_pattern, '#')
        );

        // preg_quote escapes colons in the original pattern — rebuild properly
        $ramVar_regex = preg_replace_callback(
            '/\\\\:([a-zA-Z_][a-zA-Z0-9_]*)(\\\\?)?/',
            static fn(array $m): string => '(?P<' . $m[1] . '>[^/]+)',
            preg_quote($ramVar_pattern, '#')
        );

        return '#^' . ($ramVar_regex ?? preg_quote($ramVar_pattern, '#')) . '$#u';
    }

    private static function ramFn_normalizePath(string $ramVar_path): string
    {
        if (($ramVar_pos = strpos($ramVar_path, '?')) !== false) {
            $ramVar_path = substr($ramVar_path, 0, $ramVar_pos);
        }

        $ramVar_path = preg_replace('#/+#', '/', $ramVar_path) ?? $ramVar_path;

        $ramVar_base = self::ramFn_getBase();
        if ($ramVar_base !== '' && str_starts_with($ramVar_path, $ramVar_base)) {
            $ramVar_path = '/' . ltrim(substr($ramVar_path, strlen($ramVar_base)), '/');
        }

        return $ramVar_path ?: '/';
    }

    private static function ramFn_getBase(): string
    {
        static $ramVar_base = null;
        if ($ramVar_base !== null) {
            return $ramVar_base;
        }
        $ramVar_script = $_SERVER['SCRIPT_NAME'] ?? '';
        $ramVar_base   = rtrim(dirname($ramVar_script), '/\\');
        if ($ramVar_base === '/' || $ramVar_base === '.') {
            $ramVar_base = '';
        }
        return $ramVar_base;
    }

    public static function ramFn_getRequest(): ?RamRequest
    {
        return self::$ramVar_request;
    }

    public static function ramFn_clearRoutes(): void
    {
        self::$ramVar_routes = [];
    }
}
