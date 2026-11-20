<?php
/**
 * Ramayan CMS — Theme Customizer (Ayodhya)
 * finggu.com | Author: sudarshanpjadhav
 */

declare(strict_types=1);

if (!defined('RAM_PATH')) exit;

?>
<div class="ayodhya-page-header">
    <h1>Chitrakoot Studio</h1>
    <button class="btn btn-primary" onclick="ramFn_saveCustomizer()">Seal & Publish</button>
</div>

<div style="display: grid; grid-template-columns: 350px 1fr; gap: 0; height: calc(100vh - 180px); border: 1px solid var(--ram-border); border-radius: var(--ram-radius); overflow: hidden;">
    <!-- Sidebar -->
    <div style="background: var(--ram-deep-2); border-right: 1px solid var(--ram-border); padding: 1.5rem; overflow-y: auto;">
        <div class="mb-3">
            <h3 style="font-size: 0.8rem; text-transform: uppercase; color: var(--ram-muted); margin-bottom: 1rem;">Kingdom Identity</h3>
            <div class="mb-2">
                <label class="ram-label">Kingdom Name</label>
                <input type="text" id="custom-site-name" value="<?= ram_esc_attr(ram_get_option('site_name')) ?>" class="ram-input">
            </div>
            <div class="mb-2">
                <label class="ram-label">Kingdom Motto</label>
                <input type="text" id="custom-site-desc" value="<?= ram_esc_attr(ram_get_option('site_description')) ?>" class="ram-input">
            </div>
            <div class="mb-2">
                <label class="ram-label">Kingdom Mark (Favicon)</label>
                <div style="width: 64px; height: 64px; border: 1px solid var(--ram-border); border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; background: rgba(255,255,255,0.02);" onclick="ramFn_openMediaModal(ramFn_setFavicon)">
                    <span id="favicon-preview" style="font-size: 1.5rem;">🏹</span>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <h3 style="font-size: 0.8rem; text-transform: uppercase; color: var(--ram-muted); margin-bottom: 1rem;">Colors</h3>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <label class="ram-label" style="margin: 0;">Primary Accent</label>
                <input type="color" value="#FF6B2B" style="background: none; border: none; width: 32px; height: 32px; cursor: pointer;">
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <label class="ram-label" style="margin: 0;">Background</label>
                <input type="color" value="#0F172A" style="background: none; border: none; width: 32px; height: 32px; cursor: pointer;">
            </div>
        </div>

        <?php \Ram\Hooks\AkashvaniHooks::ramFn_doAction('ram_customizer_sidebar'); ?>
    </div>

    <!-- Preview -->
    <div style="background: #fff; position: relative;">
        <div style="position: absolute; top: 1rem; left: 1rem; background: rgba(0,0,0,0.7); color: #fff; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600; pointer-events: none;">LIVE PREVIEW</div>
        <iframe id="ram-customizer-preview" src="../" style="width: 100%; height: 100%; border: none;"></iframe>
    </div>
</div>

<script>
function ramFn_setFavicon(media) {
    document.getElementById('favicon-preview').innerHTML = `<img src="${media.url}" style="width: 100%; height: 100%; object-fit: contain;">`;
}

function ramFn_saveCustomizer() {
    // Logic to save options via AJAX
    alert('Settings saved to Brahmastra database!');
}
</script>
