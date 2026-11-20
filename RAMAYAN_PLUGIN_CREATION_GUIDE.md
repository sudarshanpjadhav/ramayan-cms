# RAMAYAN CMS — Plugin Creation Guide

Version: 1.0  
Audience: PHP developers, agencies, and plugin authors  
Scope: Creating, testing, activating, packaging, and distributing plugins for Ramayan CMS

---

## 1. What a Ramayan Plugin Is

A Ramayan CMS plugin is a directory under `vaanar/`. Plugins extend CMS behavior without changing core files.

Plugins can:

- Add dashboard widgets
- Add post-editor meta boxes
- Add admin pages
- Add API endpoints
- Modify content with filters
- Run code on lifecycle events
- Add shortcodes or custom helpers
- Integrate external services
- Register custom database tables
- Add scheduled or event-driven behavior

Plugins are managed by **VaanarPlugins**.

---

## 2. Requirements

- PHP 8.1 or newer
- A working Ramayan CMS installation
- Basic PHP, HTML, CSS, JavaScript, and SQL knowledge
- No direct jQuery dependency
- No modification of core RAMAYAN CMS files
- Database access through PatalDB or published Ramayan helpers

---

## 3. Plugin Directory Structure

Each plugin directory must use the same slug as its main PHP file.

```text
vaanar/
└── my-first-plugin/
    ├── my-first-plugin.php
    ├── assets/
    │   ├── admin.css
    │   └── admin.js
    ├── includes/
    │   └── helpers.php
    └── README.md
```

The plugin manager requires this entry file:

```text
vaanar/my-first-plugin/my-first-plugin.php
```

Rules:

- Use lowercase kebab-case
- Use the same slug for the folder and entry file
- Do not place plugin source outside `vaanar/`
- Do not include files outside the plugin directory
- Keep all assets inside the plugin directory

---

## 4. Plugin Header

Create `vaanar/my-first-plugin/my-first-plugin.php`.

```php
<?php
/**
 * Plugin Name: My First Plugin
 * Slug: my-first-plugin
 * Version: 1.0.0
 * Author: Your Name
 * Plugin URI: https://example.com/my-first-plugin
 * Description: Demonstrates a complete Ramayan CMS plugin.
 */

declare(strict_types=1);

if (!defined('RAM_PATH')) {
    exit;
}

/**
 * Plugin code begins here.
 */
```

The plugin manager parses these headers:

| Header | Purpose |
|---|---|
| `Plugin Name` | Display name in Ayodhya |
| `Slug` | Informational slug |
| `Version` | Displayed version |
| `Author` | Plugin author |
| `Plugin URI` | Optional project URL |
| `Description` | Plugin description |

For discovery, the plugin folder and entry file names must match exactly.

---

## 5. Plugin Naming Rules

Use unique prefixes.

| Item | Convention | Example |
|---|---|---|
| Directory | lowercase kebab-case | `my-first-plugin` |
| Main file | directory name plus `.php` | `my-first-plugin.php` |
| PHP function | `ramFn_` plus plugin prefix | `ramFn_myFirstPlugin_render` |
| PHP class | PascalCase plus unique prefix | `MyFirstPluginSettings` |
| Option key | plugin-prefixed snake_case | `my_first_plugin_enabled` |
| Meta key | plugin-prefixed snake_case | `_my_first_plugin_label` |
| Database table | `ram_{slug}_{table}` | `ram_my_first_plugin_items` |
| JavaScript function | `ramFn_` | `ramFn_myFirstPlugin_save` |
| JavaScript variable | `ramVar_` | `ramVar_myFirstPluginNonce` |

Do not use generic function names such as `render()`, `save()`, or `init()` unless they are inside a namespaced class.

---

## 6. Activation and Deactivation

Use the lifecycle helpers:

