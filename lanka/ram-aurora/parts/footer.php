<?php
/**
 * Ramayan Aurora — Global Footer
 */

$siteName = $siteName ?? (string) ram_get_option('site_name', 'Ramayan CMS');
$siteDesc = $siteDesc ?? (string) ram_get_option('site_description', 'A premium publishing experience.');
$siteUrl  = rtrim((string) ($siteUrl ?? (defined('RAM_URL') ? RAM_URL : '/')), '/');
?>
</main>

<footer class="ra-footer">
    <div class="ra-footer-glow" aria-hidden="true"></div>
    <div class="ra-container">
        <div class="ra-footer-grid">
            <div class="ra-footer-brand">
                <a class="ra-logo" href="<?= ram_esc_url($siteUrl . '/') ?>" aria-label="<?= ram_esc_attr($siteName) ?> — home">
                    <span class="ra-logo-mark"><?= ra_icon('bow', 21) ?></span>
                    <span class="ra-logo-text"><?= ram_esc_html($siteName) ?></span>
                </a>
                <p><?= ram_esc_html($siteDesc) ?></p>

                <form class="ra-newsletter" method="post" action="<?= ram_esc_url($siteUrl . '/#contact') ?>" aria-label="Newsletter signup">
                    <label class="ra-sr-only" for="ra-news-email">Email address</label>
                    <span class="ra-newsletter-ico"><?= ra_icon('mail', 18) ?></span>
                    <input id="ra-news-email" type="email" name="email" placeholder="Your email address" autocomplete="email" required>
                    <button type="submit" aria-label="Subscribe"><?= ra_icon('arrow', 18) ?></button>
                </form>

                <div class="ra-socials" aria-label="Social links">
                    <a href="#" aria-label="X (Twitter)">X</a>
                    <a href="#" aria-label="GitHub">GH</a>
                    <a href="#" aria-label="YouTube">YT</a>
                    <a href="#" aria-label="LinkedIn">in</a>
                </div>
            </div>

            <div class="ra-footer-col">
                <h4>Product</h4>
                <ul>
                    <li><a href="<?= ram_esc_url($siteUrl . '/#features') ?>">Features</a></li>
                    <li><a href="<?= ram_esc_url($siteUrl . '/#showcase') ?>">Workflow</a></li>
                    <li><a href="<?= ram_esc_url($siteUrl . '/#blog') ?>">Journal</a></li>
                    <li><a href="<?= ram_esc_url(ram_admin_url()) ?>">Dashboard</a></li>
                </ul>
            </div>

            <div class="ra-footer-col">
                <h4>Company</h4>
                <ul>
                    <li><a href="<?= ram_esc_url($siteUrl . '/about') ?>">About</a></li>
                    <li><a href="<?= ram_esc_url($siteUrl . '/#contact') ?>">Contact</a></li>
                    <li><a href="<?= ram_esc_url($siteUrl . '/careers') ?>">Careers</a></li>
                    <li><a href="<?= ram_esc_url($siteUrl . '/press') ?>">Press</a></li>
                </ul>
            </div>

            <div class="ra-footer-col">
                <h4>Legal</h4>
                <ul>
                    <li><a href="<?= ram_esc_url($siteUrl . '/privacy') ?>">Privacy</a></li>
                    <li><a href="<?= ram_esc_url($siteUrl . '/terms') ?>">Terms</a></li>
                    <li><a href="<?= ram_esc_url($siteUrl . '/cookies') ?>">Cookies</a></li>
                    <li><a href="<?= ram_esc_url($siteUrl . '/sitemap') ?>">Sitemap</a></li>
                </ul>
            </div>
        </div>

        <div class="ra-footer-bottom">
            <p>© <?= date('Y') ?> <?= ram_esc_html($siteName) ?>. Crafted with Ramayan CMS.</p>
            <div class="ra-footer-links">
                <a href="<?= ram_esc_url($siteUrl . '/privacy') ?>">Privacy</a>
                <a href="<?= ram_esc_url($siteUrl . '/terms') ?>">Terms</a>
                <a href="<?= ram_esc_url($siteUrl . '/#contact') ?>">Support</a>
            </div>
        </div>
    </div>
</footer>

<button class="ra-top" id="ra-top" type="button" aria-label="Back to top"><?= ra_icon('up', 20) ?></button>

