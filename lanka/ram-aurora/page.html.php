<?php
/**
 * Ramayan Aurora — Static Page Template
 */

require_once __DIR__ . '/parts/helpers.php';

$ramVar_post = ra_current_post();

require_once __DIR__ . '/parts/header.php';

$ramVar_siteUrl = rtrim((string) (defined('RAM_URL') ? RAM_URL : '/'), '/');

if ($ramVar_post === null):
?>
    <section class="ra-section">
        <div class="ra-container">
            <div class="ra-empty">
                <div class="ra-empty-icon"><?= ra_icon('book', 28) ?></div>
                <h3>Page not found</h3>
                <p>This page may have been moved or unpublished.</p>
                <a class="ra-btn ra-btn-primary" href="<?= ram_esc_url($ramVar_siteUrl . '/') ?>">Back home</a>
            </div>
        </div>
    </section>
<?php
    require __DIR__ . '/parts/footer.php';
    return;
endif;

$ramVar_featured = ra_img($ramVar_post['featured_image'] ?? '');
?>

<article class="ra-article ra-page">
    <div class="ra-container ra-article-inner">
        <header class="ra-article-head">
            <span class="ra-chip" data-ra-reveal>Page</span>
            <h1 class="ra-article-title" data-ra-reveal style="--d:.05s"><?= ram_esc_html((string) $ramVar_post['title']) ?></h1>
            <div class="ra-article-meta" data-ra-reveal style="--d:.1s">
                <span><strong>Updated</strong><small><?= ram_esc_html(ra_date($ramVar_post)) ?></small></span>
            </div>
        </header>

        <?php if ($ramVar_featured !== ''): ?>
            <figure class="ra-article-hero" data-ra-reveal style="--d:.12s">
                <img src="<?= ram_esc_url($ramVar_featured) ?>" alt="<?= ram_esc_attr((string) $ramVar_post['title']) ?>">
            </figure>
        <?php endif; ?>

        <div class="ra-prose" data-ra-reveal style="--d:.15s">
            <?= ram_kses((string) $ramVar_post['content']) ?>
        </div>
    </div>
</article>

<?php require __DIR__ . '/parts/footer.php'; ?>
