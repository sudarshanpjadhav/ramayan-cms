# RAMAYAN CMS — Theme Creation Guide

Version: 1.0  
Audience: Designers, frontend developers, PHP developers, and agencies  
Scope: Creating and distributing a frontend theme for Ramayan CMS

---

## 1. What a Ramayan Theme Is

A Ramayan CMS theme is a directory inside the `lanka/` folder. It controls:

- Site layout
- Header and footer
- Home page
- Post and page templates
- Archive and 404 templates
- Colors, typography, images, and responsive behavior
- Theme-specific JavaScript and PHP helpers

The theme does not need to modify the CMS core. A theme should only add presentation files and optional theme behavior.

The main systems used by themes are:

| Ramayan term | Purpose |
|---|---|
| **Lanka** | Theme storage area |
| **Chitrakoot** | Template rendering engine |
| **Pushpak** | Content routing |
| **Akashvani** | Theme hooks and actions |
| **Sudarshana** | Output escaping and security |
| **Patal** | Database and content data |

---

## 2. Requirements

Before creating a theme, ensure the developer environment has:

- PHP 8.1 or newer
- Apache or another PHP-compatible web server
- Ramayan CMS installed and running
- A modern browser
- Access to the project folder
- Basic knowledge of PHP, HTML, CSS, and JavaScript

Do not directly edit themes while the CMS is running on a production server. Develop locally or in a staging environment first.

---

## 3. Theme Naming Rules

Theme directories must use lowercase kebab-case names.

Examples:

```text
lanka/ramayan-editorial/
lanka/sita-magazine/
lanka/hanuman-business/
```

Avoid spaces, uppercase letters, underscores, and special characters in folder names.

Recommended names:

- Theme identity: `Ramayan Editorial`
- Directory name: `ramayan-editorial`
- Main CSS class prefix: `ram-` or a theme-specific BEM prefix
- Theme PHP functions: `ramFn_` or `ramFn_themeName_`
- Theme JavaScript variables: `ramVar_`
- Theme JavaScript functions: `ramFn_`

Follow the visible naming convention in `RAMAYAN_VISIBLE_NAMING.md` for user-facing labels.

---

## 4. Create the Theme Folder

Create a directory below `lanka/`:

```text
lanka/
└── my-first-theme/
```

Use your theme slug consistently. For this guide, the slug is:

```text
my-first-theme
```

Every file path in this guide can be changed to your own theme slug.

---

## 5. Minimum Theme Structure

A theme can begin with these files:

```text
lanka/my-first-theme/
├── style.css
├── functions.php
├── index.html.php
├── page.html.php
├── single.html.php
├── archive.html.php
├── 404.html.php
├── search.html.php
├── assets/
│   ├── script.js
│   └── images/
└── parts/
    ├── header.html.php
    └── footer.html.php
```

The smallest functional theme requires:

```text
style.css
functions.php
index.html.php
```

However, `page.html.php`, `single.html.php`, `404.html.php`, and `archive.html.php` are recommended for a complete theme.

---

## 6. Add Theme Metadata

Create `lanka/my-first-theme/style.css`.

The Ayodhya theme manager reads:

- `Theme Name`
- `Version`
- `Description`
- `Author`
- `Tags`

Add a metadata header:

```css
/*
Theme Name: My First Theme
Description: A responsive starter theme for Ramayan CMS.
Version: 1.0.0
Author: Your Name
Author URI: https://example.com
Tags: responsive, editorial, modern
*/

:root {
    --theme-primary: #F5A623;
    --theme-primary-dark: #A97822;
    --theme-background: #0F172A;
    --theme-surface: #1E293B;
    --theme-text: #F8FAFC;
    --theme-muted: #94A3B8;
    --theme-border: rgba(255, 255, 255, 0.10);
    --theme-content-width: 1200px;
}

* {
    box-sizing: border-box;
}
```

Use CSS variables instead of repeating color values throughout the stylesheet.

---

## 7. Create the Theme Functions File

Create `lanka/my-first-theme/functions.php`.

```php
<?php
/**
 * My First Theme — Functions
 * finggu.com | Author: your-name
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

use Ram\Themes\LankaThemes;

LankaThemes::ramFn_enqueueStyle(
    'my-first-theme-style',
    'lanka/my-first-theme/style.css',
    [],
    '1.0.0'
);

LankaThemes::ramFn_enqueueScript(
    'my-first-theme-script',
    'lanka/my-first-theme/assets/script.js',
    [],
    '1.0.0',
    true
);
```

