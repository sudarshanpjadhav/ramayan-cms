<?php
/**
 * Ramayan CMS — HTTP Response Abstraction (RamResponse)
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

namespace Ram\Core;

/**
 * RamResponse — HTTP response builder and sender.
 *
 * Fluent interface for setting status, headers, and body.
 * Supports HTML, JSON, plain text, and redirect responses.
 */
class RamResponse
{
    /** @var int HTTP status code */
    private int $ramVar_statusCode;

    /** @var array<string, string> Response headers */
    private array $ramVar_headers = [];

    /** @var string Response body */
    private string $ramVar_body = '';

    /** @var string Content type */
    private string $ramVar_contentType = 'text/html; charset=UTF-8';

    /**
     * @param mixed $ramVar_body       Body string, or data array for JSON
     * @param int   $ramVar_statusCode HTTP status code
     * @param array<string, string> $ramVar_headers Additional headers
     */
    public function __construct(
        mixed $ramVar_body       = '',
        int   $ramVar_statusCode = 200,
        array $ramVar_headers    = []
    ) {
        $this->ramVar_statusCode = $ramVar_statusCode;
        $this->ramVar_headers    = $ramVar_headers;

        if (is_array($ramVar_body) || is_object($ramVar_body)) {
            $this->ramFn_asJson($ramVar_body);
        } else {
            $this->ramVar_body = (string) $ramVar_body;
        }
    }

    // ─── Static factories ─────────────────────────────────────────────────────

    /**
     * Create a 200 HTML response.
     */
    public static function ramFn_html(string $ramVar_content, int $ramVar_status = 200): static
    {
        return new static($ramVar_content, $ramVar_status);
    }

    /**
     * Create a JSON response.
     *
     * @param array<mixed>|object $ramVar_data Data to encode
     * @param int $ramVar_status HTTP status code
     */
    public static function ramFn_json(array|object $ramVar_data, int $ramVar_status = 200): static
    {
        $ramVar_response = new static('', $ramVar_status);
        $ramVar_response->ramFn_asJson($ramVar_data);
        return $ramVar_response;
    }

    /**
     * Create a redirect response.
     *
     * @param string $ramVar_url    Destination URL
     * @param int    $ramVar_status HTTP status code (301 or 302)
     */
    public static function ramFn_redirect(string $ramVar_url, int $ramVar_status = 302): static
    {
        $ramVar_response = new static('', $ramVar_status);
        $ramVar_response->ramFn_withHeader('Location', $ramVar_url);
        return $ramVar_response;
    }

    /**
     * Create a 404 Not Found response.
     */
    public static function ramFn_notFound(string $ramVar_message = 'Not Found'): static
    {
        return new static($ramVar_message, 404);
    }

    /**
     * Create a 403 Forbidden response.
     */
    public static function ramFn_forbidden(string $ramVar_message = 'Forbidden'): static
    {
        return new static($ramVar_message, 403);
    }

    /**
     * Create a 500 Server Error response.
     */
    public static function ramFn_serverError(string $ramVar_message = 'Internal Server Error'): static
    {
        return new static($ramVar_message, 500);
    }

    // ─── Fluent setters ───────────────────────────────────────────────────────

    /**
     * Set the HTTP status code.
     */
    public function ramFn_withStatus(int $ramVar_code): static
    {
        $this->ramVar_statusCode = $ramVar_code;
        return $this;
    }

    /**
     * Add or override a response header.
     *
     * @param string $ramVar_name  Header name
     * @param string $ramVar_value Header value
     */
    public function ramFn_withHeader(string $ramVar_name, string $ramVar_value): static
    {
        $this->ramVar_headers[$ramVar_name] = $ramVar_value;
        return $this;
    }

    /**
     * Set the body content.
     */
    public function ramFn_withBody(string $ramVar_body): static
    {
        $this->ramVar_body = $ramVar_body;
        return $this;
    }

    /**
     * Set the Content-Type header.
     */
    public function ramFn_withContentType(string $ramVar_type): static
    {
        $this->ramVar_contentType = $ramVar_type;
        return $this;
    }

    /**
     * Convert to JSON response body and set content type.
     *
     * @param array<mixed>|object $ramVar_data Data to encode
     */
    public function ramFn_asJson(array|object $ramVar_data): static
    {
        $this->ramVar_body        = (string) json_encode($ramVar_data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $this->ramVar_contentType = 'application/json; charset=UTF-8';
        return $this;
    }

    // ─── Getters ─────────────────────────────────────────────────────────────

    public function ramFn_getStatusCode(): int  { return $this->ramVar_statusCode; }
    public function ramFn_getBody(): string      { return $this->ramVar_body; }
    public function ramFn_getContentType(): string { return $this->ramVar_contentType; }

    /**
     * Get all headers (includes Content-Type).
     * @return array<string, string>
     */
    public function ramFn_getHeaders(): array
    {
        return array_merge(['Content-Type' => $this->ramVar_contentType], $this->ramVar_headers);
    }

    // ─── Send ─────────────────────────────────────────────────────────────────

    /**
     * Send the response to the browser (sets headers + echoes body).
     */
    public function ramFn_send(): void
    {
        if (headers_sent()) {
            echo $this->ramVar_body;
            return;
        }

        http_response_code($this->ramVar_statusCode);

        foreach ($this->ramFn_getHeaders() as $ramVar_name => $ramVar_value) {
            header("{$ramVar_name}: {$ramVar_value}");
        }

        echo $this->ramVar_body;
    }

    /**
     * Send and terminate execution.
     */
    public function ramFn_sendAndExit(): never
    {
        $this->ramFn_send();
        exit;
    }
}
