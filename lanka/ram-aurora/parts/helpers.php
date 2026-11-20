<?php
/**
 * Ramayan Aurora — Theme Helpers
 * finggu.com | Author: sudarshanpjadhav
 */

declare(strict_types=1);

if (!function_exists('ra_theme_url')) {
    /**
     * Absolute URL to a file inside this theme.
     * Computed from the theme folder name so it stays correct regardless of
     * the (buggy) active-theme constant.
     */
    function ra_theme_url(string $ramVar_path = ''): string
    {
        $ramVar_slug = basename(dirname(__DIR__)); // parts/ -> theme root
        $ramVar_base = defined('RAM_THEME_URL')
            ? RAM_THEME_URL
            : ((defined('RAM_URL') ? RAM_URL : '') . '/lanka');

        return rtrim($ramVar_base, '/') . '/' . $ramVar_slug
            . ($ramVar_path !== '' ? '/' . ltrim($ramVar_path, '/') : '');
    }
}

if (!function_exists('ra_date')) {
    function ra_date(array $ramVar_post): string
    {
        $ramVar_ts = (string) ($ramVar_post['published_at'] ?? '');
        if ($ramVar_ts === '' || str_starts_with($ramVar_ts, '0000')) {
            $ramVar_ts = (string) ($ramVar_post['created_at'] ?? '');
        }
        $ramVar_time = $ramVar_ts !== '' ? strtotime($ramVar_ts) : false;

        return date('M j, Y', $ramVar_time ?: time());
    }
}

if (!function_exists('ra_excerpt')) {
    function ra_excerpt(array $ramVar_post, int $ramVar_len = 150): string
    {
        $ramVar_text = trim((string) ($ramVar_post['excerpt'] ?? ''));
        if ($ramVar_text === '') {
            $ramVar_text = strip_tags((string) ($ramVar_post['content'] ?? ''));
        }
        $ramVar_text = preg_replace('/\s+/', ' ', $ramVar_text) ?? $ramVar_text;
        $ramVar_text = trim($ramVar_text);

        if (mb_strlen($ramVar_text) > $ramVar_len) {
            $ramVar_text = mb_substr($ramVar_text, 0, $ramVar_len) . '…';
        }

        return $ramVar_text;
    }
}

if (!function_exists('ra_read_time')) {
    function ra_read_time(array $ramVar_post): int
    {
        $ramVar_words = str_word_count(strip_tags((string) ($ramVar_post['content'] ?? '')));
        return max(1, (int) ceil($ramVar_words / 200));
    }
}

if (!function_exists('ra_img')) {
    function ra_img(?string $ramVar_url): string
    {
        $ramVar_url = trim((string) $ramVar_url);
        if ($ramVar_url === '') {
            return '';
        }

        return function_exists('ram_media_url') ? ram_media_url($ramVar_url) : $ramVar_url;
    }
}

if (!function_exists('ra_initial')) {
    function ra_initial(string $ramVar_name): string
    {
        $ramVar_name = trim($ramVar_name);
        return $ramVar_name !== '' ? mb_strtoupper(mb_substr($ramVar_name, 0, 1)) : 'R';
    }
}

