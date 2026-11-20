<?php
/**
 * Ramayan Aurora — Archive Template
 */

require_once __DIR__ . '/parts/header.php';

$ramVar_siteUrl = rtrim((string) (defined('RAM_URL') ? RAM_URL : '/'), '/');
$ramVar_posts   = ram_get_posts(['limit' => 30, 'status' => 'publish']);
?>

<section class="ra-page-hero">
    <div class="ra-container">
        <span class="ra-eyebrow" data-ra-reveal><span class="ra-dot"></span> Archive</span>
        <h1 class="ra-page-title" data-ra-reveal style="--d:.05s">All <span class="ra-grad-text">stories.</span></h1>
        <p class="ra-page-sub" data-ra-reveal style="--d:.1s">Browse every published story, note and update.</p>
    </div>
</section>

<section class="ra-section ra-section-tight">
    <div class="ra-container">
        <?php if (!empty($ramVar_posts)): ?>
            <div class="ra-posts">
                <?php foreach ($ramVar_posts as $ramVar_index => $ramVar_post): ?>
                    <?php $ramVar_featured = ra_img($ramVar_post['featured_image'] ?? ''); ?>
                    <article class="ra-post" data-ra-reveal style="--d:<?= number_format(min($ramVar_index, 8) * 0.05, 2) ?>s">
                        <a class="ra-post-media" href="<?= ram_esc_url(ram_get_permalink($ramVar_post)) ?>" tabindex="-1" aria-hidden="true">
                            <?php if ($ramVar_featured !== ''): ?>
                                <img src="<?= ram_esc_url($ramVar_featured) ?>" alt="<?= ram_esc_attr((string) $ramVar_post['title']) ?>" loading="lazy">
                            <?php else: ?>
                                <span class="ra-post-placeholder"><?= ram_esc_html(ra_initial((string) $ramVar_post['title'])) ?></span>
                            <?php endif; ?>
                            <span class="ra-post-chip">Journal</span>
                        </a>
                        <div class="ra-post-body">
                            <div class="ra-post-meta">
                                <span><?= ram_esc_html(ra_date($ramVar_post)) ?></span>
                                <span class="ra-meta-dot">•</span>
                                <span><?= (int) ra_read_time($ramVar_post) ?> min read</span>
                            </div>
                            <h3 class="ra-post-title"><a href="<?= ram_esc_url(ram_get_permalink($ramVar_post)) ?>"><?= ram_esc_html((string) $ramVar_post['title']) ?></a></h3>
                            <p class="ra-post-excerpt"><?= ram_esc_html(ra_excerpt($ramVar_post, 130)) ?></p>
                            <a class="ra-post-link" href="<?= ram_esc_url(ram_get_permalink($ramVar_post)) ?>">Read story <span>→</span></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="ra-empty" data-ra-reveal>
                <div class="ra-empty-icon"><?= ra_icon('book', 28) ?></div>
                <h3>Nothing here yet</h3>
                <p>Stories will appear here as they are published.</p>
                <a class="ra-btn ra-btn-primary" href="<?= ram_esc_url($ramVar_siteUrl . '/') ?>">Back home</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/parts/footer.php'; ?>