<script>
(function () {
    'use strict';

    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var finePointer  = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

    /* ── Sticky header + progress ──────────────────────────────── */
    var header   = document.getElementById('ra-header');
    var progress = document.getElementById('ra-progress');
    var topBtn   = document.getElementById('ra-top');
    var ticking  = false;

    function onScroll() {
        var y = window.scrollY || window.pageYOffset || 0;

        if (header) header.classList.toggle('is-scrolled', y > 24);
        if (topBtn) topBtn.classList.toggle('is-visible', y > 700);

        if (progress) {
            var h = document.documentElement.scrollHeight - window.innerHeight;
            progress.style.width = (h > 0 ? (y / h) * 100 : 0).toFixed(2) + '%';
        }
        ticking = false;
    }
    window.addEventListener('scroll', function () {
        if (!ticking) { ticking = true; window.requestAnimationFrame(onScroll); }
    }, { passive: true });
    onScroll();

    /* ── Mobile menu ───────────────────────────────────────────── */
    var burger = document.getElementById('ra-burger');
    var nav    = document.getElementById('ra-nav');

    function setMenu(open) {
        if (!nav || !burger) return;
        nav.classList.toggle('is-open', open);
        burger.classList.toggle('is-open', open);
        burger.setAttribute('aria-expanded', open ? 'true' : 'false');
        document.body.classList.toggle('ra-menu-open', open);
    }

    if (burger && nav) {
        burger.addEventListener('click', function () {
            setMenu(!nav.classList.contains('is-open'));
        });
        nav.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', function () { setMenu(false); });
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && nav.classList.contains('is-open')) {
                setMenu(false);
                burger.focus();
            }
        });
    }

    /* ── Reveal on scroll ──────────────────────────────────────── */
    var reveals = document.querySelectorAll('[data-ra-reveal]');
    if ('IntersectionObserver' in window && !reduceMotion) {
        var revealObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

        reveals.forEach(function (el) { revealObserver.observe(el); });
    } else {
        reveals.forEach(function (el) { el.classList.add('is-visible'); });
    }

    /* Failsafe: if the observer never fires (throttled/hidden tab, old engine),
       reveal whatever is currently in view so content is never stuck invisible. */
    function revealInView() {
        var vh = window.innerHeight || 800;
        reveals.forEach(function (el) {
            if (el.classList.contains('is-visible')) return;
            var r = el.getBoundingClientRect();
            if (r.top < vh * 1.05 && r.bottom > -80) el.classList.add('is-visible');
        });
    }
    window.addEventListener('load', revealInView);
    window.setTimeout(revealInView, 1200);
    window.setTimeout(revealInView, 3000);

    /* ── Animated counters ─────────────────────────────────────── */
    var counters = document.querySelectorAll('[data-ra-count]');

    function animateCounter(el) {
        var target   = parseFloat(el.getAttribute('data-ra-count')) || 0;
        var decimals = parseInt(el.getAttribute('data-ra-decimals') || '0', 10);
        var suffix   = el.getAttribute('data-ra-suffix') || '';
        var prefix   = el.getAttribute('data-ra-prefix') || '';

        if (reduceMotion) {
            el.textContent = prefix + target.toFixed(decimals) + suffix;
            return;
        }

        var start = null;
        var duration = 1500;

        function step(ts) {
            if (!start) start = ts;
            var p = Math.min((ts - start) / duration, 1);
            var eased = 1 - Math.pow(1 - p, 3);
            el.textContent = prefix + (target * eased).toFixed(decimals) + suffix;
            if (p < 1) window.requestAnimationFrame(step);
        }
        window.requestAnimationFrame(step);
    }

    if ('IntersectionObserver' in window) {
        var countObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    countObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        counters.forEach(function (el) { countObserver.observe(el); });
    } else {
        counters.forEach(animateCounter);
    }

    /* ── Pointer spotlight ─────────────────────────────────────── */
    document.querySelectorAll('.ra-spot').forEach(function (card) {
        card.addEventListener('pointermove', function (e) {
            var rect = card.getBoundingClientRect();
            card.style.setProperty('--mx', (e.clientX - rect.left) + 'px');
            card.style.setProperty('--my', (e.clientY - rect.top) + 'px');
        });
    });

    /* ── Ambient parallax on aurora blobs ──────────────────────── */
    if (!reduceMotion && finePointer) {
        var aurora = document.querySelector('.ra-aurora');
        if (aurora) {
            var tx = 0, ty = 0, cx = 0, cy = 0, raf = null;

            window.addEventListener('pointermove', function (e) {
                tx = (e.clientX / window.innerWidth - 0.5) * 2;
                ty = (e.clientY / window.innerHeight - 0.5) * 2;
                if (!raf) raf = window.requestAnimationFrame(tick);
            }, { passive: true });

            function tick() {
                cx += (tx - cx) * 0.06;
                cy += (ty - cy) * 0.06;
                aurora.style.setProperty('--px', cx.toFixed(3));
                aurora.style.setProperty('--py', cy.toFixed(3));
                raf = (Math.abs(tx - cx) > 0.001 || Math.abs(ty - cy) > 0.001)
                    ? window.requestAnimationFrame(tick) : null;
            }
        }
    }

    /* ── Subtle 3D tilt ────────────────────────────────────────── */
    if (!reduceMotion && finePointer) {
        document.querySelectorAll('[data-ra-tilt]').forEach(function (el) {
            var max = 6;
            el.addEventListener('pointermove', function (e) {
                var rect = el.getBoundingClientRect();
                var px = (e.clientX - rect.left) / rect.width - 0.5;
                var py = (e.clientY - rect.top) / rect.height - 0.5;
                el.style.transform = 'perspective(1100px) rotateX(' + (-py * max).toFixed(2)
                    + 'deg) rotateY(' + (px * max).toFixed(2) + 'deg)';
            });
            el.addEventListener('pointerleave', function () { el.style.transform = ''; });
        });
    }

    /* ── Back to top ───────────────────────────────────────────── */
    if (topBtn) {
        topBtn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
        });
    }

    /* ── Smooth anchor scrolling ───────────────────────────────── */
    document.querySelectorAll('a[href*="#"]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            var url = link.getAttribute('href');
            var hashIndex = url.indexOf('#');
            if (hashIndex === -1) return;

            var path = url.slice(0, hashIndex);
            var hash = url.slice(hashIndex);
            var samePage = path === '' || path === window.location.pathname
                || path === window.location.href.replace(window.location.hash, '');

            if (!samePage || hash === '#') return;
            var target = document.querySelector(hash);
            if (!target) return;

            e.preventDefault();
            target.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'start' });
            history.replaceState(null, '', hash);
        });
    });
})();
</script>

</body>
</html>