if (!function_exists('ra_icon')) {
    /**
     * Inline SVG icon (Phosphor-style). Decorative by default.
     */
    function ra_icon(string $ramVar_name, int $ramVar_size = 20, string $ramVar_class = ''): string
    {
        $ramVar_paths = [
            'sparkle' => '<path d="M12 2.5c.7 5 4.5 8.8 9.5 9.5-5 .7-8.8 4.5-9.5 9.5-.7-5-4.5-8.8-9.5-9.5 5-.7 8.8-4.5 9.5-9.5Z"/>',
            'bolt'    => '<path d="M13 2 4.8 13.4h6.1L10 22l8.2-11.4h-6.1z"/>',
            'search'  => '<circle cx="11" cy="11" r="6.5"/><path d="m16 16 4.5 4.5"/>',
            'shield'  => '<path d="M12 3 5.2 5.9v5.6c0 4.3 2.9 7.3 6.8 8.7 3.9-1.4 6.8-4.4 6.8-8.7V5.9z"/><path d="m9 11.8 2 2 4-4.2"/>',
            'layers'  => '<path d="m12 3 8.5 4.6L12 12.2 3.5 7.6z"/><path d="m3.5 12.4 8.5 4.6 8.5-4.6"/><path d="m3.5 16.9 8.5 4.6 8.5-4.6"/>',
            'book'    => '<path d="M12 6.5C10.4 5 8.3 4.3 4.5 4.3v14.2c3.8 0 5.9.7 7.5 2.2 1.6-1.5 3.7-2.2 7.5-2.2V4.3c-3.8 0-5.9.7-7.5 2.2Z"/><path d="M12 6.5v14.2"/>',
            'check'   => '<path d="m5 12.5 4.5 4.5L19 6.5"/>',
            'arrow'   => '<path d="M4.5 12h14"/><path d="m13 6.5 5.5 5.5L13 17.5"/>',
            'up'      => '<path d="M12 19.5v-14"/><path d="m6.5 11 5.5-5.5L17.5 11"/>',
            'play'    => '<path d="M8 5.5v13l10-6.5z"/>',
            'quote'   => '<path d="M9.5 6.5C6.9 7.7 5.5 9.9 5.5 13v4.5h5V12h-3c0-1.9.8-3.2 2.4-3.9z"/><path d="M18.5 6.5C15.9 7.7 14.5 9.9 14.5 13v4.5h5V12h-3c0-1.9.8-3.2 2.4-3.9z"/>',
            'star'    => '<path d="m12 3.2 2.6 5.5 6 .8-4.4 4.2 1.1 6-5.3-2.9-5.3 2.9 1.1-6L3.4 9.5l6-.8z"/>',
            'clock'   => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 1.8"/>',
            'pen'     => '<path d="m4 20 1-4.5L16.5 4a2.1 2.1 0 0 1 3 3L8 18.5z"/><path d="m14.5 6 3.5 3.5"/>',
            'mail'    => '<rect x="3.5" y="5.5" width="17" height="13" rx="2.5"/><path d="m4.5 7.5 7.5 5.5 7.5-5.5"/>',
            'bow'     => '<path d="M4 20 20 4"/><path d="M13.5 4H20v6.5"/><path d="M4 20c0-8.5 4.5-13 13-13"/>',
            'chevron' => '<path d="m6.5 9.5 5.5 5.5 5.5-5.5"/>',
            'orbit'   => '<circle cx="12" cy="12" r="3.2"/><ellipse cx="12" cy="12" rx="9" ry="4.2"/><ellipse cx="12" cy="12" rx="9" ry="4.2" transform="rotate(60 12 12)"/>',
        ];

        $ramVar_path = $ramVar_paths[$ramVar_name] ?? $ramVar_paths['sparkle'];
        $ramVar_solid = in_array($ramVar_name, ['star', 'sparkle', 'bolt', 'play', 'quote'], true);

        $ramVar_attrs = $ramVar_solid
            ? 'fill="currentColor" stroke="none"'
            : 'fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"';

        return '<svg class="ra-icon ' . ram_esc_attr($ramVar_class) . '" width="' . $ramVar_size
            . '" height="' . $ramVar_size . '" viewBox="0 0 24 24" aria-hidden="true" focusable="false" '
            . $ramVar_attrs . '>' . $ramVar_path . '</svg>';
    }
}

if (!function_exists('ra_current_post')) {
    function ra_current_post(): ?array
    {
        if (!class_exists('Ram\\Core\\Ram')) {
            return null;
        }

        try {
            $ramVar_post = \Ram\Core\Ram::ramFn_getInstance()->ramFn_get('current_post');
            return is_array($ramVar_post) ? $ramVar_post : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
