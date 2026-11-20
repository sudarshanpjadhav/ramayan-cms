<?php
/**
 * Ramayan Business Theme — Main Template
 * Full business landing page with hero, features, services, etc.
 */

$siteName = ram_get_option('site_name', 'Ramayan CMS');
$siteDesc = ram_get_option('site_description', 'Professional business solutions');
$siteUrl = defined('RAM_URL') ? RAM_URL : '/';
$posts = ram_get_posts(['limit' => 6, 'status' => 'publish']);
$categories = ram_get_categories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ram_esc_html($siteName) ?></title>
    <meta name="description" content="<?= ram_esc_html($siteDesc) ?>">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/lanka/ram-business/style.css">
</head>

<body>

<!-- HEADER -->
<header class="b-header" id="header">
    <div class="b-header-inner">
        <a href="<?= $siteUrl ?>" class="b-logo">
            <span>🏹</span> <span><?= ram_esc_html($siteName) ?></span>
        </a>
        
        <nav class="b-nav">
            <ul>
                <li><a href="<?= $siteUrl ?>">Home</a></li>
                <li><a href="#features">Features</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
                <?php if (ram_is_logged_in()): ?>
                <li><a href="<?= ram_admin_url() ?>" class="b-cta">Ayodhya Darbar</a></li>
                <?php else: ?>
                <li><a href="<?= ram_admin_url('login.php') ?>" class="b-cta">Enter Ayodhya</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<!-- HERO SECTION -->
<section class="b-hero">
    <div class="b-hero-inner">
        <div class="b-hero-content">
            <span class="b-hero-tag">✨ Welcome to <?= ram_esc_html($siteName) ?></span>
            <h1>Build Your Dream Website Today</h1>
            <p><?= ram_esc_html($siteDesc) ?>. We provide professional solutions to help your business grow and succeed in the digital world.</p>
            
            <div class="b-hero-buttons">
                <a href="<?= $siteUrl ?>/about" class="b-btn b-btn-primary">Learn More</a>
                <a href="<?= $siteUrl ?>/contact" class="b-btn b-btn-secondary">Contact Us</a>
            </div>
        </div>
        
        <div class="b-hero-image">
            <div class="b-hero-mockup">
                <div class="b-hero-mockup-inner">
                    🚀
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES SECTION -->
<section class="b-section" id="features">
    <div class="b-section-inner">
        <div class="b-section-header">
            <span class="b-section-tag">Why Choose Us</span>
            <h2>Everything You Need to Succeed</h2>
            <p>We provide all the tools and features you need to create a stunning online presence.</p>
        </div>
        
        <div class="b-features-grid">
            <div class="b-feature-card">
                <div class="b-feature-icon">⚡</div>
                <h3>Lightning Fast</h3>
                <p>Optimized for speed with near-instant page loads. Your visitors will love the smooth experience.</p>
            </div>
            
            <div class="b-feature-card">
                <div class="b-feature-icon">🎨</div>
                <h3>Beautiful Design</h3>
                <p>Modern, professional designs that look amazing on all devices and screen sizes.</p>
            </div>
            
            <div class="b-feature-card">
                <div class="b-feature-icon">🔒</div>
                <h3>Secure & Reliable</h3>
                <p>Enterprise-grade security to protect your data and your customers' information.</p>
            </div>
            
            <div class="b-feature-card">
                <div class="b-feature-icon">📱</div>
                <h3>Mobile Ready</h3>
                <p>Fully responsive websites that work perfectly on phones, tablets, and desktops.</p>
            </div>
            
            <div class="b-feature-card">
                <div class="b-feature-icon">🔧</div>
                <h3>Easy Customization</h3>
                <p>Intuitive controls to customize your site without any coding knowledge.</p>
            </div>
            
            <div class="b-feature-card">
                <div class="b-feature-icon">📊</div>
                <h3>Analytics</h3>
                <p>Built-in analytics to track visitors, engagement, and conversion metrics.</p>
            </div>
        </div>
    </div>
</section>

<!-- SERVICES SECTION -->
<section class="b-section b-section-light" id="services">
    <div class="b-section-inner">
        <div class="b-section-header">
            <span class="b-section-tag">What We Offer</span>
            <h2>Our Services</h2>
            <p>Comprehensive solutions tailored to meet your business needs.</p>
        </div>
        
        <div class="b-services-grid">
            <div class="b-service-card">
                <div class="b-service-icon">🌐</div>
                <h3>Web Development</h3>
                <p>Custom websites built with modern technologies.</p>
            </div>
            
            <div class="b-service-card">
                <div class="b-service-icon">📱</div>
                <h3>Mobile Apps</h3>
                <p>Native and cross-platform mobile applications.</p>
            </div>
            
            <div class="b-service-card">
                <div class="b-service-icon">🎯</div>
                <h3>Digital Marketing</h3>
                <p>Strategic campaigns to grow your audience.</p>
            </div>
            
            <div class="b-service-card">
                <div class="b-service-icon">☁️</div>
                <h3>Cloud Solutions</h3>
                <p>Scalable cloud infrastructure and services.</p>
            </div>
        </div>
    </div>
