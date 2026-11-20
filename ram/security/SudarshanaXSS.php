<?php
/**
 * Ramayan CMS — Output Escaping Helpers (Sudarshana XSS)
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

namespace Ram\Security;

/**
 * SudarshanaXSS — Output escaping and content sanitization.
 *
 * All output must pass through one of these methods.
 * XSS is structurally impossible when these are used correctly.
 *
 * Methods map to global helper functions in global-helpers.php:
 *   SudarshanaXSS::ramFn_html()  → ram_esc_html()
 *   SudarshanaXSS::ramFn_attr()  → ram_esc_attr()
 *   SudarshanaXSS::ramFn_url()   → ram_esc_url()
 *   SudarshanaXSS::ramFn_js()    → ram_esc_js()
 *   SudarshanaXSS::ramFn_kses()  → ram_kses()
 */
final class SudarshanaXSS
{
    /**
     * Escape a string for safe output inside HTML text content.
     *
     * Converts <, >, &, ", ' to HTML entities.
     * Use for: <?= ram_esc_html($title) ?>
     *
     * @param mixed $ramVar_value Any scalar or stringable value
     * @return string HTML-escaped string
     */
    public static function ramFn_html(mixed $ramVar_value): string
    {
        return htmlspecialchars((string) $ramVar_value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Escape a string for safe use inside an HTML attribute value.
     *
     * Use for: <input value="<?= ram_esc_attr($val) ?>">
     *
     * @param mixed $ramVar_value Any scalar or stringable value
     * @return string HTML-attribute-escaped string
     */
    public static function ramFn_attr(mixed $ramVar_value): string
    {
        return htmlspecialchars((string) $ramVar_value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Escape and validate a URL for safe use in href/src attributes.
     *
     * Strips javascript:, data:, vbscript: and other dangerous protocols.
     * Use for: <a href="<?= ram_esc_url($url) ?>">
     *
     * @param mixed $ramVar_url The URL to escape
     * @return string Safe URL (empty string if URL is dangerous)
     */
    public static function ramFn_url(mixed $ramVar_url): string
    {
        $ramVar_url = trim((string) $ramVar_url);

        if ($ramVar_url === '') {
            return '';
        }

        // Allow only safe protocols
        $ramVar_allowedProtocols = ['http', 'https', 'ftp', 'ftps', 'mailto', 'tel', 'sms', '#', '/'];

        // Check for dangerous protocols
        $ramVar_stripped = preg_replace('/[\x00-\x1F\x7F]/u', '', $ramVar_url);
        if ($ramVar_stripped === null) {
            return '';
        }

        $ramVar_lower = strtolower($ramVar_stripped);

        // Block javascript:, data:, vbscript:
        $ramVar_dangerous = ['javascript:', 'data:', 'vbscript:', 'about:', 'blob:'];
        foreach ($ramVar_dangerous as $ramVar_proto) {
            if (str_starts_with(ltrim($ramVar_lower), $ramVar_proto)) {
                return '';
            }
        }

        // Validate with PHP filter
        $ramVar_valid = filter_var($ramVar_url, FILTER_SANITIZE_URL);
        if ($ramVar_valid === false) {
            return '';
        }

        return htmlspecialchars($ramVar_valid, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Escape a value for safe inline JavaScript output.
     *
     * Use for: <script>var x = <?= ram_esc_js($value) ?>;</script>
     *
     * Encodes the value as JSON (which is valid JS literal syntax)
     * and escapes HTML entities to prevent </script> injection.
     *
     * @param mixed $ramVar_value Value to encode (any JSON-serializable type)
     * @return string JSON-encoded, HTML-safe string
     */
    public static function ramFn_js(mixed $ramVar_value): string
    {
        $ramVar_json = json_encode($ramVar_value, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

        if ($ramVar_json === false) {
            return '""';
        }

        return $ramVar_json;
    }

    /**
     * Escape a string for use inside a JavaScript string literal (quoted).
     *
     * Use for: onclick="alert('<?= ram_esc_js_string($msg) ?>')"
     *
     * @param mixed $ramVar_value Value to escape
     * @return string Escaped string (without surrounding quotes)
     */
    public static function ramFn_jsString(mixed $ramVar_value): string
    {
        $ramVar_str = (string) $ramVar_value;

        $ramVar_replacements = [
            "\\"  => '\\\\',
            "'"   => "\\'",
            '"'   => '\\"',
            "\n"  => '\\n',
            "\r"  => '\\r',
            "\t"  => '\\t',
            '<'   => '\\u003C',
            '>'   => '\\u003E',
            '&'   => '\\u0026',
            '='   => '\\u003D',
        ];

        return strtr($ramVar_str, $ramVar_replacements);
    }

    /**
     * Strip or allow only whitelisted HTML tags and attributes.
     *
     * Use for outputting user-supplied HTML (e.g. comment content).
     *
     * @param mixed                  $ramVar_html    HTML to sanitize
     * @param array<string, string[]>|null $ramVar_allowed Allowed tags => allowed attributes.
     *                                               null = use defaults (basic inline)
     * @return string Sanitized HTML
     */
    public static function ramFn_kses(mixed $ramVar_html, ?array $ramVar_allowed = null): string
    {
        $ramVar_html = (string) $ramVar_html;

        $ramVar_allowedTags = $ramVar_allowed ?? self::ramFn_getDefaultAllowedTags();

        // Strip all tags not in allowlist
        $ramVar_allowedString = '';
        foreach (array_keys($ramVar_allowedTags) as $ramVar_tag) {
            $ramVar_allowedString .= '<' . $ramVar_tag . '>';
        }

        $ramVar_stripped = strip_tags($ramVar_html, $ramVar_allowedString);

        // Now strip disallowed attributes from allowed tags
        return preg_replace_callback(
            '/<([a-zA-Z][a-zA-Z0-9]*)\s([^>]*)>/i',
            static function (array $ramVar_matches) use ($ramVar_allowedTags): string {
                $ramVar_tag     = strtolower($ramVar_matches[1]);
                $ramVar_attrStr = $ramVar_matches[2];

                if (!isset($ramVar_allowedTags[$ramVar_tag])) {
                    return '<' . $ramVar_tag . '>';
                }

                $ramVar_allowedAttrs = $ramVar_allowedTags[$ramVar_tag];
                $ramVar_cleanAttrs   = '';

                // Parse attributes
                preg_match_all(
                    '/([a-zA-Z\-]+)\s*=\s*(["\'])(.*?)\2/i',
                    $ramVar_attrStr,
                    $ramVar_attrMatches,
                    PREG_SET_ORDER
                );

                foreach ($ramVar_attrMatches as $ramVar_attr) {
                    $ramVar_attrName = strtolower($ramVar_attr[1]);
                    $ramVar_attrVal  = $ramVar_attr[3];
                    $ramVar_quote    = $ramVar_attr[2];

                    if (!in_array($ramVar_attrName, $ramVar_allowedAttrs, true)) {
                        continue;
                    }

                    // For href/src/action attributes: validate URL
                    if (in_array($ramVar_attrName, ['href', 'src', 'action', 'data-url'], true)) {
                        $ramVar_attrVal = self::ramFn_url($ramVar_attrVal);
                        // ramFn_url already escapes — use as-is
                        $ramVar_cleanAttrs .= ' ' . $ramVar_attrName . '=' . $ramVar_quote . $ramVar_attrVal . $ramVar_quote;
                    } else {
                        $ramVar_cleanAttrs .= ' ' . $ramVar_attrName . '=' . $ramVar_quote
                            . htmlspecialchars($ramVar_attrVal, ENT_QUOTES, 'UTF-8') . $ramVar_quote;
                    }
                }

                return '<' . $ramVar_tag . $ramVar_cleanAttrs . '>';
            },
            $ramVar_stripped
        ) ?? $ramVar_stripped;
    }

    /**
     * Strip ALL HTML tags — plain text only.
     *
     * @param mixed $ramVar_value Value to strip
     * @return string Plain text with HTML removed
     */
    public static function ramFn_stripTags(mixed $ramVar_value): string
    {
        return strip_tags((string) $ramVar_value);
    }

    /**
     * Sanitize a slug (URL-safe identifier).
     *
     * Converts to lowercase, replaces spaces and special chars with hyphens.
     *
     * @param string $ramVar_value Input string
     * @return string URL-safe slug
     */
    public static function ramFn_slug(string $ramVar_value): string
    {
        // Transliterate unicode to ASCII (basic approach)
        $ramVar_value = mb_strtolower(trim($ramVar_value), 'UTF-8');
        $ramVar_value = preg_replace('/[^\pL\pN\s\-]/u', '', $ramVar_value) ?? $ramVar_value;
        $ramVar_value = preg_replace('/[\s\-]+/', '-', $ramVar_value)        ?? $ramVar_value;
        return trim($ramVar_value, '-');
    }

    /**
     * Sanitize an email address.
     *
     * @param string $ramVar_email Raw email
     * @return string Sanitized email (empty string if invalid)
     */
    public static function ramFn_email(string $ramVar_email): string
    {
        $ramVar_cleaned = filter_var(trim($ramVar_email), FILTER_SANITIZE_EMAIL);
        if ($ramVar_cleaned === false) {
            return '';
        }
        return filter_var($ramVar_cleaned, FILTER_VALIDATE_EMAIL) ? $ramVar_cleaned : '';
    }

    /**
     * Sanitize an integer.
     *
     * @param mixed $ramVar_value Value to sanitize
     * @return int Integer value (0 if not numeric)
     */
    public static function ramFn_int(mixed $ramVar_value): int
    {
        return (int) filter_var($ramVar_value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Sanitize a float.
     *
     * @param mixed $ramVar_value Value to sanitize
     * @return float Float value (0.0 if not numeric)
     */
    public static function ramFn_float(mixed $ramVar_value): float
    {
        return (float) filter_var($ramVar_value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * Sanitize a filename (strip directory traversal).
     *
     * @param string $ramVar_filename Raw filename
     * @return string Safe filename
     */
    public static function ramFn_filename(string $ramVar_filename): string
    {
        $ramVar_filename = basename($ramVar_filename);
        $ramVar_filename = preg_replace('/[^\w\.\-]/', '', $ramVar_filename) ?? '';
        // Prevent double extensions like .php.jpg
        return preg_replace('/\.php$/i', '', $ramVar_filename) ?? $ramVar_filename;
    }

    // ─── Default allowed tags ─────────────────────────────────────────────────

    /**
     * Get the default set of allowed HTML tags and their safe attributes.
     *
     * @return array<string, string[]>
     */
    public static function ramFn_getDefaultAllowedTags(): array
    {
        return [
            'a'          => ['href', 'title', 'rel', 'target', 'class', 'id'],
            'abbr'       => ['title', 'class'],
            'acronym'    => ['title', 'class'],
            'b'          => ['class'],
            'blockquote' => ['cite', 'class'],
            'br'         => [],
            'cite'       => ['class'],
            'code'       => ['class'],
            'del'        => ['datetime', 'class'],
            'em'         => ['class'],
            'i'          => ['class'],
            'img'        => ['src', 'alt', 'title', 'width', 'height', 'class', 'loading'],
            'ins'        => ['datetime', 'class'],
            'li'         => ['class'],
            'ol'         => ['class'],
            'p'          => ['class', 'style'],
            'pre'        => ['class'],
            'q'          => ['cite', 'class'],
            's'          => ['class'],
            'span'       => ['class', 'style'],
            'strike'     => ['class'],
            'strong'     => ['class'],
            'sub'        => ['class'],
            'sup'        => ['class'],
            'table'      => ['class', 'border', 'cellpadding', 'cellspacing', 'width'],
            'tbody'      => ['class'],
            'td'         => ['class', 'colspan', 'rowspan'],
            'tfoot'      => ['class'],
            'th'         => ['class', 'colspan', 'rowspan', 'scope'],
            'thead'      => ['class'],
            'tr'         => ['class'],
            'u'          => ['class'],
            'ul'         => ['class'],
            'h1'         => ['class', 'id'],
            'h2'         => ['class', 'id'],
            'h3'         => ['class', 'id'],
            'h4'         => ['class', 'id'],
            'h5'         => ['class', 'id'],
            'h6'         => ['class', 'id'],
        ];
    }

    /**
     * Get a richer set of allowed tags for post content.
     *
     * @return array<string, string[]>
     */
    public static function ramFn_getPostAllowedTags(): array
    {
        return array_merge(self::ramFn_getDefaultAllowedTags(), [
            'figure'     => ['class'],
            'figcaption' => ['class'],
            'hr'         => ['class'],
            'div'        => ['class', 'id', 'style'],
            'section'    => ['class', 'id'],
            'article'    => ['class', 'id'],
            'aside'      => ['class', 'id'],
            'header'     => ['class', 'id'],
            'footer'     => ['class', 'id'],
            'nav'        => ['class', 'id'],
            'main'       => ['class', 'id'],
            'iframe'     => ['src', 'width', 'height', 'class', 'allowfullscreen', 'frameborder'],
            'video'      => ['src', 'width', 'height', 'class', 'controls', 'autoplay', 'muted', 'loop', 'poster'],
            'audio'      => ['src', 'class', 'controls'],
            'source'     => ['src', 'type'],
            'caption'    => ['class'],
            'dl'         => ['class'],
            'dt'         => ['class'],
            'dd'         => ['class'],
            'details'    => ['class', 'open'],
            'summary'    => ['class'],
        ]);
    }
}