```php
ram_register_activation_hook(__FILE__, 'ramFn_myFirstPlugin_activate');
ram_register_deactivation_hook(__FILE__, 'ramFn_myFirstPlugin_deactivate');

function ramFn_myFirstPlugin_activate(): void
{
    ram_update_option('my_first_plugin_enabled', true);

    PatalDB::ramFn_table('options')->ramFn_insert([
        'option_name' => 'my_first_plugin_version',
        'option_value' => '1.0.0',
        'autoload' => 'yes',
    ]);
}

function ramFn_myFirstPlugin_deactivate(): void
{
    ram_update_option('my_first_plugin_enabled', false);
}
```

The activation and deactivation hooks are converted into:

```text
ram_activate_my-first-plugin
ram_deactivate_my-first-plugin
```

Activation behavior:

1. Ramayan loads the plugin entry file.
2. The plugin registers its activation callback.
3. Vaanar fires `ram_activate_<slug>`.
4. Vaanar adds the plugin slug to `active_plugins`.

Deactivation behavior:

1. Vaanar fires `ram_deactivate_<slug>`.
2. Vaanar removes the plugin slug from `active_plugins`.

Do not perform database migration work on every page load. Perform migrations in the activation callback.

If activation or database setup fails, throw or return an explicit failure and log through the Ramayan error system. Do not silently print success.

---

## 7. Plugin Options

Use Ramayan option helpers.

```php
$ramVar_enabled = ram_get_option('my_first_plugin_enabled', false);
$ramVar_settings = ram_get_option(
    'my_first_plugin_settings',
    [
        'heading' => 'My Plugin',
        'limit' => 5,
    ]
);
```

Update options:

```php
ram_update_option('my_first_plugin_enabled', true);
ram_update_option('my_first_plugin_settings', [
    'heading' => 'Vaanar Updates',
    'limit' => 5,
]);
```

For arrays and objects, store JSON through the option system only if `ram_update_option()` and the database schema support the expected value.

Never store:

- Database passwords
- API secrets
- Private tokens
- Encryption keys
- Personal credentials

Settings visible to users must be escaped before output.

---

## 8. Hook Registration

Use Akashvani actions and filters.

### Action

```php
ram_action('ram_head', 'ramFn_myFirstPlugin_head');

function ramFn_myFirstPlugin_head(): void
{
    echo '<meta name="generator" content="My First Plugin 1.0">';
}
```

### Filter

```php
ram_filter('ram_permalink', 'ramFn_myFirstPlugin_permalink');

function ramFn_myFirstPlugin_permalink(string $ramVar_url, array $ramVar_post): string
{
    return ram_esc_url($ramVar_url);
}
```

Aliases are available:

```php
akashvani_add_action(...)
akashvani_add_filter(...)
akashvani_do_action(...)
ram_action(...)
ram_filter(...)
ram_do_action(...)
```

Use one consistent style in a plugin.

### Common Hooks

| Hook | Purpose |
|---|---|
| `ram_head` | Output inside the public `<head>` |
| `ram_footer` | Output before public `</body>` |
| `ram_admin_init` | Register admin features |
| `ram_admin_dashboard_bottom` | Add dashboard widgets |
| `ram_save_post` | Run after a post is saved |
| `ram_render_home` | React to home rendering |
| `ram_render_single` | React to single-post rendering |
| `ram_render_page` | React to page rendering |
| `ram_render_404` | React to 404 rendering |
| `ram_plugins_loaded` | Run after active plugins load |
| `ram_before_render` | Run before a template loads |
| `ram_after_render` | Run after a template loads |

The hooks implemented by the core should be checked before a plugin depends on them.

---

## 9. Add an Ayodhya Dashboard Widget

```php
ram_action('ram_admin_dashboard_bottom', 'ramFn_myFirstPlugin_dashboardWidget');

function ramFn_myFirstPlugin_dashboardWidget(): void
{
    $ramVar_postCount = PatalDB::ramFn_table('posts')
        ->ramFn_where('status', '=', 'publish')
        ->ramFn_count();
    ?>
    <div class="ram-card" style="margin-top:1.5rem;">
        <h3>My First Plugin</h3>
        <p><?= (int) $ramVar_postCount ?> published chronicles</p>
    </div>
    <?php
}
```

