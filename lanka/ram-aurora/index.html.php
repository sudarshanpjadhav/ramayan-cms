<?php
/**
 * Ramayan Aurora — Home Template
 * Premium editorial landing page.
 */

require_once __DIR__ . '/parts/header.php';

$ramVar_posts      = ram_get_posts(['limit' => 6, 'status' => 'publish']);
$ramVar_categories = ram_get_categories();
$ramVar_siteUrl    = rtrim((string) (defined('RAM_URL') ? RAM_URL : '/'), '/');
?>

<!-- ── HERO ─────────────────────────────────────────────────────── -->
<section class="ra-hero">
    <div class="ra-hero-glyph" aria-hidden="true"><?= ra_icon('orbit', 640) ?></div>

    <div class="ra-container ra-hero-grid">
        <div class="ra-hero-copy">
            <span class="ra-eyebrow" data-ra-reveal>
                <span class="ra-dot"></span> A new chapter in publishing
            </span>

            <h1 class="ra-hero-title" data-ra-reveal style="--d:.05s">
                Craft stories<br>
                worth <em class="ra-grad-text">remembering.</em>
            </h1>

            <p class="ra-hero-sub" data-ra-reveal style="--d:.12s">
                <?= ram_esc_html($siteDesc ?: 'An editorial platform with the speed of a modern app and the soul of a great publication.') ?>
            </p>

            <div class="ra-hero-actions" data-ra-reveal style="--d:.18s">
                <a class="ra-btn ra-btn-primary ra-btn-lg" href="<?= ram_esc_url(ram_admin_url('login.php')) ?>">
                    <span>Start publishing</span>
                    <?= ra_icon('arrow', 18, 'ra-btn-ico') ?>
                </a>
                <a class="ra-btn ra-btn-ghost ra-btn-lg" href="<?= ram_esc_url($ramVar_siteUrl . '/#showcase') ?>">
                    <?= ra_icon('play', 16, 'ra-btn-ico') ?>
                    <span>See how it works</span>
                </a>
            </div>

            <div class="ra-trust" data-ra-reveal style="--d:.24s">
                <div class="ra-avatars" aria-hidden="true">
                    <span>R</span><span>S</span><span>L</span><span>+</span>
                </div>
                <p>Trusted by <strong>12,000+</strong> writers, editors and teams.</p>
            </div>
        </div>

        <div class="ra-hero-visual" data-ra-reveal data-ra-tilt style="--d:.15s">
            <div class="ra-orbit ra-orbit-1" aria-hidden="true"></div>
            <div class="ra-orbit ra-orbit-2" aria-hidden="true"></div>

            <div class="ra-window">
                <div class="ra-window-bar">
                    <span class="ra-dot-red"></span>
                    <span class="ra-dot-amber"></span>
                    <span class="ra-dot-green"></span>
                    <div class="ra-window-url"><?= ram_esc_html(parse_url($ramVar_siteUrl, PHP_URL_HOST) ?: 'ramayan.local') ?></div>
                </div>
                <div class="ra-window-body">
                    <aside class="ra-window-side">
                        <span class="ra-side-logo"><?= ra_icon('bow', 18) ?></span>
                        <span class="ra-side-line"></span>
                        <span class="ra-side-line short"></span>
                        <span class="ra-side-line"></span>
                        <span class="ra-side-line short"></span>
                    </aside>
                    <div class="ra-window-main">
                        <div class="ra-skeleton ra-skeleton-title"></div>
                        <div class="ra-skeleton"></div>
                        <div class="ra-skeleton short"></div>
                        <div class="ra-chart">
                            <span style="--h:38%"></span><span style="--h:62%"></span>
                            <span style="--h:48%"></span><span style="--h:82%"></span>
                            <span style="--h:58%"></span><span style="--h:94%"></span>
                            <span style="--h:70%"></span>
                        </div>
                        <div class="ra-window-cards">
                            <div class="ra-mini-card"><span class="ra-mini-glow"></span></div>
                            <div class="ra-mini-card"><span class="ra-mini-glow"></span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ra-float-card ra-float-1">
                <span class="ra-float-icon"><?= ra_icon('bolt', 18) ?></span>
                <div><strong>0.12s</strong><small>Time to first byte</small></div>
            </div>
            <div class="ra-float-card ra-float-2">
                <span class="ra-float-icon"><?= ra_icon('sparkle', 18) ?></span>
                <div><strong>99.99%</strong><small>Uptime SLA</small></div>
            </div>
        </div>
    </div>

    <div class="ra-container">
        <a class="ra-scroll-cue" href="#features" aria-label="Scroll to features">
            <span class="ra-scroll-line"></span>
            <span class="ra-scroll-text">Scroll to explore</span>
        </a>
    </div>