Important:

- The first argument is a unique asset handle.
- The source must be relative to `RAM_URL`.
- Use `ramFn_` for theme functions.
- Use `$ramVar_` for local variables.
- Do not use jQuery.
- Do not place database queries directly in templates.

The active theme's `functions.php` is loaded automatically by the Lanka theme engine.

---

## 8. Create a Basic JavaScript File

Create `lanka/my-first-theme/assets/script.js`.

```javascript
(function () {
    'use strict';

    var ramVar_menu = document.querySelector('[data-theme-menu]');
    var ramVar_toggle = document.querySelector('[data-theme-menu-toggle]');

    if (!ramVar_menu || !ramVar_toggle) {
        return;
    }

    ramVar_toggle.addEventListener('click', function () {
        var ramVar_isOpen = ramVar_menu.classList.toggle('is-open');
        ramVar_toggle.setAttribute('aria-expanded', ramVar_isOpen ? 'true' : 'false');
    });
})();
```

Use browser APIs and vanilla JavaScript. Do not require jQuery.

---

## 9. Understand the Template Files

Ramayan CMS serves theme templates using `.html.php` files.

The Pushpak router and Chitrakoot engine recognize these main template types:

| Request | Template |
|---|---|
| Home page | `index.html.php` or custom `home.html.php` routing |
| Regular post | `single.html.php` |
| Static page | `page.html.php` |
| Archive | `archive.html.php` |
| Search | `search.html.php` |
| Not found | `404.html.php` |

For a regular theme, start with:

- `index.html.php` for the home fallback
- `single.html.php` for posts
- `page.html.php` for pages
- `404.html.php` for missing pages

The current home route is:

1. `Akashvani` action `ram_render_home`
2. `index.html.php`

The current regular content route is:

1. Match published content by slug in Patal
2. Set `current_post` in the Ram registry
3. Render `single.html.php` for posts
4. Render `page.html.php` for pages
5. Fall back to `index.html.php`

The current 404 route renders `404.html.php` before `index.html.php`.

---

## 10. Create the Header and Footer

Create `parts/header.html.php`:

```php
<?php
$ramVar_siteName = ram_get_option('site_name', 'Ramayan CMS');
$ramVar_siteDescription = ram_get_option('site_description', '');
$ramVar_siteUrl = defined('RAM_URL') ? rtrim(RAM_URL, '/') : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ram_esc_html($ramVar_siteName) ?></title>
    <meta name="description" content="<?= ram_esc_attr($ramVar_siteDescription) ?>">
    <?php ram_head(); ?>
</head>
<body>
<header class="ram-header">
    <a class="ram-logo" href="<?= ram_esc_url($ramVar_siteUrl . '/') ?>">
        <?= ram_esc_html($ramVar_siteName) ?>
    </a>

    <button type="button" data-theme-menu-toggle aria-expanded="false">
        Menu
    </button>

    <nav data-theme-menu aria-label="Primary navigation">
        <a href="<?= ram_esc_url($ramVar_siteUrl . '/') ?>">Home</a>
        <a href="<?= ram_esc_url($ramVar_siteUrl . '/archive') ?>">Archive</a>
        <?php if (ram_is_logged_in()): ?>
            <a href="<?= ram_esc_url(ram_admin_url()) ?>">Ayodhya</a>
        <?php endif; ?>
    </nav>
</header>
<main class="ram-main">
```

Create `parts/footer.html.php`:

```php
</main>

<footer class="ram-footer">
    <p>
        &copy; <?= date('Y') ?>
        <?= ram_esc_html(ram_get_option('site_name', 'Ramayan CMS')) ?>.
        Powered by Ramayan CMS.
    </p>
</footer>

<?php ram_footer(); ?>
</body>
</html>
```

Every public template must include:

- `<?php ram_head(); ?>` inside `<head>`
- `<?php ram_footer(); ?>` immediately before `</body>`

These hooks print the theme's enqueued styles and scripts.

---

## 11. Create the Index Template

Create `lanka/my-first-theme/index.html.php`.

