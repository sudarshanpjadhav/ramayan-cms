<?php
/**
 * Ramayan CMS — SEO System (Saraswati)
 * finggu.com | Author: sudarshanpjadhav
 */

declare(strict_types=1);

namespace Ram\Seo;

use Ram\Hooks\AkashvaniHooks;

/**
 * SaraswatiSEO — The Goddess of Knowledge and Expression.
 * 
 * Handles meta tags, titles, and social graph data.
 */
class SaraswatiSEO
{
    public static function ramFn_init(): void
    {
        AkashvaniHooks::ramFn_addAction('ram_head', [self::class, 'ramFn_renderMetaTags'], 1);
    }

    public static function ramFn_renderMetaTags(): void
    {
        $ramVar_title = self::ramFn_getTitle();
        $ramVar_desc  = self::ramFn_getDescription();

        echo "\n<!-- SEO by Saraswati (Ramayan CMS) -->\n";
        echo '<title>' . ram_esc_html($ramVar_title) . "</title>\n";
        echo '<meta name="description" content="' . ram_esc_attr($ramVar_desc) . "\">\n";
        
        // Open Graph
        echo '<meta property="og:title" content="' . ram_esc_attr($ramVar_title) . "\">\n";
        echo '<meta property="og:description" content="' . ram_esc_attr($ramVar_desc) . "\">\n";
        echo '<meta property="og:type" content="website">' . "\n";
        echo "<!-- / SEO by Saraswati -->\n";
    }

    public static function ramFn_getTitle(): string
    {
        $ramVar_siteName = ram_get_option('site_name', 'Ramayan CMS');
        return $ramVar_siteName; // Simplified for now
    }

    public static function ramFn_getDescription(): string
    {
        return ram_get_option('site_description', 'A powerful PHP CMS ecosystem.');
    }
}