</section>

<!-- ── LOGO MARQUEE ─────────────────────────────────────────────── -->
<section class="ra-marquee-wrap" aria-label="Trusted by">
    <div class="ra-marquee">
        <div class="ra-marquee-track">
            <?php for ($ramVar_i = 0; $ramVar_i < 2; $ramVar_i++): ?>
                <span>Ramayan</span><span class="ra-marquee-sep" aria-hidden="true"></span>
                <span>Ayodhya</span><span class="ra-marquee-sep" aria-hidden="true"></span>
                <span>Lanka</span><span class="ra-marquee-sep" aria-hidden="true"></span>
                <span>Vaanar</span><span class="ra-marquee-sep" aria-hidden="true"></span>
                <span>Nandan</span><span class="ra-marquee-sep" aria-hidden="true"></span>
                <span>Brahmaand</span><span class="ra-marquee-sep" aria-hidden="true"></span>
            <?php endfor; ?>
        </div>
    </div>
</section>

<!-- ── FEATURES / BENTO ─────────────────────────────────────────── -->
<section class="ra-section" id="features">
    <div class="ra-container">
        <div class="ra-section-head">
            <span class="ra-eyebrow" data-ra-reveal><span class="ra-dot"></span> 01 · Why Aurora</span>
            <h2 data-ra-reveal style="--d:.05s">Everything you need to <em class="ra-grad-text">shine.</em></h2>
            <p data-ra-reveal style="--d:.1s">A carefully engineered toolkit that feels effortless — from the first pixel to the final publish.</p>
        </div>

        <div class="ra-bento">
            <article class="ra-bento-card ra-spot ra-span-2" data-ra-reveal>
                <span class="ra-bento-num">01</span>
                <div class="ra-bento-icon"><?= ra_icon('sparkle', 22) ?></div>
                <h3>An aurora design language</h3>
                <p>Fluid gradients, optical glass and motion that feels alive — without ever getting in the way of the words.</p>
                <div class="ra-bento-visual ra-bento-glow"></div>
            </article>

            <article class="ra-bento-card ra-spot" data-ra-reveal style="--d:.05s">
                <span class="ra-bento-num">02</span>
                <div class="ra-bento-icon"><?= ra_icon('bolt', 22) ?></div>
                <h3>Blazing fast</h3>
                <p>Edge-ready output and lean markup keep every page under a blink.</p>
            </article>

            <article class="ra-bento-card ra-spot" data-ra-reveal style="--d:.1s">
                <span class="ra-bento-num">03</span>
                <div class="ra-bento-icon"><?= ra_icon('search', 22) ?></div>
                <h3>SEO built in</h3>
                <p>Structured data, sitemaps and clean semantics out of the box.</p>
            </article>

            <article class="ra-bento-card ra-spot" data-ra-reveal style="--d:.15s">
                <span class="ra-bento-num">04</span>
                <div class="ra-bento-icon"><?= ra_icon('shield', 22) ?></div>
                <h3>Secure by design</h3>
                <p>Hardened sessions, nonces and XSS protection at the core.</p>
            </article>

            <article class="ra-bento-card ra-spot ra-span-2" data-ra-reveal style="--d:.2s">
                <span class="ra-bento-num">05</span>
                <div class="ra-bento-icon"><?= ra_icon('layers', 22) ?></div>
                <h3>Composable &amp; extensible</h3>
                <p>Hooks, plugins and a clean API let you shape the platform around your ideas — not the other way around.</p>
                <div class="ra-bento-chips">
                    <span>Hooks</span><span>REST API</span><span>Plugins</span><span>Themes</span>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- ── STATS ────────────────────────────────────────────────────── -->
<section class="ra-stats-wrap">
    <div class="ra-container">
        <div class="ra-stats">
            <div class="ra-stat" data-ra-reveal>
                <span class="ra-num" data-ra-count="99.99" data-ra-decimals="2" data-ra-suffix="%">0%</span>
                <small>Uptime SLA</small>
            </div>
            <div class="ra-stat" data-ra-reveal style="--d:.05s">
                <span class="ra-num" data-ra-count="120" data-ra-suffix="ms">0ms</span>
                <small>Median response</small>
            </div>
            <div class="ra-stat" data-ra-reveal style="--d:.1s">
                <span class="ra-num" data-ra-count="12" data-ra-suffix="k+">0</span>
                <small>Happy creators</small>
            </div>
            <div class="ra-stat" data-ra-reveal style="--d:.15s">
                <span class="ra-num" data-ra-count="4.9" data-ra-decimals="1" data-ra-suffix="/5">0</span>
                <small>Average rating</small>
            </div>
        </div>
    </div>