Use Ayodhya CSS variables and classes:

- `.ram-card`
- `.ayodhya-table`
- `.ayodhya-page-header`
- `.btn`
- `.btn-primary`
- `--ram-saffron`
- `--ram-gold`
- `--ram-text`
- `--ram-muted`
- `--ram-border`

Use [RAMAYAN_VISIBLE_NAMING.md](RAMAYAN_VISIBLE_NAMING.md) for user-facing labels.

---

## 10. Add a Meta Box

Meta boxes can be attached to the post editor.

```php
ram_action('ram_admin_init', 'ramFn_myFirstPlugin_registerMetaBox');

function ramFn_myFirstPlugin_registerMetaBox(): void
{
    ram_add_meta_box(
        'my_first_plugin_options',
        'My Plugin Options',
        'ramFn_myFirstPlugin_renderMetaBox',
        'sidebar'
    );
}
```

Render the fields:

```php
function ramFn_myFirstPlugin_renderMetaBox(array $ramVar_post): void
{
    $ramVar_postId = (int) ($ramVar_post['id'] ?? 0);
    $ramVar_label = ram_get_post_meta(
        $ramVar_postId,
        '_my_first_plugin_label',
        true
    );
    ?>
    <div class="mb-2">
        <label class="ram-label" for="my_first_plugin_label">Custom Label</label>
        <input
            type="text"
            id="my_first_plugin_label"
            name="my_first_plugin_label"
            class="ram-input"
            value="<?= ram_esc_attr((string) $ramVar_label) ?>"
        >
    </div>
    <?php
}
```

Save the fields:

```php
ram_action('ram_save_post', 'ramFn_myFirstPlugin_saveMetaBox');

function ramFn_myFirstPlugin_saveMetaBox(int $ramVar_postId): void
{
    if (!ram_verify_nonce(
        (string) ($_POST['_ram_nonce'] ?? ''),
        'save_post'
    )) {
        return;
    }

    $ramVar_label = trim((string) ($_POST['my_first_plugin_label'] ?? ''));
    ram_update_post_meta(
        $ramVar_postId,
        '_my_first_plugin_label',
        ram_sanitize_slug($ramVar_label)
    );
}
```

Meta boxes are stored through post meta, not in core post columns.

---

## 11. Add an Admin Page

An admin page should use the existing Ayodhya `page=<slug>` route or a plugin hook.

Example plugin:

```php
ram_action('ram_admin_init', 'ramFn_myFirstPlugin_adminPage');

function ramFn_myFirstPlugin_adminPage(): void
{
    // Route registration or menu integration can be attached here.
}
```

If adding a custom page to `ayodhya/index.php`, update the switch dispatcher carefully and preserve the existing route contract. Third-party plugins should prefer hooks and existing plugin-page APIs instead of editing core admin files.

The plugin admin view should:

- Verify login
- Verify capabilities
- Validate nonces
- Escape output
- Use PatalDB for database access
- Return a clear success or error message

---

## 12. Database Access

Use PatalDB.

### Query

```php
$ramVar_items = PatalDB::ramFn_table('my_first_plugin_items')
    ->ramFn_where('status', '=', 'active')
    ->ramFn_orderBy('created_at', 'DESC')
    ->ramFn_limit(20)
    ->ramFn_get();
```

### Insert

```php
$ramVar_id = PatalDB::ramFn_table('my_first_plugin_items')
    ->ramFn_insert([
        'title' => $ramVar_title,
        'status' => 'active',
    ]);
```

### Update

```php
PatalDB::ramFn_table('my_first_plugin_items')
    ->ramFn_where('id', '=', $ramVar_id)
    ->ramFn_update([
        'status' => 'archived',
    ]);
```

### Prepared Raw Query

Use prepared bindings for custom SQL:

```php
$ramVar_rows = PatalDB::ramFn_selectRaw(
    'SELECT * FROM `ram_my_first_plugin_items` WHERE status = ?',
    ['active']
);
```

Use table names with the plugin prefix:

```text
ram_my_first_plugin_items
```

