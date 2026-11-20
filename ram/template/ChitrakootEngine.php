<?php
/**
 * Ramayan CMS — Template Engine (Chitrakoot)
 * finggu.com | Author: sudarshanpjadhav
 */

declare(strict_types=1);

namespace Ram\Template;

use Ram\Core\Ram;
use Ram\Hooks\AkashvaniHooks;

/**
 * ChitrakootEngine — Where Ram and Sita rested.
 * 
 * Responsible for rendering theme templates and parts.
 */
class ChitrakootEngine
{
    /**
     * Render the main template for the current page.
     */
    public static function ramFn_render(string $ramVar_template): void
    {
        $ramVar_ram = Ram::ramFn_getInstance();
        $ramVar_themePath = $ramVar_ram->ramFn_getThemePath();
        
        $ramVar_file = $ramVar_themePath . '/' . $ramVar_template . '.html.php';
        if (!file_exists($ramVar_file)) {
            $ramVar_file = $ramVar_themePath . '/index.html.php';
        }

        AkashvaniHooks::ramFn_doAction('ram_before_render', $ramVar_template);
        
        if (file_exists($ramVar_file)) {
            require $ramVar_file;
        } else {
            echo "Theme template missing: " . ram_esc_html($ramVar_template);
        }

        AkashvaniHooks::ramFn_doAction('ram_after_render', $ramVar_template);
    }

    /**
     * Get a header part.
     */
    public static function ramFn_getHeader(?string $ramVar_name = null): void
    {
        AkashvaniHooks::ramFn_doAction('get_header', $ramVar_name);
        ram_get_template_part('header', $ramVar_name);
    }

    /**
     * Get a footer part.
     */
    public static function ramFn_getFooter(?string $ramVar_name = null): void
    {
        AkashvaniHooks::ramFn_doAction('get_footer', $ramVar_name);
        ram_get_template_part('footer', $ramVar_name);
    }
}
