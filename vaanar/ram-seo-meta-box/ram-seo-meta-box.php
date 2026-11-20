<?php
/**
 * Plugin Name: Ramayan SEO Meta Box
 * Slug: ram-seo-meta-box
 * Version: 1.0.0
 * Author: Finggu
 * Description: Adds SEO optimization controls to the post editor.
 */

declare(strict_types=1);

if (!defined('RAM_PATH')) exit;

ram_action('ram_admin_init', function() {
    ram_add_meta_box('ram_seo_options', 'Saraswati SEO Settings', 'ramFn_render_seo_meta_box');
});

function ramFn_render_seo_meta_box($post): void
{
    $postId = $post['id'] ?? 0;
    $seoTitle = ram_get_post_meta($postId, '_ram_seo_title');
    $seoDesc  = ram_get_post_meta($postId, '_ram_seo_desc');
    ?>
    <div class="mb-2">
        <label class="ram-label">SEO Title</label>
        <input type="text" name="ram_seo_title" value="<?= ram_esc_attr($seoTitle) ?>" class="ram-input" placeholder="Custom meta title...">
    </div>
    <div class="mb-2">
        <label class="ram-label">Meta Description</label>
        <textarea name="ram_seo_desc" class="ram-textarea" rows="3" placeholder="Custom meta description..."><?= ram_esc_html($seoDesc) ?></textarea>
    </div>
    <div style="font-size: 0.75rem; color: var(--ram-muted); padding-top: 0.5rem;">
        🏹 Powered by Saraswati SEO Engine.
    </div>
    <?php
}

// Handle saving
ram_action('ram_save_post', function($postId) {
    if (isset($_POST['ram_seo_title'])) {
        ram_update_post_meta($postId, '_ram_seo_title', $_POST['ram_seo_title']);
    }
    if (isset($_POST['ram_seo_desc'])) {
        ram_update_post_meta($postId, '_ram_seo_desc', $_POST['ram_seo_desc']);
    }
});