Do not use concatenated SQL values.

---

## 13. Creating Plugin Tables

Create custom tables only during activation.

```php
ram_register_activation_hook(__FILE__, 'ramFn_myFirstPlugin_activate');

function ramFn_myFirstPlugin_activate(): void
{
    $ramVar_pdo = PatalDB::ramFn_getPdo();
    $ramVar_prefix = PatalDB::ramFn_getPrefix();

    $ramVar_pdo->exec("
        CREATE TABLE IF NOT EXISTS `{$ramVar_prefix}my_first_plugin_items` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `title` VARCHAR(255) NOT NULL,
            `status` VARCHAR(20) NOT NULL DEFAULT 'active',
            `created_at` DATETIME NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}
```

Table names should use the configured Patal prefix. Do not hardcode `ram_` in SQL.

---

## 14. Security

### Input

Validate and sanitize input.

```php
$ramVar_title = trim((string) ($_POST['title'] ?? ''));
$ramVar_title = mb_substr($ramVar_title, 0, 255);
```

Never pass raw input to SQL or HTML.

### Output

| Data | Helper |
|---|---|
| Text | `ram_esc_html()` |
| Attribute | `ram_esc_attr()` |
| URL | `ram_esc_url()` |
| JavaScript | `ram_esc_js()` |
| Rich HTML | `ram_kses()` |
| Email | `ram_sanitize_email()` |

### Nonces

Create a hidden nonce:

```php
<?= ram_nonce_field('my_first_plugin_save') ?>
```

Verify it:

```php
$ramVar_nonce = (string) ($_POST['_ram_nonce'] ?? '');

if (!ram_verify_nonce($ramVar_nonce, 'my_first_plugin_save')) {
    ram_send_json_error('Security check failed', 403);
}
```

### Capability Checks

Use the session and current-user helpers:

```php
if (!ram_is_logged_in() || !ram_current_user_can('manage_options')) {
    ram_send_json_error('Permission denied', 403);
}
```

Plugin endpoints must verify both authentication and authorization.

---

## 15. Asset Enqueuing

Register plugin assets through LankaThemes or output them only where required.

```php
use Ram\Themes\LankaThemes;

LankaThemes::ramFn_enqueueStyle(
    'my-first-plugin-admin',
    'vaanar/my-first-plugin/assets/admin.css',
    [],
    '1.0.0'
);

LankaThemes::ramFn_enqueueScript(
    'my-first-plugin-admin',
    'vaanar/my-first-plugin/assets/admin.js',
    [],
    '1.0.0',
    true
);
```

Only load admin assets in the Ayodhya context.

JavaScript must use:

- Vanilla JavaScript
- Fetch API
- `ramVar_` variables
- `ramFn_` functions
- No jQuery

---

## 16. Admin API Requests

For JSON API requests:

```php
async function ramFn_myFirstPlugin_submit() {
    const ramVar_response = await fetch('/api/v1/my-first-plugin/save', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            nonce: window.ramVar_nonce,
            title: document.querySelector('#my-plugin-title').value
        })
    });

    const ramVar_json = await ramVar_response.json();
    if (!ramVar_json.success) {
        throw new Error(ramVar_json.message || 'Request failed');
    }
}
```

The server must validate the nonce and sanitize the title.

Do not expose secrets or internal database values in API responses.

---

## 17. Plugin API Hooks

Plugins can expose settings and events through filters.

```php
$ramVar_value = ram_apply_filters(
    'my_first_plugin_label',
    'Default',
    $ramVar_postId
);
```

Filter callback:

```php
ram_filter('my_first_plugin_label', 'ramFn_myFirstPlugin_changeLabel');

function ramFn_myFirstPlugin_changeLabel(string $ramVar_value, int $ramVar_postId): string
{
    return ram_esc_html($ramVar_value);
}
```

Document every public hook, filter, option, and data shape that your plugin provides.

---

## 18. Plugin Filesystem Safety

Only read or write within approved paths.

Use:

