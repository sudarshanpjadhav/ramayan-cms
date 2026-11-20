<?php
/**
 * Ramayan Aurora — Search Results Template
 */

require_once __DIR__ . '/parts/header.php';

$ramVar_siteUrl = rtrim((string) (defined('RAM_URL') ? RAM_URL : '/'), '/');
$ramVar_query   = trim((string) ($_GET['s'] ?? $_GET['q'] ?? ''));

$ramVar_posts = ram_get_posts(['limit' => 50, 'status' => 'publish']);

if ($ramVar_query !== '') {
    $ramVar_needle = mb_strtolower($ramVar_query);
    $ramVar_posts  = array_values(array_filter($ramVar_posts, static function (array $ramVar_p) use ($ramVar_needle): bool {
        $ramVar_haystack = mb_strtolower(
            (string) ($ramVar_p['title'] ?? '') . ' ' .
            (string) ($ramVar_p['excerpt'] ?? '') . ' ' .
            strip_tags((string) ($ramVar_p['content'] ?? ''))
        );
        return str_contains($ramVar_haystack, $ramVar_needle);
    }));
}
?>

<section class="ra-page-hero">
    <div class="ra-container">
        <span class="ra-eyebrow" data-ra-reveal><span class="ra-dot"></span> Search</span>
        <h1 class="ra-page-title" data-ra-reveal style="--d:.05s">
            <?php if ($ramVar_query !== ''): ?>
                Results for <span class="ra-grad-text">“<?= ram_esc_html($ramVar_query) ?>”</span>
            <?php else: ?>
                Search the <span class="ra-grad-text">journal.</span>
            <?php endif; ?>
        </h1>
        <p class="ra-page-sub" data-ra-reveal style="--d:.1s"><?= count($ramVar_posts) ?> result<?= count($ramVar_posts) === 1 ? '' : 's' ?> found.</p>

        <form class="ra-search" method="get" action="<?= ram_esc_url($ramVar_siteUrl . '/') ?>" data-ra-reveal style="--d:.15s">
            <input type="search" name="s" value="<?= ram_esc_attr($ramVar_query) ?>" placeholder="Search stories…" aria-label="Search">
            <button class="ra-btn ra-btn-primary" type="submit">Search</button>
        </form>
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
                        </a>
                        <div class="ra-post-body">
                            <div class="ra-post-meta"><span><?= ram_esc_html(ra_date($ramVar_post)) ?></span></div>
                            <h3 class="ra-post-title"><a href="<?= ram_esc_url(ram_get_permalink($ramVar_post)) ?>"><?= ram_esc_html((string) $ramVar_post['title']) ?></a></h3>
                            <p class="ra-post-excerpt"><?= ram_esc_html(ra_excerpt($ramVar_post, 130)) ?></p>
                            <a class="ra-post-link" href="<?= ram_esc_url(ram_get_permalink($ramVar_post)) ?>">Read story <span>→</span></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="ra-empty" data-ra-reveal>
                <div class="ra-empty-icon"><?= ra_icon('search', 28) ?></div>
                <h3>No matches found</h3>
                <p>Try a different keyword or browse the full journal.</p>
                <a class="ra-btn ra-btn-primary" href="<?= ram_esc_url($ramVar_siteUrl . '/#blog') ?>">Browse journal</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/parts/footer.php'; ?>
