<?php
/**
 * Ramayan Aurora — Single Post Template
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
                <div class="ra-empty-icon"><?= ra_icon('search', 28) ?></div>
                <h3>Story not found</h3>
                <p>This post may have been moved or unpublished.</p>
                <a class="ra-btn ra-btn-primary" href="<?= ram_esc_url($ramVar_siteUrl . '/') ?>">Back home</a>
            </div>
        </div>
    </section>
<?php
    require __DIR__ . '/parts/footer.php';
    return;
endif;

$ramVar_featured = ra_img($ramVar_post['featured_image'] ?? '');
$ramVar_related  = array_values(array_filter(
    ram_get_posts(['limit' => 4, 'status' => 'publish']),
    static fn(array $ramVar_p): bool => (int) ($ramVar_p['id'] ?? 0) !== (int) ($ramVar_post['id'] ?? 0)
));
$ramVar_related = array_slice($ramVar_related, 0, 3);
?>

<article class="ra-article">
    <div class="ra-container ra-article-inner">
        <header class="ra-article-head">
            <a class="ra-chip" href="<?= ram_esc_url($ramVar_siteUrl . '/#blog') ?>" data-ra-reveal>Journal</a>

            <h1 class="ra-article-title" data-ra-reveal style="--d:.05s"><?= ram_esc_html((string) $ramVar_post['title']) ?></h1>

            <div class="ra-article-meta" data-ra-reveal style="--d:.1s">
                <span class="ra-avatar"><?= ram_esc_html(ra_initial((string) $ramVar_post['title'])) ?></span>
                <span><strong>Editorial</strong><small><?= ram_esc_html(ra_date($ramVar_post)) ?> · <?= (int) ra_read_time($ramVar_post) ?> min read</small></span>
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

        <footer class="ra-article-foot" data-ra-reveal>
            <div class="ra-article-tags">
                <span class="ra-pill">Ramayan</span>
                <span class="ra-pill">Aurora</span>
            </div>
            <div class="ra-article-share">
                <span>Share</span>
                <a href="#" aria-label="Share on X">𝕏</a>
                <a href="#" aria-label="Share on LinkedIn">in</a>
                <a href="#" aria-label="Copy link">⧉</a>
            </div>
        </footer>
    </div>
</article>

<?php if (!empty($ramVar_related)): ?>
<section class="ra-section ra-related">
    <div class="ra-container">
        <div class="ra-section-head ra-head-row">
            <div>
                <span class="ra-eyebrow" data-ra-reveal><span class="ra-dot"></span> Keep reading</span>
                <h2 data-ra-reveal style="--d:.05s">More <span class="ra-grad-text">stories.</span></h2>
            </div>
            <a class="ra-text-link" href="<?= ram_esc_url($ramVar_siteUrl . '/#blog') ?>" data-ra-reveal>View all →</a>
        </div>

        <div class="ra-posts">
            <?php foreach ($ramVar_related as $ramVar_index => $ramVar_rel): ?>
                <?php $ramVar_relImg = ra_img($ramVar_rel['featured_image'] ?? ''); ?>
                <article class="ra-post" data-ra-reveal style="--d:<?= number_format($ramVar_index * 0.06, 2) ?>s">
                    <a class="ra-post-media" href="<?= ram_esc_url(ram_get_permalink($ramVar_rel)) ?>" tabindex="-1" aria-hidden="true">
                        <?php if ($ramVar_relImg !== ''): ?>
                            <img src="<?= ram_esc_url($ramVar_relImg) ?>" alt="<?= ram_esc_attr((string) $ramVar_rel['title']) ?>" loading="lazy">
                        <?php else: ?>
                            <span class="ra-post-placeholder"><?= ram_esc_html(ra_initial((string) $ramVar_rel['title'])) ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="ra-post-body">
                        <div class="ra-post-meta"><span><?= ram_esc_html(ra_date($ramVar_rel)) ?></span></div>
                        <h3 class="ra-post-title"><a href="<?= ram_esc_url(ram_get_permalink($ramVar_rel)) ?>"><?= ram_esc_html((string) $ramVar_rel['title']) ?></a></h3>
                        <p class="ra-post-excerpt"><?= ram_esc_html(ra_excerpt($ramVar_rel, 110)) ?></p>
                        <a class="ra-post-link" href="<?= ram_esc_url(ram_get_permalink($ramVar_rel)) ?>">Read story <span>→</span></a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/parts/footer.php'; ?>