</section>

<!-- POSTS SECTION -->
<?php if (!empty($posts)): ?>
<section class="b-section" id="blog">
    <div class="b-section-inner">
        <div class="b-section-header">
            <span class="b-section-tag">Latest Updates</span>
            <h2>From Our Blog</h2>
            <p>Stay informed with our latest news and insights.</p>
        </div>
        
        <div class="b-posts-grid">
            <?php foreach ($posts as $post): ?>
            <article class="b-post-card">
                <?php if (!empty($post['featured_image'])): ?>
                <div class="b-post-image">
                    <img src="<?= $post['featured_image'] ?>" alt="<?= $post['title'] ?>">
                </div>
                <?php endif; ?>
                
                <div class="b-post-content">
                    <span class="b-post-category">Blog</span>
                    <h3 class="b-post-title">
                        <a href="<?= ram_get_permalink($post) ?>"><?= ram_esc_html($post['title']) ?></a>
                    </h3>
                    <div class="b-post-meta">
                        <?= date('M j, Y', strtotime($post['published_at'] ?: $post['created_at'])) ?>
                    </div>
                    <div class="b-post-excerpt">
                        <?= ram_esc_html(substr(strip_tags($post['content']), 0, 100)) ?>...
                    </div>
                    <a href="<?= ram_get_permalink($post) ?>" class="b-post-link">Read more →</a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA SECTION -->
<section class="b-cta-section" id="contact">
    <div class="b-cta-inner">
        <h2>Ready to Get Started?</h2>
        <p>Join thousands of satisfied customers who have transformed their online presence.</p>
        <div class="b-hero-buttons" style="justify-content: center;">
            <a href="<?= ram_admin_url('login.php') ?>" class="b-btn b-btn-primary">Start Free Trial</a>
            <a href="<?= $siteUrl ?>/contact" class="b-btn b-btn-secondary">Talk to Sales</a>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="b-footer">
    <div class="b-footer-inner">
        <div class="b-footer-brand">
            <div class="b-footer-logo">🏹 <?= ram_esc_html($siteName) ?></div>
            <p><?= ram_esc_html($siteDesc) ?>. Professional solutions for modern businesses.</p>
            
            <div class="b-footer-social">
                <a href="#" class="b-social-link">𝕏</a>
                <a href="#" class="b-social-link">◉</a>
                <a href="#" class="b-social-link">▶</a>
                <a href="#" class="b-social-link">📘</a>
            </div>
        </div>
        
        <div class="b-footer-col">
            <h4>Company</h4>
            <ul>
                <li><a href="<?= $siteUrl ?>/about">About Us</a></li>
                <li><a href="<?= $siteUrl ?>/careers">Careers</a></li>
                <li><a href="<?= $siteUrl ?>/press">Press</a></li>
                <li><a href="<?= $siteUrl ?>/contact">Contact</a></li>
            </ul>
        </div>
        
        <div class="b-footer-col">
            <h4>Resources</h4>
            <ul>
                <li><a href="#">Documentation</a></li>
                <li><a href="#">Help Center</a></li>
                <li><a href="#">API Reference</a></li>
                <li><a href="#">Status</a></li>
            </ul>
        </div>
        
        <div class="b-footer-col">
            <h4>Legal</h4>
            <ul>
                <li><a href="<?= $siteUrl ?>/privacy">Privacy Policy</a></li>
                <li><a href="<?= $siteUrl ?>/terms">Terms of Service</a></li>
                <li><a href="<?= $siteUrl ?>/cookies">Cookie Policy</a></li>
            </ul>
        </div>
    </div>
    
    <div class="b-footer-bottom">
        <p>© <?= date('Y') ?> <?= ram_esc_html($siteName) ?>. All rights reserved.</p>
        <div class="b-footer-links">
            <a href="<?= $siteUrl ?>/privacy">Privacy</a>
            <a href="<?= $siteUrl ?>/terms">Terms</a>
            <a href="<?= $siteUrl ?>/sitemap">Sitemap</a>
        </div>
    </div>
</footer>

<script>
// Header scroll effect
window.addEventListener('scroll', function() {
    const header = document.getElementById('header');
    if (window.scrollY > 50) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
});

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});
</script>

</body>
</html>