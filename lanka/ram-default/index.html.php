<?php
/**
 * Ramayan Modern Theme — Main Template
 */

$siteName = ram_get_option('site_name', 'Ramayan CMS');
$siteDesc = ram_get_option('site_description', '');
$siteUrl = defined('RAM_URL') ? RAM_URL : '/';
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
    
    <link rel="stylesheet" href="/lanka/ram-default/style.css">
</head>

<body>

<header class="site-header">
    <div class="container header-inner">
        <a href="<?= $siteUrl ?>" class="site-logo">🏹 <span><?= ram_esc_html($siteName) ?></span></a>
        
        <nav class="main-navigation">
            <ul>
                <li><a href="<?= $siteUrl ?>">Home</a></li>
                <li><a href="<?= $siteUrl ?>/about">About</a></li>
                <li><a href="<?= $siteUrl ?>/contact">Contact</a></li>
                <?php if (ram_is_logged_in()): ?>
                <li><a href="<?= ram_admin_url() ?>">Ayodhya</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<section class="hero">
    <div class="container">
        <h1><?= ram_esc_html($siteName) ?></h1>
        <p><?= ram_esc_html($siteDesc ?: 'Welcome to our website') ?></p>
    </div>
</section>

<main class="container content-area">
    <div class="posts-grid">
        <?php
        $posts = ram_get_posts(['limit' => 10, 'status' => 'publish']);
        
        if (!empty($posts)):
            foreach ($posts as $post): ?>
            <article class="post-card">
                <?php if (!empty($post['featured_image'])): ?>
                <div class="post-image">
                    <img src="<?= $post['featured_image'] ?>" alt="<?= $post['title'] ?>">
                </div>
                <?php endif; ?>
                
                <div class="post-content">
                    <h2 class="post-title">
                        <a href="<?= ram_get_permalink($post) ?>"><?= ram_esc_html($post['title']) ?></a>
                    </h2>
                    
                    <div class="post-meta">
                        <span><?= date('F j, Y', strtotime($post['published_at'] ?: $post['created_at'])) ?></span>
                        <span> • </span>
                        <span>By the Darbar</span>
                    </div>
                    
                    <div class="post-excerpt">
                        <?= ram_kses($post['excerpt'] ?: substr(strip_tags($post['content']), 0, 200) . '...') ?>
                    </div>
                    
                    <a href="<?= ram_get_permalink($post) ?>" class="read-more">Read the story →</a>
                </div>
            </article>
            <?php endforeach;
        else: ?>
        <div class="not-found">
            <h1>📝</h1>
            <h3>No chronicles found</h3>
            <p>Return soon for a new chapter!</p>
            <?php if (ram_is_logged_in()): ?>
            <a href="<?= ram_admin_url('post-new') ?>" class="btn">Write a Chronicle</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <aside class="sidebar">
        <div class="widget">
            <h3 class="widget-title">About the Kingdom</h3>
            <p style="color: var(--muted); font-size: 0.9rem;">
                <?= ram_esc_html($siteDesc ?: 'Welcome to ' . $siteName) ?>
            </p>
        </div>
        
        <div class="widget">
            <h3 class="widget-title">Recent Chronicles</h3>
            <ul>
                <?php
                $recent = ram_get_posts(['limit' => 5, 'status' => 'publish']);
                foreach ($recent as $r): ?>
                <li><a href="<?= ram_get_permalink($r) ?>"><?= ram_esc_html($r['title']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <div class="widget">
            <h3 class="widget-title">Kaands</h3>
            <ul>
                <?php
                $cats = ram_get_categories();
                foreach ($cats as $cat): ?>
                <li><a href="<?= $siteUrl ?>?category=<?= $cat['slug'] ?>"><?= ram_esc_html($cat['name']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </aside>
</main>

<footer class="site-footer">
    <div class="container footer-inner">
        <div class="footer-links">
            <a href="<?= $siteUrl ?>">Home</a>
            <a href="<?= $siteUrl ?>/about">About</a>
            <a href="<?= $siteUrl ?>/privacy">Privacy</a>
        </div>
        <p style="color: var(--muted); font-size: 0.9rem;">
            © <?= date('Y') ?> <?= ram_esc_html($siteName) ?>. Powered by <a href="https://finggu.com">Ramayan CMS</a>
        </p>
    </div>
</footer>

</body>
</html>