</section>

<!-- ── WORKFLOW ─────────────────────────────────────────────────── -->
<section class="ra-section" id="showcase">
    <div class="ra-container ra-split">
        <div class="ra-split-media" data-ra-reveal>
            <div class="ra-glass-panel ra-spot">
                <div class="ra-panel-head">
                    <span class="ra-pill">Live preview</span>
                    <span class="ra-panel-dots"><i></i><i></i><i></i></span>
                </div>
                <div class="ra-panel-body">
                    <div class="ra-panel-hero">
                        <span class="ra-panel-tag">New</span>
                        <h4>Publish beautifully.</h4>
                        <p>Compose, preview and ship in a single flow.</p>
                    </div>
                    <div class="ra-panel-grid">
                        <span></span><span></span><span></span><span></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="ra-split-copy">
            <span class="ra-eyebrow" data-ra-reveal><span class="ra-dot"></span> 02 · The workflow</span>
            <h2 data-ra-reveal style="--d:.05s">From idea to live in <em class="ra-grad-text">minutes.</em></h2>
            <p data-ra-reveal style="--d:.1s">A focused editor, instant previews and one-click publishing. No friction, no clutter — just momentum.</p>

            <ul class="ra-checks">
                <li data-ra-reveal style="--d:.15s"><span class="ra-check-ico"><?= ra_icon('check', 14) ?></span> Distraction-free block editor</li>
                <li data-ra-reveal style="--d:.2s"><span class="ra-check-ico"><?= ra_icon('check', 14) ?></span> Real-time responsive preview</li>
                <li data-ra-reveal style="--d:.25s"><span class="ra-check-ico"><?= ra_icon('check', 14) ?></span> Media library with smart URLs</li>
                <li data-ra-reveal style="--d:.3s"><span class="ra-check-ico"><?= ra_icon('check', 14) ?></span> One-click theme switching</li>
            </ul>

            <a class="ra-btn ra-btn-primary" href="<?= ram_esc_url(ram_admin_url('login.php')) ?>" data-ra-reveal style="--d:.35s">
                <span>Try the editor</span><?= ra_icon('arrow', 17, 'ra-btn-ico') ?>
            </a>
        </div>
    </div>
</section>

<!-- ── JOURNAL ──────────────────────────────────────────────────── -->
<section class="ra-section" id="blog">
    <div class="ra-container">
        <div class="ra-section-head ra-head-row">
            <div>
                <span class="ra-eyebrow" data-ra-reveal><span class="ra-dot"></span> 03 · The journal</span>
                <h2 data-ra-reveal style="--d:.05s">Latest <em class="ra-grad-text">stories.</em></h2>
            </div>
            <a class="ra-text-link" href="<?= ram_esc_url($ramVar_siteUrl . '/#blog') ?>" data-ra-reveal>View all <?= ra_icon('arrow', 15) ?></a>
        </div>

        <?php if (!empty($ramVar_posts)): ?>
            <div class="ra-posts">
                <?php foreach ($ramVar_posts as $ramVar_index => $ramVar_post): ?>
                    <?php $ramVar_featured = ra_img($ramVar_post['featured_image'] ?? ''); ?>
                    <article class="ra-post" data-ra-reveal style="--d:<?= number_format($ramVar_index * 0.06, 2) ?>s">
                        <a class="ra-post-media" href="<?= ram_esc_url(ram_get_permalink($ramVar_post)) ?>" tabindex="-1" aria-hidden="true">
                            <?php if ($ramVar_featured !== ''): ?>
                                <img src="<?= ram_esc_url($ramVar_featured) ?>" alt="" loading="lazy">
                            <?php else: ?>
                                <span class="ra-post-placeholder"><?= ram_esc_html(ra_initial((string) $ramVar_post['title'])) ?></span>
                            <?php endif; ?>
                            <span class="ra-post-chip">Journal</span>
                        </a>
                        <div class="ra-post-body">
                            <div class="ra-post-meta">
                                <span><?= ram_esc_html(ra_date($ramVar_post)) ?></span>
                                <span class="ra-meta-dot">·</span>
                                <span><?= (int) ra_read_time($ramVar_post) ?> min read</span>
                            </div>
                            <h3 class="ra-post-title">
                                <a href="<?= ram_esc_url(ram_get_permalink($ramVar_post)) ?>"><?= ram_esc_html((string) $ramVar_post['title']) ?></a>
                            </h3>
                            <p class="ra-post-excerpt"><?= ram_esc_html(ra_excerpt($ramVar_post, 130)) ?></p>
                            <a class="ra-post-link" href="<?= ram_esc_url(ram_get_permalink($ramVar_post)) ?>">Read story <?= ra_icon('arrow', 15) ?></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="ra-empty" data-ra-reveal>
                <div class="ra-empty-icon"><?= ra_icon('pen', 28) ?></div>
                <h3>No stories yet</h3>
                <p>Your first post is one click away.</p>
                <?php if (ram_is_logged_in()): ?>
                    <a class="ra-btn ra-btn-primary" href="<?= ram_esc_url(ram_admin_url('post-new')) ?>">Write a post</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ── TESTIMONIALS ─────────────────────────────────────────────── -->
