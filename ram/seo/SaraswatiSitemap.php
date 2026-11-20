<?php
/**
 * Ramayan CMS — Sitemap Generator (Saraswati)
 * finggu.com | Author: sudarshanpjadhav
 */

declare(strict_types=1);

namespace Ram\Seo;

/**
 * SaraswatiSitemap — Generates XML sitemaps for search engines.
 */
class SaraswatiSitemap
{
    public static function ramFn_generate(): string
    {
        $ramVar_posts = ram_get_posts(['limit' => 1000]);
        
        $ramVar_xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $ramVar_xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($ramVar_posts as $ramVar_post) {
            $ramVar_xml .= "  <url>\n";
            $ramVar_xml .= "    <loc>" . ram_get_permalink($ramVar_post) . "</loc>\n";
            $ramVar_xml .= "    <lastmod>" . date('c', strtotime($ramVar_post['updated_at'])) . "</lastmod>\n";
            $ramVar_xml .= "    <changefreq>weekly</changefreq>\n";
            $ramVar_xml .= "  </url>\n";
        }

        $ramVar_xml .= '</urlset>';
        return $ramVar_xml;
    }
}