```php
<?php
declare(strict_types=1);

require __DIR__ . '/parts/header.html.php';

$ramVar_siteUrl = defined('RAM_URL') ? rtrim(RAM_URL, '/') : '';
$ramVar_posts = ram_get_posts([
    'post_type' => 'post',
    'status' => 'publish',
    'limit' => 10,
    'orderby' => 'published_at',
    'order' => 'DESC',
]);
?>

<section class="ram-container">
    <h1><?= ram_esc_html(ram_get_option('site_name', 'Ramayan CMS')) ?></h1>
    <p><?= ram_esc_html(ram_get_option('site_description', 'Welcome to Ramayan CMS')) ?></p>
</section>

<section class="ram-container ram-post-list">
    <?php if ($ramVar_posts !== []): ?>
        <?php foreach ($ramVar_posts as $ramVar_post): ?>
            <article class="ram-post">
                <h2>
                    <a href="<?= ram_esc_url(ram_get_permalink($ramVar_post)) ?>">
                        <?= ram_esc_html((string) ($ramVar_post['title'] ?? 'Untitled')) ?>
                    </a>
                </h2>

                <?php if (!empty($ramVar_post['featured_image'])): ?>
                    <img
                        src="<?= ram_esc_url(ram_media_url((string) $ramVar_post['featured_image'])) ?>"
                        alt="<?= ram_esc_attr((string) ($ramVar_post['title'] ?? '')) ?>"
                        loading="lazy"
                    >
                <?php endif; ?>

                <p>
                    <?= ram_esc_html((string) substr(
                        strip_tags((string) ($ramVar_post['excerpt'] ?? $ramVar_post['content'] ?? '')),
                        0,
                        180
                    )) ?>
                </p>
            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No published chronicles are available yet.</p>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/parts/footer.html.php'; ?>
```

Use `ram_get_permalink()` instead of manually constructing `/slug`.

---

## 12. Create the Single Post Template

Create `lanka/my-first-theme/single.html.php`.

```php
<?php
declare(strict_types=1);

require __DIR__ . '/parts/header.html.php';

$ramVar_post = \Ram\Core\Ram::ramFn_getInstance()->ramFn_get('current_post');
?>

<article class="ram-container ram-post ram-post--single">
    <?php if (is_array($ramVar_post)): ?>
        <h1><?= ram_esc_html((string) ($ramVar_post['title'] ?? 'Untitled')) ?></h1>

        <?php if (!empty($ramVar_post['featured_image'])): ?>
            <img
                src="<?= ram_esc_url(ram_media_url((string) $ramVar_post['featured_image'])) ?>"
                alt="<?= ram_esc_attr((string) ($ramVar_post['title'] ?? '')) ?>"
            >
        <?php endif; ?>

        <div class="ram-post__content">
            <?= ram_kses((string) ($ramVar_post['content'] ?? '')) ?>
        </div>
    <?php else: ?>
        <h1>Story not found</h1>
        <p>This chronicle may have been moved or unpublished.</p>
    <?php endif; ?>
</article>

<?php require __DIR__ . '/parts/footer.html.php'; ?>
```

Do not assume `current_post` exists. Always verify that it is an array.

---

## 13. Create the Static Page Template

Create `lanka/my-first-theme/page.html.php`.

```php
<?php
declare(strict_types=1);

require __DIR__ . '/parts/header.html.php';

$ramVar_post = \Ram\Core\Ram::ramFn_getInstance()->ramFn_get('current_post');
?>

<article class="ram-container ram-page">
    <?php if (is_array($ramVar_post)): ?>
        <h1><?= ram_esc_html((string) ($ramVar_post['title'] ?? 'Untitled')) ?></h1>
        <div class="ram-page__content">
            <?= ram_kses((string) ($ramVar_post['content'] ?? '')) ?>
        </div>
    <?php else: ?>
        <h1>Page not found</h1>
    <?php endif; ?>
</article>

<?php require __DIR__ . '/parts/footer.html.php'; ?>
```

---

## 14. Create Archive and 404 Templates

### Archive

Create `lanka/my-first-theme/archive.html.php`:

```php
<?php
declare(strict_types=1);

require __DIR__ . '/parts/header.html.php';

$ramVar_posts = ram_get_posts([
    'post_type' => 'post',
    'status' => 'publish',
    'limit' => 30,
]);
?>

<section class="ram-container">
    <h1>All Chronicles</h1>
    <?php foreach ($ramVar_posts as $ramVar_post): ?>
        <article class="ram-post">
            <h2>
                <a href="<?= ram_esc_url(ram_get_permalink($ramVar_post)) ?>">
                    <?= ram_esc_html((string) ($ramVar_post['title'] ?? 'Untitled')) ?>
                </a>
            </h2>
        </article>
    <?php endforeach; ?>
</section>

<?php require __DIR__ . '/parts/footer.html.php'; ?>
```