- `RAM_CONTENT_PATH` for plugins
- `RAM_THEME_PATH` for themes
- `RAM_UPLOAD_PATH` for uploaded files
- `RAM_CACHE_PATH` for cache
- `RAM_LOG_PATH` for logs

```php
$ramVar_pluginFile = RAM_CONTENT_PATH
    . '/my-first-plugin'
    . '/config.json';
```

Never allow untrusted paths to escape the plugin directory.

Never include files based directly on `$_GET`, `$_POST`, or request URLs.

---

## 19. Packaging

Package the plugin folder as a ZIP.

```text
my-first-plugin.zip
└── my-first-plugin/
    ├── my-first-plugin.php
    ├── README.md
    ├── assets/
    └── includes/
```

Do not include:

- `.git/`
- `.env`
- `ramayan-config.php`
- Uploads
- Database dumps
- Cache files
- Logs
- Node modules
- API credentials
- Passwords
- Private certificates

---

## 20. Testing Checklist

### Discovery

- [ ] Folder is directly under `vaanar/`
- [ ] Folder name matches entry-file name
- [ ] Plugin appears in Ayodhya
- [ ] Plugin name, description, and version are parsed

### Activation

- [ ] Activation hook runs once
- [ ] Required tables are created
- [ ] Default options are created
- [ ] Plugin appears as active
- [ ] No duplicate migration occurs

### Deactivation

- [ ] Deactivation hook runs
- [ ] Active plugin state is removed
- [ ] Plugin does not leave callbacks active
- [ ] Plugin data is preserved unless removal is explicitly intended

### Security

- [ ] All forms use nonces
- [ ] All mutations check authentication and capabilities
- [ ] SQL values are bound or inserted through PatalDB
- [ ] HTML text is escaped
- [ ] Attributes are escaped
- [ ] URLs are escaped
- [ ] Rich HTML uses `ram_kses()`
- [ ] No raw request values are logged or rendered

### Compatibility

- [ ] PHP 8.1 syntax works
- [ ] No jQuery dependency
- [ ] No core RAMAYAN files are modified
- [ ] Plugin works with plugin files unloaded
- [ ] Plugin does not conflict with existing function names
- [ ] Plugin works in subdirectory installations

### UX

- [ ] Visible labels follow the Ramayan naming convention
- [ ] Success and error states are clear
- [ ] Empty states are handled
- [ ] Admin controls are mobile usable
- [ ] Accessibility labels exist

---

## 21. Troubleshooting

### Plugin does not appear

Check:

- The folder is under `vaanar/`
- The entry file is `<folder>/<folder>.php`
- `Plugin Name:` exists in the header
- The PHP file has no fatal parse error

### Activation does not run

Check:

- `ram_register_activation_hook(__FILE__, ...)` is called
- The callback is declared before activation
- The folder slug equals the activation hook slug
- PatalDB is connected

### Hook does not run

Check:

- The plugin is active
- The hook name is exact
- The callback is callable
- The hook fires after plugin loading
- Priority and argument count are correct

### Database table is missing

Check:

- The table is created in the activation callback
- `PatalDB::ramFn_getPrefix()` is used
- PatalDB is connected
- MySQL privileges allow `CREATE TABLE`

### Plugin output is unsafe

Replace direct output with the appropriate Sudarshana helper:

```php
ram_esc_html()
ram_esc_attr()
ram_esc_url()
ram_kses()
ram_sanitize_email()
```

---

## 22. Reference Plugin

A complete plugin should contain at minimum:

```text
1. Header metadata
2. RAM_PATH guard
3. Activation hook
4. Deactivation hook
5. Option definitions
6. Akashvani action/filter registrations
7. Unique functions or namespaced classes
8. Escaped output
9. Nonce validation
10. PatalDB data access
11. Packaged assets
12. Documentation
```

Use the bundled plugin examples as implementation references:

- [vaanar/ram-dashboard-widgets](/xampp/htdocs/rmcms/vaanar/ram-dashboard-widgets)
- [vaanar/ram-seo-meta-box](/xampp/htdocs/rmcms/vaanar/ram-seo-meta-box)
