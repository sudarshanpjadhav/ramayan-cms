<?php
/**
 * Ramayan Aurora — Global Header
 */

require_once __DIR__ . '/helpers.php';

$siteName = $siteName ?? (string) ram_get_option('site_name', 'Ramayan CMS');
$siteDesc = $siteDesc ?? (string) ram_get_option('site_description', 'A premium publishing experience.');
$siteUrl  = $siteUrl ?? (defined('RAM_URL') ? RAM_URL : '/');
$siteUrl  = rtrim($siteUrl, '/');

$ramVar_pageType = 'home';
if (class_exists('Ram\\Core\\Ram')) {
    try {
        $ramVar_pageType = \Ram\Core\Ram::ramFn_getInstance()->ramFn_getPageType();
    } catch (\Throwable) {
        $ramVar_pageType = 'home';
    }
}

$raStyleUrl = ra_theme_url('style.css') . '?ver=2.1.1';
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ram_esc_html($siteName) ?><?= $ramVar_pageType === 'home' ? '' : ' — ' . ram_esc_html((string) ram_get_option('site_description', '')) ?></title>
    <meta name="description" content="<?= ram_esc_html($siteDesc) ?>">
    <meta name="theme-color" content="#06060a">
    <meta name="color-scheme" content="dark">

    <script>document.documentElement.classList.remove('no-js');document.documentElement.classList.add('js');</script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= ram_esc_url($raStyleUrl) ?>">
</head>

<body class="ra-body ra-page-<?= ram_esc_attr($ramVar_pageType) ?>">

<a class="ra-skip" href="#ra-main">Skip to content</a>

<div class="ra-progress" id="ra-progress" aria-hidden="true"></div>

<div class="ra-aurora" aria-hidden="true">
    <span class="ra-blob ra-blob-1"></span>
    <span class="ra-blob ra-blob-2"></span>
    <span class="ra-blob ra-blob-3"></span>
    <span class="ra-blob ra-blob-4"></span>
    <span class="ra-blob ra-blob-5"></span>
</div>
<div class="ra-grain" aria-hidden="true"></div>

<header class="ra-header" id="ra-header">
    <div class="ra-container ra-header-inner">
        <a class="ra-logo" href="<?= ram_esc_url($siteUrl . '/') ?>" aria-label="<?= ram_esc_attr($siteName) ?> — home">
            <span class="ra-logo-mark"><?= ra_icon('bow', 21) ?></span>
            <span class="ra-logo-text"><?= ram_esc_html($siteName) ?></span>
        </a>

        <nav class="ra-nav" id="ra-nav" aria-label="Primary">
            <a href="<?= ram_esc_url($siteUrl . '/') ?>"<?= $ramVar_pageType === 'home' ? ' aria-current="page"' : '' ?>>Home</a>
            <a href="<?= ram_esc_url($siteUrl . '/#features') ?>">Features</a>
            <a href="<?= ram_esc_url($siteUrl . '/#showcase') ?>">Workflow</a>
            <a href="<?= ram_esc_url($siteUrl . '/#blog') ?>">Ram Katha</a>
            <a href="<?= ram_esc_url($siteUrl . '/#contact') ?>">Sampark</a>
        </nav>

        <div class="ra-header-actions">
            <?php if (ram_is_logged_in()): ?>
                <a class="ra-btn ra-btn-ghost ra-hide-sm" href="<?= ram_esc_url(ram_admin_url()) ?>">Ayodhya Darbar</a>
            <?php else: ?>
                <a class="ra-btn ra-btn-ghost ra-hide-sm" href="<?= ram_esc_url(ram_admin_url('login.php')) ?>">Enter Ayodhya</a>
            <?php endif; ?>
            <a class="ra-btn ra-btn-primary ra-hide-sm" href="<?= ram_esc_url(ram_admin_url('login.php')) ?>">
                <span>Start free</span>
                <?= ra_icon('arrow', 17, 'ra-btn-ico') ?>
            </a>
            <button class="ra-burger" id="ra-burger" type="button" aria-label="Toggle menu" aria-expanded="false" aria-controls="ra-nav">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>

<main class="ra-main" id="ra-main">