### 404

Create `lanka/my-first-theme/404.html.php`:

```php
<?php
declare(strict_types=1);

require __DIR__ . '/parts/header.html.php';
?>

<section class="ram-container ram-not-found">
    <h1>404 — Page not found</h1>
    <p>This page is no longer in the kingdom.</p>
    <a href="<?= ram_esc_url((defined('RAM_URL') ? RAM_URL : '/') . '/') ?>">Back home</a>
</section>

<?php require __DIR__ . '/parts/footer.html.php'; ?>
```

---

## 15. Querying Content

Use global helpers before querying Patal directly.

### Published posts

```php
$ramVar_posts = ram_get_posts([
    'post_type' => 'post',
    'status' => 'publish',
    'limit' => 10,
    'offset' => 0,
    'orderby' => 'published_at',
    'order' => 'DESC',
]);
```

### Published pages

```php
$ramVar_pages = ram_get_posts([
    'post_type' => 'page',
    'status' => 'publish',
    'limit' => 50,
]);
```

### Categories

```php
$ramVar_categories = ram_get_categories();
```

### Tags

```php
$ramVar_tags = ram_get_tags();
```

### Terms attached to a post

```php
$ramVar_postId = (int) ($ramVar_post['id'] ?? 0);
$ramVar_categories = ram_get_post_terms($ramVar_postId, 'category');
$ramVar_tags = ram_get_post_terms($ramVar_postId, 'post_tag');
```

Use PatalDB directly only when no helper covers the required data.

---

## 16. Output Escaping Rules

Every dynamic value must be escaped or sanitized before output.

| Data type | Helper |
|---|---|
| Text | `ram_esc_html()` |
| HTML attributes | `ram_esc_attr()` |
| URLs | `ram_esc_url()` |
| JavaScript | `ram_esc_js()` |
| Trusted rich HTML | `ram_kses()` |
| Email | `ram_sanitize_email()` |

Examples:

```php
<h1><?= ram_esc_html($ramVar_title) ?></h1>

<a href="<?= ram_esc_url($ramVar_url) ?>">Visit</a>

<div title="<?= ram_esc_attr($ramVar_title) ?>"></div>

<article><?= ram_kses($ramVar_post['content']) ?></article>

<script>
var ramVar_title = <?= ram_esc_js($ramVar_title) ?>;
</script>
```

Never use raw variables in HTML, attributes, URLs, or JavaScript.

Do not use `echo $_GET[...]`, `var_dump()`, `print_r()`, or `die()` in a theme.

---

## 17. Media URLs

Use `ram_media_url()` before displaying stored media paths.

```php
$ramVar_mediaUrl = ram_media_url((string) ($ramVar_post['featured_image'] ?? ''));
```

Then escape it for HTML:

```php
<img src="<?= ram_esc_url($ramVar_mediaUrl) ?>" alt="">
```

Do not assume uploads are at `/uploads/`. The site may be installed in a subdirectory.

---

## 18. Site and Theme URLs

Use constants and helpers:

```php
ram_site_url()
ram_get_theme_url()
ram_get_theme_path()
ram_admin_url()
ram_upload_url()
```

Examples:

```php
$ramVar_siteUrl = rtrim(RAM_URL, '/');
$ramVar_themeUrl = ram_get_theme_url();

<link href="<?= ram_esc_url($ramVar_themeUrl . '/assets/style.css') ?>" rel="stylesheet">
```

Prefer `ram_get_theme_url()` instead of hardcoding `lanka/my-first-theme`.

---

## 19. Hooks

Theme PHP can register actions and filters with Akashvani.

### Action example

```php
use Ram\Hooks\AkashvaniHooks;

AkashvaniHooks::ramFn_addAction(
    'ram_render_home',
    static function (): void {
        // Theme setup for home rendering.
    },
    10
);
```

### Common implemented hooks

```text
ram_head
ram_footer
ram_before_render
ram_after_render
ram_render_home
ram_render_single
ram_render_page
ram_render_404
ram_after_theme_setup
ram_get_posts_args
ram_posts_results
ram_permalink
```

Call hooks responsibly. Do not output unescaped content from a hook callback.

