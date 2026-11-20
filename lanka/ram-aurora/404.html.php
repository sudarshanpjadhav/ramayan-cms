<?php
/**
 * Ramayan Aurora — 404 Template
 */

require_once __DIR__ . '/parts/header.php';

$ramVar_siteUrl = rtrim((string) (defined('RAM_URL') ? RAM_URL : '/'), '/');
?>

<section class="ra-404">
    <div class="ra-container ra-404-inner">
        <div class="ra-404-code" aria-hidden="true">404</div>
        <span class="ra-eyebrow" data-ra-reveal><span class="ra-dot"></span> Lost in the woods</span>
        <h1 class="ra-404-title" data-ra-reveal style="--d:.05s">This page took a <span class="ra-grad-text">detour.</span></h1>
        <p class="ra-404-sub" data-ra-reveal style="--d:.1s">The page you are looking for does not exist, or has been moved somewhere new.</p>
        <div class="ra-404-actions" data-ra-reveal style="--d:.15s">
            <a class="ra-btn ra-btn-primary ra-btn-lg" href="<?= ram_esc_url($ramVar_siteUrl . '/') ?>"><span>Back home</span><span class="ra-btn-arrow">→</span></a>
            <a class="ra-btn ra-btn-ghost ra-btn-lg" href="<?= ram_esc_url($ramVar_siteUrl . '/#blog') ?>">Browse journal</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/parts/footer.php'; ?>
