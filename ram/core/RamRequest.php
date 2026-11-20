<?php
/**
 * Ramayan CMS — HTTP Request Abstraction (RamRequest)
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

namespace Ram\Core;

/**
 * RamRequest — Immutable HTTP request abstraction.
 *
 * Wraps PHP superglobals into a clean, typed, XSS-safe interface.
 * Instances are created once per request cycle by PushpakRouter.
 */
final class RamRequest
{
    /** @var string HTTP method (GET, POST, PUT, PATCH, DELETE, HEAD, OPTIONS) */
    private readonly string $ramVar_method;

    /** @var string Decoded request URI path (no query string) */
    private readonly string $ramVar_path;

    /** @var string Full request URI including query string */
    private readonly string $ramVar_uri;

    /** @var array<string, string> GET parameters */
    private readonly array $ramVar_query;

    /** @var array<string, mixed> POST parameters */
    private readonly array $ramVar_body;

    /** @var array<string, string> Uploaded files */
    private readonly array $ramVar_files;

    /** @var array<string, string> HTTP headers */
    private readonly array $ramVar_headers;

    /** @var array<string, string> Route parameters extracted by the router */
    private array $ramVar_routeParams = [];

    /**
     * Build a RamRequest from PHP superglobals.
     */
    public function __construct(
        ?string                  $ramVar_method  = null,
        ?string                  $ramVar_uri     = null,
        ?array                   $ramVar_query   = null,
        ?array                   $ramVar_body    = null,
        ?array                   $ramVar_files   = null,
        ?array                   $ramVar_headers = null,
    ) {
        $this->ramVar_method  = strtoupper($ramVar_method  ?? $_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->ramVar_uri     = $ramVar_uri     ?? $_SERVER['REQUEST_URI']  ?? '/';
        $this->ramVar_path    = parse_url($this->ramVar_uri, PHP_URL_PATH) ?? '/';
        $this->ramVar_query   = $ramVar_query   ?? $_GET  ?? [];
        $this->ramVar_body    = $ramVar_body    ?? $_POST ?? [];
        $this->ramVar_files   = $ramVar_files   ?? $_FILES ?? [];
        $this->ramVar_headers = $ramVar_headers ?? self::ramFn_extractHeaders();
    }

    /**
     * Create a RamRequest from current PHP superglobals.
     */
    public static function ramFn_fromGlobals(): self
    {
        return new self();
    }

    // ─── Method ──────────────────────────────────────────────────────────────

    /** Get the HTTP method (always uppercase). */
    public function ramFn_getMethod(): string
    {
        return $this->ramVar_method;
    }

    public function ramFn_isGet(): bool    { return $this->ramVar_method === 'GET'; }
    public function ramFn_isPost(): bool   { return $this->ramVar_method === 'POST'; }
    public function ramFn_isPut(): bool    { return $this->ramVar_method === 'PUT'; }
    public function ramFn_isPatch(): bool  { return $this->ramVar_method === 'PATCH'; }
    public function ramFn_isDelete(): bool { return $this->ramVar_method === 'DELETE'; }

    /** Check if the request was made via AJAX (X-Requested-With header). */
    public function ramFn_isAjax(): bool
    {
        return strtolower($this->ramVar_headers['x-requested-with'] ?? '') === 'xmlhttprequest';
    }

    /** Check if request expects JSON (Accept header). */
    public function ramFn_wantsJson(): bool
    {
        $ramVar_accept = $this->ramVar_headers['accept'] ?? '';
        return str_contains($ramVar_accept, 'application/json');
    }

    // ─── Path & URI ──────────────────────────────────────────────────────────

    /** Get the request URI path (no query string). */
    public function ramFn_getPath(): string
    {
        return $this->ramVar_path;
    }

    /** Get the full request URI. */
    public function ramFn_getUri(): string
    {
        return $this->ramVar_uri;
    }

    /** Get the client IP address. */
    public function ramFn_getIp(): string
    {
        // Check common proxy headers
        foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'] as $ramVar_key) {
            if (!empty($_SERVER[$ramVar_key])) {
                $ramVar_ip = trim(explode(',', $_SERVER[$ramVar_key])[0]);
                if (filter_var($ramVar_ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ramVar_ip;
                }
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    // ─── Query String ($_GET) ─────────────────────────────────────────────────

    /**
     * Get a query string parameter.
     *
     * @param string $ramVar_key     Parameter name
     * @param mixed  $ramVar_default Default if missing
     */
    public function ramFn_query(string $ramVar_key, mixed $ramVar_default = null): mixed
    {
        return $this->ramVar_query[$ramVar_key] ?? $ramVar_default;
    }

    /**
     * Get all query parameters.
     * @return array<string, mixed>
     */
    public function ramFn_queryAll(): array
    {
        return $this->ramVar_query;
    }

    // ─── Request Body ($_POST / JSON body) ───────────────────────────────────

    /**
     * Get a POST body parameter.
     *
     * @param string $ramVar_key     Parameter name
     * @param mixed  $ramVar_default Default if missing
     */
    public function ramFn_input(string $ramVar_key, mixed $ramVar_default = null): mixed
    {
        // Check POST body first, then JSON body
        if (array_key_exists($ramVar_key, $this->ramVar_body)) {
            return $this->ramVar_body[$ramVar_key];
        }

        $ramVar_json = $this->ramFn_json();
        return $ramVar_json[$ramVar_key] ?? $ramVar_default;
    }

    /**
     * Get all POST body parameters.
     * @return array<string, mixed>
     */
    public function ramFn_all(): array
    {
        return array_merge($this->ramVar_body, $this->ramFn_json());
    }

    /**
     * Parse and return JSON body (if Content-Type is application/json).
     * @return array<string, mixed>
     */
    public function ramFn_json(): array
    {
        static $ramVar_parsed = null;
        if ($ramVar_parsed !== null) {
            return $ramVar_parsed;
        }

        $ramVar_contentType = $this->ramVar_headers['content-type'] ?? '';
        if (str_contains($ramVar_contentType, 'application/json')) {
            $ramVar_raw  = file_get_contents('php://input') ?: '{}';
            $ramVar_data = json_decode($ramVar_raw, true);
            $ramVar_parsed = is_array($ramVar_data) ? $ramVar_data : [];
        } else {
            $ramVar_parsed = [];
        }

        return $ramVar_parsed;
    }

    // ─── Headers ─────────────────────────────────────────────────────────────

    /**
     * Get an HTTP header value (case-insensitive).
     *
     * @param string $ramVar_name    Header name
     * @param string $ramVar_default Default if not found
     */
    public function ramFn_header(string $ramVar_name, string $ramVar_default = ''): string
    {
        return $this->ramVar_headers[strtolower($ramVar_name)] ?? $ramVar_default;
    }

    /**
     * Get the Authorization header bearer token.
     */
    public function ramFn_bearerToken(): ?string
    {
        $ramVar_auth = $this->ramVar_headers['authorization'] ?? '';
        if (str_starts_with($ramVar_auth, 'Bearer ')) {
            return substr($ramVar_auth, 7);
        }
        return null;
    }

    // ─── Files ($_FILES) ─────────────────────────────────────────────────────

    /**
     * Get an uploaded file array.
     *
     * @param string $ramVar_key Field name
     * @return array<string, mixed>|null
     */
    public function ramFn_file(string $ramVar_key): ?array
    {
        return isset($this->ramVar_files[$ramVar_key]) ? $this->ramVar_files[$ramVar_key] : null;
    }

    /**
     * Check if a file was uploaded.
     */
    public function ramFn_hasFile(string $ramVar_key): bool
    {
        return isset($this->ramVar_files[$ramVar_key])
            && $this->ramVar_files[$ramVar_key]['error'] === UPLOAD_ERR_OK;
    }

    // ─── Route Parameters ─────────────────────────────────────────────────────

    /**
     * Set route parameters (called by PushpakRouter after matching).
     *
     * @param array<string, string> $ramVar_params
     */
    public function ramFn_setRouteParams(array $ramVar_params): void
    {
        $this->ramVar_routeParams = $ramVar_params;
    }

    /**
     * Get a route parameter (e.g. :id from /posts/:id).
     *
     * @param string $ramVar_key     Parameter name
     * @param mixed  $ramVar_default Default if not found
     */
    public function ramFn_route(string $ramVar_key, mixed $ramVar_default = null): mixed
    {
        return $this->ramVar_routeParams[$ramVar_key] ?? $ramVar_default;
    }

    /**
     * Get all route parameters.
     * @return array<string, string>
     */
    public function ramFn_routeParams(): array
    {
        return $this->ramVar_routeParams;
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Extract HTTP headers from $_SERVER.
     * @return array<string, string>
     */
    private static function ramFn_extractHeaders(): array
    {
        $ramVar_headers = [];

        if (function_exists('getallheaders')) {
            foreach (getallheaders() as $ramVar_name => $ramVar_value) {
                $ramVar_headers[strtolower($ramVar_name)] = $ramVar_value;
            }
            return $ramVar_headers;
        }

        // Fallback: parse $_SERVER
        foreach ($_SERVER as $ramVar_key => $ramVar_value) {
            if (str_starts_with($ramVar_key, 'HTTP_')) {
                $ramVar_name = strtolower(str_replace('_', '-', substr($ramVar_key, 5)));
                $ramVar_headers[$ramVar_name] = $ramVar_value;
            } elseif (in_array($ramVar_key, ['CONTENT_TYPE', 'CONTENT_LENGTH', 'CONTENT_MD5'], true)) {
                $ramVar_name = strtolower(str_replace('_', '-', $ramVar_key));
                $ramVar_headers[$ramVar_name] = $ramVar_value;
            }
        }

        return $ramVar_headers;
    }
}