---

## 20. Search Template

Create `lanka/my-first-theme/search.html.php`:

```php
<?php
declare(strict_types=1);

require __DIR__ . '/parts/header.html.php';

$ramVar_query = trim((string) ($_GET['s'] ?? $_GET['q'] ?? ''));
$ramVar_posts = ram_get_posts(['limit' => 50, 'status' => 'publish']);

if ($ramVar_query !== '') {
    $ramVar_queryLower = mb_strtolower($ramVar_query);
    $ramVar_posts = array_values(array_filter(
        $ramVar_posts,
        static function (array $ramVar_post) use ($ramVar_queryLower): bool {
            $ramVar_text = mb_strtolower(
                (string) ($ramVar_post['title'] ?? '') . ' ' .
                strip_tags((string) ($ramVar_post['excerpt'] ?? $ramVar_post['content'] ?? ''))
            );
            return str_contains($ramVar_text, $ramVar_queryLower);
        }
    ));
}
?>

<section class="ram-container">
    <h1>Search</h1>

    <form method="get" action="<?= ram_esc_url(RAM_URL . '/') ?>">
        <input
            type="search"
            name="s"
            value="<?= ram_esc_attr($ramVar_query) ?>"
            placeholder="Search chronicles"
        >
        <button type="submit">Search</button>
    </form>

    <p><?= count($ramVar_posts) ?> result(s) found.</p>

    <?php foreach ($ramVar_posts as $ramVar_post): ?>
        <h2>
            <a href="<?= ram_esc_url(ram_get_permalink($ramVar_post)) ?>">
                <?= ram_esc_html((string) ($ramVar_post['title'] ?? 'Untitled')) ?>
            </a>
        </h2>
    <?php endforeach; ?>
</section>

<?php require __DIR__ . '/parts/footer.html.php'; ?>
```

The search template performs local filtering after retrieving published posts in the existing implementation.

---

## 21. Responsive and Accessible Frontend

Every theme should include:

- A viewport meta tag
- Semantic HTML
- Keyboard-operable navigation
- Visible focus styles
- Sufficient color contrast
- Accessible labels for icon-only controls
- Responsive images
- `prefers-reduced-motion` support
- Mobile navigation
- No horizontal overflow

Example focus style:

```css
:focus-visible {
    outline: 3px solid var(--theme-primary);
    outline-offset: 3px;
}
```

Example reduced-motion style:

```css
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        scroll-behavior: auto !important;
        transition-duration: 0.01ms !important;
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
    }
}
```

Recommended breakpoints:

```css
@media (max-width: 1024px) {}
@media (max-width: 768px) {}
@media (max-width: 480px) {}
```

---

## 22. Gold Luxury Scrollbar

The following is suitable for dark, gold-themed Ramayan CMS themes:

```css
html,
body,
* {
    scrollbar-width: thin;
    scrollbar-color: #D6A84B #0F172A;
}

*::-webkit-scrollbar {
    width: 10px;
    height: 10px;
}

*::-webkit-scrollbar-track {
    background: linear-gradient(180deg, #0F172A 0%, #1E293B 100%);
}

*::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #F7D78A 0%, #D6A84B 48%, #A97822 100%);
    border: 2px solid #0F172A;
    border-radius: 999px;
}

*::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #FFE8A3 0%, #E8BC61 48%, #B8862C 100%);
}
```

Keep scrollbars optional and accessible. Do not hide the native scrollbar entirely.

---

## 23. Activate the Theme

1. Place the theme folder below `lanka/`.
2. Open Ayodhya.
3. Open **Lanka Themes**.
4. Locate the theme card.
5. Confirm `Theme Name`, `Version`, `Description`, `Author`, and `Tags` appear correctly.
6. Click **Activate**.
7. Open the public site in a separate browser tab.
8. Test home, posts, pages, search, 404, and mobile layouts.

The theme activation option is stored in the `active_theme` CMS option.

Do not rename the active theme folder after activation. Use one stable directory name for the life of the theme.

---

## 24. Theme Package Distribution

Package the theme as a ZIP containing only its theme folder:

```text
my-first-theme.zip
└── my-first-theme/
    ├── style.css
    ├── functions.php
    ├── index.html.php
    ├── page.html.php
    ├── single.html.php
    ├── archive.html.php
    ├── 404.html.php
    ├── search.html.php
    ├── assets/
    └── parts/
```

