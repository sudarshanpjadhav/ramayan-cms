# 🏹 RAMAYAN CMS — The Open Ecosystem
### *A PHP-native, cPanel-ready CMS built to surpass WordPress*
> **Finggu Project | Author: sudarshanpjadhav | Brand: finggu.com**
> **Naming Convention: Ramayan Universe | Version: 1.0.0-alpha**

---

## 📖 TABLE OF CONTENTS

1. [Vision & Philosophy](#1-vision--philosophy)
2. [Why Ramayan Naming Convention](#2-why-ramayan-naming-convention)
3. [Tech Stack Decision — Zero Extra Software](#3-tech-stack-decision--zero-extra-software)
4. [Architecture Overview](#4-architecture-overview)
5. [Folder Structure](#5-folder-structure)
6. [Core Naming Convention (Full Reference)](#6-core-naming-convention-full-reference)
7. [Core Engine — Brahmastra](#7-core-engine--brahmastra)
8. [Hook & Event System — Akashvani](#8-hook--event-system--akashvani)
9. [Plugin System — Vaanar Sena](#9-plugin-system--vaanar-sena)
10. [Theme System — Lanka](#10-theme-system--lanka)
11. [Template Engine — Chitrakoot](#11-template-engine--chitrakoot)
12. [Router — Pushpak](#12-router--pushpak)
13. [ORM / Database Layer — Patal](#13-orm--database-layer--patal)
14. [Admin Panel — Ayodhya](#14-admin-panel--ayodhya)
15. [User & Role System — Panchjanya](#15-user--role-system--panchjanya)
16. [REST API Layer — Brahmaand](#16-rest-api-layer--brahmaand)
17. [File / Media Manager — Nandan](#17-file--media-manager--nandan)
18. [SEO & Schema Layer — Saraswati](#18-seo--schema-layer--saraswati)
19. [Cache System — Amrit](#19-cache-system--amrit)
20. [Marketplace — Kishkindha](#20-marketplace--kishkindha)
21. [CLI Tool — Hanuman](#21-cli-tool--hanuman)
22. [Installer — Swayamvar](#22-installer--swayamvar)
23. [Security Layer — Sudarshana](#23-security-layer--sudarshana)
24. [Multilingual & i18n — Valmiki](#24-multilingual--i18n--valmiki)
25. [Build Phases & Milestones](#25-build-phases--milestones)
26. [Comparison: Ramayan vs WordPress](#26-comparison-ramayan-vs-wordpress)
27. [Contribution Guidelines](#27-contribution-guidelines)
28. [Finggu Branding Rules](#28-finggu-branding-rules)

---

## 1. Vision & Philosophy

**Ramayan CMS** is a 100% PHP-native open-source CMS ecosystem, installable on any cPanel shared hosting — exactly like WordPress — but architecturally superior, developer-friendly, and built for the modern web.

### Core Principles

| Principle | Meaning |
|-----------|---------|
| **Zero Dependency Install** | PHP + MySQL only. No Node, no Composer required for end users |
| **Plugin-First** | Every feature is a plugin. Even core features use the same plugin API |
| **Hook-Driven** | Everything is hookable. No core hacks needed ever |
| **Theme as Language** | Themes are full template systems, not just CSS skins |
| **API Native** | REST API is a first-class citizen, not an afterthought |
| **Contributor Friendly** | Docs, standards, and CLI make contributing trivially easy |
| **Indian-Built, World-Class** | Made in India, designed for the global web |

### What Makes It Better Than WordPress?

- **No legacy debt** — WordPress carries 20+ years of backward compatibility baggage. Ramayan starts clean.
- **Modern PHP (8.1+)** — Named arguments, enums, fibers, readonly properties, match expressions
- **Built-in REST API** — Not bolted on like WP REST API
- **Real hook ordering** — Priority + named hooks + async hooks
- **Plugin isolation** — Plugins can't accidentally break each other
- **Admin UI in vanilla JS** — No jQuery dependency
- **Built-in caching layer** — File cache + MySQL cache + optional Redis adapter
- **SEO baked in** — Schema.org JSON-LD, OpenGraph, canonical URLs out of the box
- **Multilingual native** — i18n built into core, not a plugin

---

## 2. Why Ramayan Naming Convention

The Ramayan epic is India's greatest engineering and leadership story. The characters map perfectly to a CMS:

| Character / Place | Role in CMS | Why It Fits |
|---|---|---|
| **Ram** | Core CMS class | The central authority, everything flows from Ram |
| **Sita** | Content/Post model | The heart of the system, what everything protects |
| **Hanuman** | CLI tool | Hanuman travels anywhere, carries messages, executes tasks |
| **Brahmastra** | Core engine bootstrap | The most powerful weapon — the foundation |
| **Akashvani** | Hook/Event system | Voice from the sky — broadcasting events |
| **Pushpak** | Router | The flying vehicle — takes you anywhere instantly |
| **Lanka** | Theme system | The beautiful, customizable kingdom |
| **Vaanar Sena** | Plugin system | Army of contributors, each specialized |
| **Ayodhya** | Admin panel | The capital city — where decisions are made |
| **Patal** | Database/ORM layer | The deep underground — data lives here |
| **Chitrakoot** | Template engine | Where Ram and Sita rested — rendering, views |
| **Amrit** | Cache system | The immortal nectar — speed and persistence |
| **Kishkindha** | Marketplace | The kingdom of the Vaanar — plugin/theme store |
| **Brahmaand** | REST API | The entire universe of endpoints |
| **Saraswati** | SEO layer | Goddess of knowledge and expression |
| **Valmiki** | i18n / multilingual | The poet who translated for all people |
| **Panchjanya** | User/role system | Krishna's conch — calls users to their roles |
| **Sudarshana** | Security layer | The divine spinning disc — protection |
| **Nandan** | Media manager | The celestial garden — rich media library |
| **Swayamvar** | Installer | The ceremony where setup choices are made |

---

## 3. Tech Stack Decision — Zero Extra Software

### ✅ What Users Need (Same as WordPress)
```
- PHP 8.1+ (already on every cPanel)
- MySQL 5.7+ or MariaDB 10.3+ (already on every cPanel)
- Apache or LiteSpeed (already on every cPanel)
- .htaccess support (already on every cPanel)
```

### ❌ What Users Do NOT Need
```
- Node.js / npm
- Composer (end users — devs can optionally use it)
- Python
- Redis (optional, degrades gracefully to file cache)
- Docker
- Any server-level config changes
```

### Why Pure PHP?

WordPress proved it: **PHP + MySQL on shared hosting = the world's most successful deployment model.**

Ramayan follows the same principle but uses **modern PHP 8.1+** which gives us:
- Enums (for post statuses, hook priorities)
- Named arguments (cleaner API calls)
- Readonly classes (immutable config objects)
- Fibers (async-like behavior without Node)
- Match expressions (cleaner than switch)
- Union types (safer plugin APIs)

### Developer Tooling (Optional, Not Required for Users)
```
- Hanuman CLI (pure PHP CLI — no Node required)
- Composer (optional for plugin devs who want packages)
- Vite/esbuild (optional for theme devs building JS)
```

---

## 4. Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                    RAMAYAN CMS ECOSYSTEM                    │
├─────────────────────────────────────────────────────────────┤
│  REQUEST ──► Pushpak (Router) ──► Brahmastra (Bootstrap)   │
│                                          │                   │
│              ┌───────────────────────────┼──────────────┐   │
│              │                           │              │   │
│           Ayodhya                   Brahmaand       Frontend│
│         (Admin Panel)              (REST API)      (Public) │
│              │                           │              │   │
│              └───────────────────────────┼──────────────┘   │
│                                          │                   │
│         ┌────────────────────────────────┤                   │
│         │           Akashvani            │                   │
│         │        (Hook System)           │                   │
│         └────────────────────────────────┘                   │
│              │          │         │                          │
│         Vaanar Sena   Lanka    Saraswati                     │
│         (Plugins)    (Themes)   (SEO)                        │
│              │          │         │                          │
│         ┌────┴──────────┴─────────┴────┐                    │
│         │         Patal (ORM)          │                    │
│         │      MySQL / MariaDB         │                    │
│         └─────────────────────────────┘                     │
│                         │                                   │
│              Amrit (Cache Layer)                            │
│           File Cache | MySQL Cache | Redis*                 │
└─────────────────────────────────────────────────────────────┘
                    * Redis optional
```

---

## 5. Folder Structure

```
ramayan-cms/
│
├── index.php                          # Entry point (like WP's index.php)
├── .htaccess                          # Apache rewrite rules (auto-generated)
├── ramayan-config.php                 # Config file (like wp-config.php)
├── ramayan-config-sample.php          # Sample config (committed to git)
│
├── ram/                               # CORE ENGINE (never edit)
│   ├── bootstrap/
│   │   ├── RamBootstrap.php           # Brahmastra — main bootstrap
│   │   ├── RamLoader.php              # Autoloader
│   │   └── RamContainer.php           # Dependency injection container
│   ├── core/
│   │   ├── Ram.php                    # Central CMS class (singleton)
│   │   ├── RamConfig.php              # Config reader
│   │   ├── RamRequest.php             # HTTP Request abstraction
│   │   └── RamResponse.php            # HTTP Response abstraction
│   ├── router/                        # Pushpak
│   │   ├── PushpakRouter.php
│   │   └── PushpakRoute.php
│   ├── hooks/                         # Akashvani
│   │   ├── AkashvaniHooks.php         # Hook registration
│   │   ├── AkashvaniFilters.php       # Filter system
│   │   └── AkashvaniEvents.php        # Async events
│   ├── database/                      # Patal
│   │   ├── PatalDB.php                # DB connection + query builder
│   │   ├── PatalORM.php               # ORM base class
│   │   ├── PatalSchema.php            # Schema migration system
│   │   └── PatalSeeder.php            # DB seeder
│   ├── models/
│   │   ├── SitaPost.php               # Post model
│   │   ├── SitaPage.php               # Page model
│   │   ├── SitaMedia.php              # Media model
│   │   ├── SitaTaxonomy.php           # Category/Tag model
│   │   └── SitaMeta.php               # Meta/custom fields
│   ├── user/                          # Panchjanya
│   │   ├── PanchjanyaUser.php
│   │   ├── PanchjanyaRole.php
│   │   ├── PanchjanyaCapability.php
│   │   └── PanchjanyaSession.php
│   ├── cache/                         # Amrit
│   │   ├── AmritCache.php             # Cache interface
│   │   ├── AmritFileCache.php         # File-based cache
│   │   ├── AmritMysqlCache.php        # MySQL-based cache
│   │   └── AmritRedisCache.php        # Redis adapter (optional)
│   ├── seo/                           # Saraswati
│   │   ├── SaraswatiSEO.php
│   │   ├── SaraswatiOpenGraph.php
│   │   ├── SaraswatiSchema.php        # JSON-LD schema
│   │   └── SaraswatiSitemap.php
│   ├── api/                           # Brahmaand REST API
│   │   ├── BrahmaandAPI.php           # API router
│   │   ├── BrahmaandAuth.php          # JWT / API key auth
│   │   ├── BrahmaandEndpoint.php      # Base endpoint class
│   │   └── endpoints/
│   │       ├── PostsEndpoint.php
│   │       ├── PagesEndpoint.php
│   │       ├── MediaEndpoint.php
│   │       └── UsersEndpoint.php
│   ├── template/                      # Chitrakoot
│   │   ├── ChitrakootEngine.php       # Template renderer
│   │   ├── ChitrakootHelpers.php      # Template helper functions
│   │   └── ChitrakootEscape.php       # Output escaping
│   ├── media/                         # Nandan
│   │   ├── NandanLibrary.php
│   │   ├── NandanUploader.php
│   │   └── NandanImageProcessor.php
│   ├── i18n/                          # Valmiki
│   │   ├── ValmikiTranslator.php
│   │   └── ValmikiLoader.php
│   └── security/                      # Sudarshana
│       ├── SudarshanaFirewall.php
│       ├── SudarshanaNonce.php
│       ├── SudarshanaCSRF.php
│       └── SudarshanaXSS.php
│
├── ayodhya/                           # ADMIN PANEL
│   ├── index.php                      # Admin entry
│   ├── assets/
│   │   ├── ayodhya.css
│   │   └── ayodhya.js                 # Vanilla JS only, no jQuery
│   └── views/
│       ├── dashboard.php
│       ├── posts.php
│       ├── pages.php
│       ├── media.php
│       ├── plugins.php
│       ├── themes.php
│       ├── users.php
│       └── settings.php
│
├── vaanar/                            # PLUGINS DIRECTORY
│   └── example-plugin/
│       ├── example-plugin.php         # Plugin manifest + bootstrap
│       ├── plugin.json                # Plugin metadata
│       └── ...
│
├── lanka/                             # THEMES DIRECTORY
│   ├── ram-default/                   # Default bundled theme
│   │   ├── theme.json                 # Theme manifest
│   │   ├── functions.php              # Theme hooks
│   │   ├── templates/
│   │   │   ├── index.html.php
│   │   │   ├── single.html.php
│   │   │   ├── page.html.php
│   │   │   ├── archive.html.php
│   │   │   └── 404.html.php
│   │   ├── parts/
│   │   │   ├── header.html.php
│   │   │   ├── footer.html.php
│   │   │   └── sidebar.html.php
│   │   └── assets/
│   │       ├── style.css
│   │       └── script.js
│
├── kishkindha/                        # MARKETPLACE / UPDATE SYSTEM
│   ├── KishkindhaUpdater.php
│   └── KishkindhaRegistry.php
│
├── uploads/                           # User uploads (like wp-content/uploads)
├── cache/                             # Cache storage
├── logs/                              # System logs
└── hanuman                            # CLI tool (chmod +x, run: php hanuman)
```

---

## 6. Core Naming Convention (Full Reference)

> Every file, class, function, variable, DB table, and CSS class follows Finggu + Ramayan naming.

### PHP Classes
```php
// Core classes — PascalCase with Ramayan prefix
class RamBootstrap {}        // Core bootstrap
class PushpakRouter {}       // Router
class AkashvaniHooks {}      // Hook system
class PatalDB {}             // Database
class SitaPost {}            // Post model
class PanchjanyaUser {}      // User model
class AmritCache {}          // Cache
class SaraswatiSEO {}        // SEO
class BrahmaandAPI {}        // REST API
class NandanLibrary {}       // Media
class ValmikiTranslator {}   // i18n
class SudarshanaFirewall {}  // Security
class ChitrakootEngine {}    // Template engine
class AyodhyaAdmin {}        // Admin panel
class KishkindhaUpdater {}   // Marketplace
```

### PHP Functions (Global Helper Functions)
```php
// Prefix: ram_ (like WordPress uses wp_)
ram_get_post($id)
ram_get_posts($args)
ram_get_option($key, $default)
ram_update_option($key, $value)
ram_the_content()
ram_the_title()
ram_get_permalink($id)
ram_get_template_part($slug, $name)
ram_enqueue_style($handle, $src)
ram_enqueue_script($handle, $src)
ram_current_user()
ram_is_logged_in()
ram_redirect($url)
ram_esc_html($string)
ram_esc_attr($string)
ram_esc_url($url)
ram_kses($html, $allowed)
```

### Hook System Functions
```php
// Actions (like WordPress add_action / do_action)
akashvani_add_action($hook, $callback, $priority, $args)
akashvani_do_action($hook, ...$args)
akashvani_remove_action($hook, $callback)

// Filters (like WordPress add_filter / apply_filters)
akashvani_add_filter($hook, $callback, $priority, $args)
akashvani_apply_filter($hook, $value, ...$args)
akashvani_remove_filter($hook, $callback)

// Shorthand aliases (optional, for developer convenience)
function ram_action($hook, $cb, $p = 10, $a = 1) {
    return akashvani_add_action($hook, $cb, $p, $a);
}
function ram_filter($hook, $cb, $p = 10, $a = 1) {
    return akashvani_add_filter($hook, $cb, $p, $a);
}
```

### PHP Variables
```php
$ram_post          // Current post object
$ram_user          // Current user object
$ram_query         // Current query object
$ram_options       // Site options array
$ram_theme         // Active theme object
$ram_plugins       // Loaded plugins array
```

### Constants
```php
define('RAM_VERSION', '1.0.0');
define('RAM_PATH', __DIR__);
define('RAM_URL', 'https://yoursite.com');
define('RAM_CONTENT_PATH', RAM_PATH . '/vaanar');
define('RAM_THEME_PATH', RAM_PATH . '/lanka');
define('RAM_UPLOAD_PATH', RAM_PATH . '/uploads');
define('RAM_CACHE_PATH', RAM_PATH . '/cache');
define('RAM_ADMIN_PATH', RAM_PATH . '/ayodhya');
define('RAM_DB_PREFIX', 'ram_');          // Like wp_ prefix
define('RAM_DEBUG', false);
define('RAM_API_VERSION', 'v1');
```

### Database Tables
```sql
ram_posts          -- All content (posts, pages, custom types)
ram_postmeta       -- Post metadata / custom fields
ram_terms          -- Categories, tags, taxonomies
ram_term_taxonomy  -- Taxonomy relationships
ram_term_meta      -- Taxonomy metadata
ram_users          -- Users
ram_usermeta       -- User metadata
ram_options        -- Site-wide settings
ram_comments       -- Comments
ram_commentmeta    -- Comment metadata
ram_sessions       -- User sessions
ram_cache          -- MySQL-based cache
ram_logs           -- System activity logs
ram_api_keys       -- REST API keys
ram_media          -- Media library
```

### CSS Classes (Admin + Default Theme)
```css
/* Admin Panel */
.ayodhya-wrap {}
.ayodhya-nav {}
.ayodhya-sidebar {}
.ayodhya-content {}
.ayodhya-card {}
.ayodhya-btn {}
.ayodhya-table {}

/* Frontend Theme Utilities */
.ram-container {}
.ram-grid {}
.ram-col {}
.ram-post {}
.ram-post__title {}
.ram-post__content {}
.ram-post__meta {}
.ram-widget {}
.ram-menu {}
.ram-pagination {}
```

### JS Functions (Admin Panel)
```javascript
// Prefix: ramFn_ (Finggu convention applied)
function ramFn_fetchPosts(args) {}
function ramFn_savePost(data) {}
function ramFn_uploadMedia(file) {}
function ramFn_showNotice(msg, type) {}
function ramFn_ajaxRequest(action, data) {}

// Variables: ramVar_
const ramVar_adminUrl = '/ayodhya/';
const ramVar_apiBase = '/api/v1/';
const ramVar_nonce   = 'xyz123';
```

### Plugin File Naming
```
vaanar/
  my-plugin-name/
    my-plugin-name.php         // Main plugin file
    plugin.json                // Metadata
    includes/
      class-MyPluginMain.php
      class-MyPluginAdmin.php
    assets/
      my-plugin-name.css
      my-plugin-name.js
    languages/
      my-plugin-name-hi_IN.po
      my-plugin-name-hi_IN.mo
    readme.txt
```

### Theme File Naming
```
lanka/
  my-theme-name/
    theme.json                // Theme manifest (replaces style.css header)
    functions.php
    templates/
      index.html.php
      single.html.php
    parts/
      header.html.php
      footer.html.php
    assets/
      style.css
      script.js
```

---

## 7. Core Engine — Brahmastra

The bootstrap sequence in `ram/bootstrap/RamBootstrap.php`:

```php
<?php
// finggu.com | sudarshanpjadhav | Ramayan CMS Core Bootstrap
// Class: RamBootstrap — Brahmastra (The Ultimate Weapon / Foundation)

declare(strict_types=1);

namespace Ram\Bootstrap;

use Ram\Core\Ram;
use Ram\Core\RamConfig;
use Ram\Router\PushpakRouter;
use Ram\Hooks\AkashvaniHooks;
use Ram\Database\PatalDB;
use Ram\Cache\AmritCache;
use Ram\User\PanchjanyaSession;

final class RamBootstrap
{
    private static bool $ramVar_initialized = false;

    public static function ramFn_init(): void
    {
        if (self::$ramVar_initialized) return;
        self::$ramVar_initialized = true;

        // Step 1: Load config
        RamConfig::ramFn_load(RAM_PATH . '/ramayan-config.php');

        // Step 2: Boot database layer (Patal)
        PatalDB::ramFn_connect(RamConfig::ramFn_get('db'));

        // Step 3: Boot cache (Amrit)
        AmritCache::ramFn_init(RamConfig::ramFn_get('cache_driver', 'file'));

        // Step 4: Boot session / user (Panchjanya)
        PanchjanyaSession::ramFn_start();

        // Step 5: Load plugins (Vaanar Sena)
        self::ramFn_loadPlugins();

        // Step 6: Fire 'ram_init' hook (Akashvani)
        akashvani_do_action('ram_init');

        // Step 7: Boot router (Pushpak) and dispatch
        PushpakRouter::ramFn_dispatch();
    }

    private static function ramFn_loadPlugins(): void
    {
        $ramVar_activePlugins = ram_get_option('active_plugins', []);
        foreach ($ramVar_activePlugins as $ramVar_pluginFile) {
            $ramVar_path = RAM_CONTENT_PATH . '/' . $ramVar_pluginFile;
            if (file_exists($ramVar_path)) {
                require_once $ramVar_path;
            }
        }
        akashvani_do_action('ram_plugins_loaded');
    }
}
```

---

## 8. Hook & Event System — Akashvani

> The most powerful part of the ecosystem. Every developer interaction happens through Akashvani.

### Action Hooks (Events)
```php
<?php
// finggu.com | Akashvani Hook System
// Class: AkashvaniHooks

namespace Ram\Hooks;

class AkashvaniHooks
{
    private static array $ramVar_actions = [];
    private static array $ramVar_filters = [];

    // Register an action
    public static function ramFn_addAction(
        string $hook,
        callable $callback,
        int $priority = 10,
        int $acceptedArgs = 1
    ): void {
        self::$ramVar_actions[$hook][$priority][] = [
            'callback' => $callback,
            'accepted_args' => $acceptedArgs,
        ];
        ksort(self::$ramVar_actions[$hook]);
    }

    // Fire an action
    public static function ramFn_doAction(string $hook, mixed ...$args): void
    {
        if (empty(self::$ramVar_actions[$hook])) return;
        foreach (self::$ramVar_actions[$hook] as $ramVar_priority => $ramVar_callbacks) {
            foreach ($ramVar_callbacks as $ramVar_cb) {
                $ramVar_passArgs = array_slice($args, 0, $ramVar_cb['accepted_args']);
                call_user_func_array($ramVar_cb['callback'], $ramVar_passArgs);
            }
        }
    }

    // Register a filter
    public static function ramFn_addFilter(
        string $hook,
        callable $callback,
        int $priority = 10,
        int $acceptedArgs = 1
    ): void {
        self::$ramVar_filters[$hook][$priority][] = [
            'callback' => $callback,
            'accepted_args' => $acceptedArgs,
        ];
        ksort(self::$ramVar_filters[$hook]);
    }

    // Apply filters to a value
    public static function ramFn_applyFilter(string $hook, mixed $value, mixed ...$args): mixed
    {
        if (empty(self::$ramVar_filters[$hook])) return $value;
        foreach (self::$ramVar_filters[$hook] as $ramVar_priority => $ramVar_callbacks) {
            foreach ($ramVar_callbacks as $ramVar_cb) {
                $ramVar_passArgs = array_merge([$value], array_slice($args, 0, $ramVar_cb['accepted_args'] - 1));
                $value = call_user_func_array($ramVar_cb['callback'], $ramVar_passArgs);
            }
        }
        return $value;
    }
}
```

### Core Hook Reference Table

| Hook Name | Type | Fires When |
|---|---|---|
| `ram_init` | Action | After all plugins loaded, before routing |
| `ram_plugins_loaded` | Action | All plugins are loaded |
| `ram_setup_theme` | Action | Active theme is being set up |
| `ram_head` | Action | Inside `<head>` tag |
| `ram_footer` | Action | Before `</body>` |
| `ram_before_post_save` | Action | Before a post is saved to DB |
| `ram_after_post_save` | Action | After a post is saved |
| `ram_before_delete_post` | Action | Before a post is deleted |
| `ram_login` | Action | After user logs in |
| `ram_logout` | Action | After user logs out |
| `ram_register` | Action | After new user registers |
| `ram_api_init` | Action | REST API is initializing |
| `ram_admin_init` | Action | Admin panel is initializing |
| `ram_admin_menu` | Action | Admin menu is being built |
| `the_content` | Filter | Post content before display |
| `the_title` | Filter | Post title before display |
| `the_excerpt` | Filter | Post excerpt before display |
| `ram_get_posts_args` | Filter | Query args before running |
| `ram_posts_results` | Filter | Posts after query, before return |
| `ram_api_response` | Filter | API response before sending |
| `ram_login_redirect` | Filter | Where to redirect after login |
| `ram_allowed_html` | Filter | HTML allowed in content |
| `ram_upload_mimes` | Filter | Allowed upload MIME types |
| `ram_enqueue_scripts` | Action | Enqueue JS/CSS for frontend |
| `ram_admin_enqueue_scripts` | Action | Enqueue JS/CSS for admin |

---

## 9. Plugin System — Vaanar Sena

### Plugin Manifest (`plugin.json`)
```json
{
    "name": "My Awesome Plugin",
    "slug": "my-awesome-plugin",
    "version": "1.0.0",
    "author": "Your Name",
    "author_uri": "https://yoursite.com",
    "description": "Does something amazing",
    "requires_ram": "1.0.0",
    "requires_php": "8.1",
    "license": "GPL-2.0",
    "tags": ["seo", "forms", "ecommerce"],
    "main": "my-awesome-plugin.php"
}
```

### Plugin Main File Structure
```php
<?php
/**
 * Plugin: My Awesome Plugin
 * Slug: my-awesome-plugin
 * Version: 1.0.0
 * Author: Your Name
 *
 * finggu.com convention — Plugin bootstrap
 */

declare(strict_types=1);

// Block direct access
if (!defined('RAM_VERSION')) {
    exit('Access denied');
}

define('MY_PLUGIN_VERSION', '1.0.0');
define('MY_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MY_PLUGIN_URL', plugin_dir_url(__FILE__));

// Hook in using Akashvani
akashvani_add_action('ram_init', 'ramFn_myPlugin_init');

function ramFn_myPlugin_init(): void
{
    // Your plugin initialization here
    akashvani_add_filter('the_content', 'ramFn_myPlugin_filterContent');
    akashvani_add_action('ram_admin_menu', 'ramFn_myPlugin_addMenu');
}

function ramFn_myPlugin_filterContent(string $content): string
{
    // Transform content
    return $content;
}

function ramFn_myPlugin_addMenu(): void
{
    // Add to admin menu
    ram_add_admin_menu(
        'My Plugin',
        'my-plugin',
        'ramFn_myPlugin_adminPage',
        'dashicons-admin-generic',
        25
    );
}

function ramFn_myPlugin_adminPage(): void
{
    // Render admin page
    require_once MY_PLUGIN_PATH . 'views/admin.php';
}
```

### Plugin Activation / Deactivation Hooks
```php
// Register activation hook (runs once on install)
ram_register_activation_hook(__FILE__, 'ramFn_myPlugin_activate');

function ramFn_myPlugin_activate(): void
{
    // Create tables, set defaults, etc.
    global $ramVar_db;
    $ramVar_db->ramFn_query("CREATE TABLE IF NOT EXISTS `ram_myplugin_data` (...)");
    ram_update_option('myplugin_version', MY_PLUGIN_VERSION);
}

// Register deactivation hook
ram_register_deactivation_hook(__FILE__, 'ramFn_myPlugin_deactivate');

function ramFn_myPlugin_deactivate(): void
{
    // Cleanup if needed
}
```

---

## 10. Theme System — Lanka

### Theme Manifest (`theme.json`)
```json
{
    "name": "My Beautiful Theme",
    "slug": "my-beautiful-theme",
    "version": "1.0.0",
    "author": "Your Name",
    "description": "A stunning theme for Ramayan CMS",
    "requires_ram": "1.0.0",
    "screenshot": "screenshot.png",
    "supports": {
        "post-thumbnails": true,
        "custom-logo": true,
        "menus": ["primary", "footer"],
        "widgets": ["sidebar-1", "footer-1"],
        "editor-style": true,
        "dark-mode": true
    },
    "template_parts": {
        "header": "parts/header",
        "footer": "parts/footer",
        "sidebar": "parts/sidebar"
    }
}
```

### Theme `functions.php`
```php
<?php
// Theme: my-beautiful-theme
// finggu.com | Ramayan CMS Theme

// Add theme supports
akashvani_add_action('ram_setup_theme', 'ramFn_myTheme_setup');

function ramFn_myTheme_setup(): void
{
    ram_add_theme_support('post-thumbnails');
    ram_add_theme_support('custom-logo');

    ram_register_nav_menus([
        'primary' => 'Primary Navigation',
        'footer'  => 'Footer Navigation',
    ]);
}

// Enqueue theme assets
akashvani_add_action('ram_enqueue_scripts', 'ramFn_myTheme_assets');

function ramFn_myTheme_assets(): void
{
    ram_enqueue_style('my-theme-style', get_theme_url() . '/assets/style.css', [], '1.0.0');
    ram_enqueue_script('my-theme-script', get_theme_url() . '/assets/script.js', [], '1.0.0', true);
}
```

### Template Hierarchy (Like WordPress, but cleaner)
```
Request Type          →   Template Checked (in order)
─────────────────────────────────────────────────────────
Single Post           →   single-{post_type}-{slug}.html.php
                      →   single-{post_type}.html.php
                      →   single.html.php
                      →   index.html.php

Single Page           →   page-{slug}.html.php
                      →   page-{id}.html.php
                      →   page.html.php
                      →   index.html.php

Archive               →   archive-{post_type}.html.php
                      →   archive.html.php
                      →   index.html.php

Category              →   category-{slug}.html.php
                      →   category-{id}.html.php
                      →   category.html.php
                      →   archive.html.php
                      →   index.html.php

Search                →   search.html.php
                      →   index.html.php

404                   →   404.html.php
                      →   index.html.php

Home / Blog           →   home.html.php
                      →   index.html.php
```

---

## 11. Template Engine — Chitrakoot

Ramayan uses `.html.php` files — no proprietary syntax, just clean PHP in HTML:

```php
<!-- Template: single.html.php -->
<?php ram_get_template_part('parts/header'); ?>

<main class="ram-main">
    <?php if (ram_have_posts()): ?>
        <?php while (ram_have_posts()): ram_the_post(); ?>
            <article class="ram-post ram-post--single">
                <h1 class="ram-post__title"><?php ram_the_title(); ?></h1>
                <div class="ram-post__meta">
                    <?php ram_the_date(); ?> by <?php ram_the_author(); ?>
                </div>
                <div class="ram-post__content">
                    <?php ram_the_content(); ?>
                </div>
                <div class="ram-post__tags">
                    <?php ram_the_tags(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    <?php endif; ?>
</main>

<?php ram_get_template_part('parts/footer'); ?>
```

### Shortcode System (Built-in)
```php
// Register shortcode: [ram_button text="Click me" url="/contact"]
ram_add_shortcode('ram_button', function(array $atts): string {
    $ramVar_atts = ram_shortcode_atts([
        'text' => 'Click',
        'url'  => '#',
        'class' => '',
    ], $atts);
    return '<a href="' . ram_esc_url($ramVar_atts['url']) . '" 
               class="ram-btn ' . ram_esc_attr($ramVar_atts['class']) . '">'
           . ram_esc_html($ramVar_atts['text']) . '</a>';
});
```

---

## 12. Router — Pushpak

```php
<?php
// finggu.com | Pushpak Router — URL to Controller mapping
// Class: PushpakRouter

namespace Ram\Router;

class PushpakRouter
{
    private static array $ramVar_routes = [];

    public static function ramFn_get(string $pattern, callable|array $handler): void
    {
        self::$ramVar_routes[] = [
            'method'  => 'GET',
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public static function ramFn_post(string $pattern, callable|array $handler): void
    {
        self::$ramVar_routes[] = [
            'method'  => 'POST',
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public static function ramFn_dispatch(): void
    {
        $ramVar_uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $ramVar_method = $_SERVER['REQUEST_METHOD'];

        foreach (self::$ramVar_routes as $ramVar_route) {
            if ($ramVar_route['method'] !== $ramVar_method) continue;
            
            $ramVar_pattern = '#^' . preg_replace('/:([a-z]+)/', '(?P<$1>[^/]+)', $ramVar_route['pattern']) . '$#';
            if (preg_match($ramVar_pattern, $ramVar_uri, $ramVar_matches)) {
                $ramVar_params = array_filter($ramVar_matches, 'is_string', ARRAY_FILTER_USE_KEY);
                call_user_func($ramVar_route['handler'], $ramVar_params);
                return;
            }
        }

        // No route matched — fallback to CMS content routing
        self::ramFn_dispatchContent($ramVar_uri);
    }

    private static function ramFn_dispatchContent(string $uri): void
    {
        // Resolve URI to post/page/archive via Patal DB
        // Then load appropriate template via Chitrakoot
    }
}
```

---

## 13. ORM / Database Layer — Patal

```php
<?php
// finggu.com | Patal ORM — Database Abstraction
// Class: PatalDB

// Query builder usage example:
$ramVar_posts = PatalDB::ramFn_table('posts')
    ->ramFn_select(['id', 'title', 'slug', 'status'])
    ->ramFn_where('status', '=', 'publish')
    ->ramFn_where('post_type', '=', 'post')
    ->ramFn_orderBy('created_at', 'DESC')
    ->ramFn_limit(10)
    ->ramFn_offset(0)
    ->ramFn_get();

// Model usage:
$ramVar_post = SitaPost::ramFn_find(42);
$ramVar_post->title = 'Updated Title';
$ramVar_post->ramFn_save();

// Raw queries (always use prepared statements):
$ramVar_results = PatalDB::ramFn_query(
    "SELECT * FROM `ram_posts` WHERE `status` = ? AND `post_type` = ?",
    ['publish', 'post']
);
```

---

## 14. Admin Panel — Ayodhya

The admin panel is built in **vanilla JS + PHP** — no jQuery, no React, no Vue. Loads fast on any server.

### Admin Menu Registration
```php
// In plugin or theme functions.php:
akashvani_add_action('ram_admin_menu', function(): void {
    
    // Add top-level menu
    ram_add_admin_menu(
        page_title: 'My Plugin Settings',
        menu_title: 'My Plugin',
        capability: 'manage_options',
        menu_slug:  'my-plugin',
        callback:   'ramFn_myPlugin_settingsPage',
        icon:       'dashicons-admin-tools',
        position:   30
    );

    // Add submenu
    ram_add_admin_submenu(
        parent_slug: 'my-plugin',
        page_title:  'Advanced Settings',
        menu_title:  'Advanced',
        capability:  'manage_options',
        menu_slug:   'my-plugin-advanced',
        callback:    'ramFn_myPlugin_advancedPage'
    );
});
```

### Admin Settings API
```php
// Register settings
ram_register_setting('my_plugin_group', 'my_plugin_option');

// Add settings section
ram_add_settings_section(
    id:       'my_plugin_main',
    title:    'Main Settings',
    callback: 'ramFn_myPlugin_sectionHeader',
    page:     'my-plugin'
);

// Add settings field
ram_add_settings_field(
    id:       'my_api_key',
    title:    'API Key',
    callback: 'ramFn_myPlugin_apiKeyField',
    page:     'my-plugin',
    section:  'my_plugin_main'
);
```

---

## 15. User & Role System — Panchjanya

```php
// Default Roles
RAM_ROLE_SUPER_ADMIN  // God mode — multi-site management
RAM_ROLE_ADMIN        // Full site control
RAM_ROLE_EDITOR       // Manage all content, no settings
RAM_ROLE_AUTHOR       // Publish own posts
RAM_ROLE_CONTRIBUTOR  // Write posts, cannot publish
RAM_ROLE_SUBSCRIBER   // Read-only registered user

// Custom Role Creation
ram_add_role(
    slug:         'shop_manager',
    display_name: 'Shop Manager',
    capabilities: [
        'read'         => true,
        'manage_shop'  => true,
        'edit_orders'  => true,
    ]
);

// Capability Checks
if (ram_current_user_can('edit_posts')) {
    // Show edit button
}

// Get/update user meta
$ramVar_phone = ram_get_user_meta($userId, 'phone', true);
ram_update_user_meta($userId, 'phone', '9876543210');
```

---

## 16. REST API Layer — Brahmaand

All API routes are under `/api/v1/`:

```
GET    /api/v1/posts              List posts
GET    /api/v1/posts/{id}         Single post
POST   /api/v1/posts              Create post (auth required)
PUT    /api/v1/posts/{id}         Update post (auth required)
DELETE /api/v1/posts/{id}         Delete post (auth required)

GET    /api/v1/pages
GET    /api/v1/media
GET    /api/v1/users/me           Current user (auth required)

POST   /api/v1/auth/login         Get JWT token
POST   /api/v1/auth/refresh       Refresh JWT token
```

### Register Custom API Endpoint
```php
akashvani_add_action('ram_api_init', function(): void {
    
    ram_register_api_route(
        namespace: 'my-plugin/v1',
        route:     '/products',
        method:    'GET',
        callback:  'ramFn_myPlugin_getProducts',
        permission_callback: '__return_true'  // Public endpoint
    );

    ram_register_api_route(
        namespace: 'my-plugin/v1',
        route:     '/products',
        method:    'POST',
        callback:  'ramFn_myPlugin_createProduct',
        permission_callback: fn() => ram_current_user_can('manage_products')
    );
});

function ramFn_myPlugin_getProducts(RamRequest $request): RamResponse
{
    $ramVar_products = PatalDB::ramFn_table('products')->ramFn_get();
    return new RamResponse($ramVar_products, 200);
}
```

---

## 17. File / Media Manager — Nandan

```php
// Upload a file
$ramVar_attachment = NandanUploader::ramFn_handleUpload(
    file:    $_FILES['my_file'],
    allowed: ['image/jpeg', 'image/png', 'image/webp', 'application/pdf']
);

// Get attachment URL
$ramVar_url = ram_get_attachment_url($attachmentId);

// Get image with size
$ramVar_img = ram_get_image($attachmentId, 'thumbnail');  // 150x150
$ramVar_img = ram_get_image($attachmentId, 'medium');     // 300x300
$ramVar_img = ram_get_image($attachmentId, 'large');      // 1024x1024
$ramVar_img = ram_get_image($attachmentId, 'full');       // Original

// Register custom image size
ram_add_image_size('hero-banner', 1920, 600, true);  // true = crop
```

---

## 18. SEO & Schema Layer — Saraswati

No plugin needed for basic SEO — it's built into the core.

```php
// Automatic head output — add to <head>:
// Saraswati outputs all of this automatically
akashvani_add_action('ram_head', function(): void {
    SaraswatiSEO::ramFn_renderMeta();       // title, description, canonical
    SaraswatiOpenGraph::ramFn_render();      // og:title, og:image, og:type
    SaraswatiSchema::ramFn_render();         // JSON-LD schema
});

// Custom Schema for a plugin (e.g. a recipe plugin)
akashvani_add_filter('ram_schema_data', function(array $schema): array {
    if (is_single() && get_post_type() === 'recipe') {
        $schema['@type']       = 'Recipe';
        $schema['name']        = ram_get_the_title();
        $schema['cookTime']    = 'PT30M';
        // etc.
    }
    return $schema;
});
```

---

## 19. Cache System — Amrit

```php
// Set cache
AmritCache::ramFn_set('popular_posts', $ramVar_posts, 3600);  // TTL: 1 hour

// Get cache
$ramVar_cached = AmritCache::ramFn_get('popular_posts');
if ($ramVar_cached !== false) {
    return $ramVar_cached;
}

// Delete cache
AmritCache::ramFn_delete('popular_posts');

// Delete all cache
AmritCache::ramFn_flush();

// Cache groups (for selective flushing)
AmritCache::ramFn_set('post_42', $ramVar_post, 3600, 'posts');
AmritCache::ramFn_deleteGroup('posts');  // Flush all post cache

// Drivers (auto-selected based on config)
// file  — /cache/ directory (default, works everywhere)
// mysql — ram_cache table (good for clustered hosting)
// redis — Redis server (optional, blazing fast)
```

---

## 20. Marketplace — Kishkindha

The official plugin/theme directory at **kishkindha.ramayancms.com**

### Submitting to Kishkindha (For Contributors)
```
1. Create plugin/theme with correct structure
2. Run: php hanuman validate-plugin ./vaanar/my-plugin
3. Add plugin.json with all required fields
4. Push to GitHub (public repo)
5. Submit at kishkindha.ramayancms.com/submit
6. Review bot checks:
   - Naming conventions followed?
   - No eval() or base64_decode() of user input?
   - SQL injection safe (all queries parameterized)?
   - XSS safe (all output escaped)?
   - License declared?
7. Human review for featured listing
8. Auto-update via Kishkindha API integrated into admin
```

### Auto-Update System in Admin
```php
// Plugins and themes update automatically from Kishkindha
// Just like WordPress.org — one-click updates in Ayodhya admin panel
// KishkindhaUpdater checks for updates on admin_init via cron
```

---

## 21. CLI Tool — Hanuman

> Hanuman is a pure PHP CLI. No Node, no Composer needed. Just `php hanuman <command>`.

```bash
# Install / Setup
php hanuman install

# Create a new plugin scaffold
php hanuman make:plugin my-awesome-plugin

# Create a new theme scaffold
php hanuman make:theme my-beautiful-theme

# Create a new custom post type
php hanuman make:cpt products

# Run database migrations
php hanuman migrate

# Run database seeder
php hanuman db:seed

# Create admin user
php hanuman create:admin

# Clear all cache
php hanuman cache:clear

# Clear specific cache group
php hanuman cache:clear --group=posts

# List all installed plugins
php hanuman plugins:list

# Activate a plugin
php hanuman plugins:activate my-awesome-plugin

# Deactivate a plugin
php hanuman plugins:deactivate my-awesome-plugin

# Validate plugin before submitting to Kishkindha
php hanuman validate:plugin ./vaanar/my-awesome-plugin

# Export site as ZIP (backup)
php hanuman export --output=./backup.zip

# Import backup
php hanuman import ./backup.zip

# Run all tests
php hanuman test

# Check system requirements
php hanuman doctor
```

---

## 22. Installer — Swayamvar

Installation is identical to WordPress:

```
1. Download ramayan-cms.zip
2. Extract to public_html (or subdirectory) via cPanel File Manager
3. Create MySQL database + user in cPanel
4. Visit yoursite.com — Swayamvar runs automatically
5. Fill 5-field form:
   - Database Name
   - Database User
   - Database Password
   - Database Host (usually: localhost)
   - Site Name
6. Click "Begin the Swayamvar" → Done in 30 seconds
```

### What Swayamvar Does Automatically:
- Creates `ramayan-config.php` from `ramayan-config-sample.php`
- Runs DB migrations (creates all `ram_` tables)
- Creates admin user
- Installs default theme (`ram-default`)
- Sets `.htaccess` rules
- Seeds default content (sample post, sample page, about page)
- Redirects to `yoursite.com/ayodhya/` admin panel

---

## 23. Security Layer — Sudarshana

```php
// CSRF Protection — auto applied to all admin forms
$ramVar_nonce = SudarshanaNonce::ramFn_create('save_post_42');
// In form: <input type="hidden" name="_ram_nonce" value="<?= $ramVar_nonce ?>">
// Verify: SudarshanaNonce::ramFn_verify($_POST['_ram_nonce'], 'save_post_42');

// XSS Prevention — output escaping helpers
ram_esc_html($value)          // For text content
ram_esc_attr($value)          // For HTML attribute values
ram_esc_url($value)           // For URLs
ram_esc_js($value)            // For inline JavaScript
ram_kses($html, $allowed_tags) // Allowed HTML whitelist

// SQL Injection — NEVER write raw SQL. Use PatalDB
PatalDB::ramFn_table('posts')->ramFn_where('id', '=', (int) $_GET['id'])->ramFn_first();

// Rate Limiting (built-in for API and login)
SudarshanaFirewall::ramFn_rateLimit('login_attempt', ip: $_SERVER['REMOTE_ADDR'], max: 5, window: 300);

// File Upload Validation
SudarshanaFirewall::ramFn_validateUpload($_FILES['file'], allowed_types: ['image/jpeg', 'image/png']);
```

---

## 24. Multilingual & i18n — Valmiki

```php
// Translation functions (like WP's __ and _e)
__r('Hello World', 'my-plugin-slug')         // Return translated string
_re('Hello World', 'my-plugin-slug')         // Echo translated string
_nr('One item', '%d items', $count, 'my-plugin-slug')  // Plural
_xr('Post', 'noun, not verb', 'my-plugin-slug')        // Context translation

// Load translations
ValmikiLoader::ramFn_loadPluginTextdomain(
    domain: 'my-plugin-slug',
    path:   MY_PLUGIN_PATH . '/languages'
);

// .po / .mo file naming: {domain}-{locale}.po
// Example: my-plugin-slug-hi_IN.po  (Hindi India)
//          my-plugin-slug-mr_IN.po  (Marathi India)
//          my-plugin-slug-gu_IN.po  (Gujarati India)
```

---

## 25. Build Phases & Milestones

### Phase 1 — Ayodhya Foundation (Months 1–2)
- [ ] RamBootstrap (Brahmastra)
- [ ] PushpakRouter (basic routing)
- [ ] PatalDB (query builder + basic ORM)
- [ ] AkashvaniHooks (action + filter system)
- [ ] PanchjanyaUser (auth, sessions, roles)
- [ ] SitaPost + SitaPage models
- [ ] Swayamvar installer
- [ ] Ramayan-Config system
- [ ] Basic Ayodhya admin (dashboard, post CRUD)
- [ ] Default theme (`ram-default`)
- [ ] AmritFileCache

**Milestone:** Functional CMS — create post, publish, view on frontend ✅

### Phase 2 — Lanka & Vaanar (Months 3–4)
- [ ] Full theme system (Lanka) with template hierarchy
- [ ] Full plugin system (Vaanar Sena) with activate/deactivate
- [ ] Shortcode system
- [ ] Widget system
- [ ] Menu system
- [ ] NandanLibrary (media upload, image sizes)
- [ ] Settings API
- [ ] Custom Post Types + Custom Taxonomies API
- [ ] Custom Fields / Meta API

**Milestone:** Plugin and theme ecosystem functional ✅

### Phase 3 — Brahmaand & Saraswati (Months 5–6)
- [ ] Full REST API (BrahmaandAPI)
- [ ] JWT Authentication
- [ ] API key management
- [ ] SaraswatiSEO (meta, OG, JSON-LD)
- [ ] SaraswatiSitemap (XML sitemap auto-generation)
- [ ] ValmikiTranslator (i18n full support)
- [ ] SudarshanaFirewall (rate limiting, WAF rules)
- [ ] AmritRedisCache adapter
- [ ] Hanuman CLI (basic commands)

**Milestone:** Production-ready for live sites ✅

### Phase 4 — Kishkindha & Ecosystem (Months 7–9)
- [ ] Kishkindha marketplace backend
- [ ] Kishkindha submission portal
- [ ] Auto-update system in admin
- [ ] Full Hanuman CLI (all commands)
- [ ] Documentation site (docs.ramayancms.com)
- [ ] Contributor guidelines + validation bot
- [ ] Official starter plugins:
  - RamForms (contact forms)
  - RamShop (e-commerce)
  - RamSEO (advanced SEO)
  - RamGallery (media gallery)
  - RamMail (SMTP + transactional email)
- [ ] Official starter themes (3 themes)

**Milestone:** Ecosystem launch, public beta ✅

### Phase 5 — Enterprise & Scale (Month 10+)
- [ ] Multisite support
- [ ] Headless mode (full decoupled API)
- [ ] Block editor (like Gutenberg but better)
- [ ] Page builder integration
- [ ] CDN integration
- [ ] Advanced caching (full-page cache)
- [ ] Webhooks system
- [ ] CLI plugin generation from AI

---

## 26. Comparison: Ramayan vs WordPress

| Feature | WordPress | Ramayan CMS |
|---|---|---|
| **PHP Requirement** | 7.4+ | 8.1+ (modern features) |
| **Admin UI** | jQuery-heavy | Vanilla JS only |
| **Hook System** | Global arrays | Typed, prioritized, grouped |
| **Template Engine** | `.php` files | `.html.php` (cleaner separation) |
| **REST API** | Bolted on (v4.7+) | First-class citizen |
| **ORM** | `$wpdb` (raw SQL) | Full ORM + Query Builder |
| **Cache** | Need plugin | Built into core |
| **SEO** | Need plugin (Yoast) | Built into core |
| **i18n** | Built in (good) | Built in (better, modern) |
| **Security** | Plugin-dependent | Core firewall built in |
| **Schema JSON-LD** | Need plugin | Built into core |
| **Auto-updates** | Good | Better (via Kishkindha) |
| **CLI** | WP-CLI (separate install) | Hanuman (pure PHP, built-in) |
| **Multisite** | Yes (complex) | Phase 5 roadmap |
| **Block Editor** | Gutenberg | Phase 5 (custom, lighter) |
| **Learning Curve** | Low | Same or lower |
| **cPanel Install** | 5 minutes | 5 minutes |
| **Plugin Ecosystem** | 60,000+ plugins | Starting — Kishkindha |
| **Theme Ecosystem** | Thousands | Starting — Kishkindha |
| **Legacy Baggage** | 20 years | Zero |
| **Code Quality** | Mixed (old+new) | Modern PHP 8.1+ only |

---

## 27. Contribution Guidelines

### For Plugin Developers
1. Fork `github.com/sudarshanpjadhav/ramayan-cms`
2. Use `php hanuman make:plugin my-plugin` to scaffold
3. Follow Ramayan naming conventions strictly
4. All functions prefixed `ramFn_` or your own namespace
5. All DB queries must use `PatalDB` (no raw `mysqli_query`)
6. All output must be escaped through Sudarshana helpers
7. Submit to Kishkindha marketplace after testing

### For Theme Developers
1. Use `php hanuman make:theme my-theme` to scaffold
2. Always include `theme.json` with complete metadata
3. Use `ram_*` template functions, never direct DB calls
4. Support at least: `post-thumbnails`, `menus`, `widgets`
5. Test with default content (posts, pages, media)

### For Core Contributors
1. Read `CONTRIBUTING.md` in the repo
2. All PRs require: PHPUnit tests + PHPStan level 8 pass
3. Backward compatibility breaking changes need RFC process
4. Use `ram/` namespace for all core classes
5. Document every public method with PHPDoc

---

## 28. Finggu Branding Rules

All code in this ecosystem carries the Finggu brand identity:

```php
// File header comment — REQUIRED in every file
/**
 * Ramayan CMS — [Component Name]
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */
```

```
CSS Class Prefixes:   ram-*, ayodhya-*, lanka-*
PHP Class Names:      PascalCase with Ramayan character names
PHP Functions:        ramFn_* or ram_* (global helpers)
PHP Variables:        $ramVar_* (local) or $ram_* (global)
PHP Constants:        RAM_* (screaming snake case)
DB Tables:            ram_* prefix
JS Functions:         ramFn_* (admin JS)
JS Variables:         ramVar_* (admin JS)
API Routes:           /api/v1/* (Brahmaand)
GitHub:               github.com/sudarshanpjadhav/ramayan-cms
npm (if ever needed): @finggujadhav/ramayan-*
```

---

## 🏹 Final Words

> *"Just as Ram built the Setu bridge across the ocean — stone by stone, with an army of contributors — Ramayan CMS will be built the same way. Every plugin a stone. Every theme a warrior. Every developer a member of the Vaanar Sena."*

**Start coding. Start with Phase 1. Ship it.**

---

*Blueprint Version: 1.0.0 | Date: 2026 | Author: sudarshanpjadhav | Brand: finggu.com*
*License: GPL-2.0 (same as WordPress — maximum ecosystem compatibility)*
