<?php
/**
 * Ramayan CMS — Meta Box System (Ayodhya)
 * finggu.com | Author: sudarshanpjadhav
 */

declare(strict_types=1);

namespace Ram\Ayodhya;

use Ram\Hooks\AkashvaniHooks;

/**
 * MetaBoxes — Handles registration and rendering of custom editor panels.
 */
class MetaBoxes
{
    private static array $ramVar_metaBoxes = [];

    public static function ramFn_add(string $ramVar_id, string $ramVar_title, callable $ramVar_callback, string $ramVar_context = 'sidebar'): void
    {
        self::$ramVar_metaBoxes[] = [
            'id'       => $ramVar_id,
            'title'    => $ramVar_title,
            'callback' => $ramVar_callback,
            'context'  => $ramVar_context
        ];
    }

    public static function ramFn_render(string $ramVar_context, mixed $ramVar_post): void
    {
        foreach (self::$ramVar_metaBoxes as $ramVar_box) {
            if ($ramVar_box['context'] === $ramVar_context) {
                ?>
                <div class="ram-card mb-3 ram-meta-box" id="ram-mb-<?= $ramVar_box['id'] ?>">
                    <h3 style="font-size: 0.9rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--ram-border); padding-bottom: 0.75rem; font-family: 'Space Grotesk';">
                        <?= ram_esc_html($ramVar_box['title']) ?>
                    </h3>
                    <div class="ram-mb-content">
                        <?php call_user_func($ramVar_box['callback'], $ramVar_post); ?>
                    </div>
                </div>
                <?php
            }
        }
    }
}