Do not package:

- `.git/`
- Cache files
- Database dumps
- `ramayan-config.php`
- Uploads
- Logs
- Node modules
- Local environment files
- API secrets

---

## 25. Testing Checklist

### Installation and activation

- [ ] Theme folder uses lowercase kebab-case.
- [ ] Theme metadata appears in Lanka Themes.
- [ ] Theme activates successfully.
- [ ] Theme deactivates or falls back correctly.

### Templates

- [ ] Home page loads.
- [ ] Published post loads.
- [ ] Static page loads.
- [ ] 404 page loads.
- [ ] Search page loads.
- [ ] Archive page loads if included.
- [ ] Missing template falls back to `index.html.php`.

### Content

- [ ] Titles are escaped.
- [ ] Post content uses `ram_kses()`.
- [ ] URLs use `ram_esc_url()` or `ram_get_permalink()`.
- [ ] Attributes use `ram_esc_attr()`.
- [ ] Featured images use `ram_media_url()`.
- [ ] Empty content and missing images do not break the layout.

### Assets

- [ ] CSS loads with `ram_head()`.
- [ ] JavaScript loads through the theme enqueuer.
- [ ] Asset paths work in subdirectories.
- [ ] Cache-busting versions are updated after releases.

### Security

- [ ] No raw `$_GET`, `$_POST`, or database values are printed.
- [ ] Forms use a Sudarshana nonce.
- [ ] Database access uses PatalDB or a Ramayan helper.
- [ ] No jQuery dependency.
- [ ] No secrets or credentials exist in the theme.

### Frontend quality

- [ ] Desktop layout works.
- [ ] Tablet layout works.
- [ ] Mobile layout works.
- [ ] Navigation is keyboard accessible.
- [ ] Focus states are visible.
- [ ] Images have alternative text.
- [ ] Reduced-motion users are respected.
- [ ] No browser console errors occur.

---

## 26. Troubleshooting

### Theme does not appear

Check:

- Folder is directly under `lanka/`
- Folder name is lowercase and has no spaces
- `style.css` exists
- `Theme Name:` exists in the CSS metadata header

### CSS or JavaScript does not load

Check:

- `functions.php` exists
- Asset handles are unique
- Asset paths are relative to the site root
- `ram_head()` appears inside `<head>`
- `ram_footer()` appears before `</body>`
- Browser cache is cleared

### A post or page template is not used

Check:

- The file is named exactly `single.html.php` or `page.html.php`
- The theme is active
- The content uses `publish` status
- The slug is correctly stored in Patal
- `current_post` is available in the Ram registry

### Featured image is broken

Use `ram_media_url()` before rendering the image:

```php
$ramVar_url = ram_media_url((string) $ramVar_post['featured_image']);
```

### Theme works in the root folder but not in a subdirectory

Do not hardcode `/lanka/`, `/uploads/`, or `/ayodhya/`.

Use:

- `RAM_URL`
- `RAM_THEME_URL`
- `ram_get_theme_url()`
- `ram_upload_url()`
- `ram_admin_url()`
- `ram_get_permalink()`

### Theme output looks unsafe or breaks HTML

Use the correct escape helper:

- Text: `ram_esc_html()`
- Attribute: `ram_esc_attr()`
- URL: `ram_esc_url()`
- Rich HTML: `ram_kses()`

---

## 27. Release Checklist

Before publishing a theme:

1. Increment the theme version in `style.css`.
2. Increment asset versions in `functions.php`.
3. Remove debugging and test data.
4. Remove unused files and third-party libraries.
5. Document required PHP and CMS versions.
6. Provide screenshots.
7. Provide a changelog.
8. License all fonts, images, icons, and code.
9. Include author and support information.
10. ZIP only the theme folder.

---

## 28. Starter Theme Summary

A new theme author should create these files first:

```text
lanka/my-first-theme/
├── style.css
├── functions.php
├── index.html.php
├── page.html.php
├── single.html.php
├── 404.html.php
├── assets/script.js
└── parts/
    ├── header.html.php
    └── footer.html.php
```

Then:

1. Add the theme metadata.
2. Enqueue CSS and JavaScript.
3. Create the shared header and footer.
4. Render published posts.
5. Render a current post or page.
6. Add 404 and archive support.
7. Escape every dynamic value.
8. Test on desktop and mobile.
9. Activate it through Ayodhya.
10. Package and distribute it.