<section class="ra-section ra-quotes-section">
    <div class="ra-container">
        <div class="ra-section-head">
            <span class="ra-eyebrow" data-ra-reveal><span class="ra-dot"></span> 04 · Kind words</span>
            <h2 data-ra-reveal style="--d:.05s">Loved by <em class="ra-grad-text">builders.</em></h2>
        </div>
    </div>

    <div class="ra-quotes">
        <div class="ra-quotes-track">
            <?php
            $ramVar_quotes = [
                ['Aurora made our relaunch feel effortless. It is genuinely the most polished CMS we have used.', 'Ravi S.', 'Product Lead'],
                ['The motion design is subtle but everywhere. Our bounce rate dropped almost immediately.', 'Sita M.', 'Growth'],
                ['From setup to publish in under an hour. The developer experience is outstanding.', 'Lakshman P.', 'Engineer'],
                ['Beautiful defaults, zero clutter. Our writers actually enjoy publishing again.', 'Nandan K.', 'Editor'],
            ];
            foreach ($ramVar_quotes as $ramVar_q): ?>
                <figure class="ra-quote ra-spot">
                    <div class="ra-stars" role="img" aria-label="5 out of 5 stars"><?= str_repeat(ra_icon('star', 14), 5) ?></div>
                    <blockquote><?= ram_esc_html($ramVar_q[0]) ?></blockquote>
                    <figcaption>
                        <span class="ra-avatar"><?= ram_esc_html(ra_initial($ramVar_q[1])) ?></span>
                        <span><strong><?= ram_esc_html($ramVar_q[1]) ?></strong><small><?= ram_esc_html($ramVar_q[2]) ?></small></span>
                    </figcaption>
                </figure>
            <?php endforeach; ?>
            <?php foreach ($ramVar_quotes as $ramVar_q): ?>
                <figure class="ra-quote ra-spot" aria-hidden="true">
                    <div class="ra-stars"><?= str_repeat(ra_icon('star', 14), 5) ?></div>
                    <blockquote><?= ram_esc_html($ramVar_q[0]) ?></blockquote>
                    <figcaption>
                        <span class="ra-avatar"><?= ram_esc_html(ra_initial($ramVar_q[1])) ?></span>
                        <span><strong><?= ram_esc_html($ramVar_q[1]) ?></strong><small><?= ram_esc_html($ramVar_q[2]) ?></small></span>
                    </figcaption>
                </figure>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ── CTA ──────────────────────────────────────────────────────── -->
<section class="ra-section" id="contact">
    <div class="ra-container">
        <div class="ra-cta" data-ra-reveal>
            <div class="ra-cta-glow" aria-hidden="true"></div>
            <div class="ra-cta-ornament" aria-hidden="true"><?= ra_icon('sparkle', 26) ?></div>
            <span class="ra-eyebrow"><span class="ra-dot"></span> 05 · Ready when you are</span>
            <h2>Let’s build something <em class="ra-grad-text">remarkable.</em></h2>
            <p>Join thousands of teams publishing with confidence on Ramayan Aurora.</p>
            <div class="ra-cta-actions">
                <a class="ra-btn ra-btn-primary ra-btn-lg" href="<?= ram_esc_url(ram_admin_url('login.php')) ?>">
                    <span>Get started free</span><?= ra_icon('arrow', 18, 'ra-btn-ico') ?>
                </a>
                <a class="ra-btn ra-btn-ghost ra-btn-lg" href="<?= ram_esc_url($ramVar_siteUrl . '/about') ?>">Talk to us</a>
            </div>
            <small class="ra-cta-note">No credit card required · Set up in minutes</small>
        </div>
    </div>
</section>

<?php require __DIR__ . '/parts/footer.php'; ?>
