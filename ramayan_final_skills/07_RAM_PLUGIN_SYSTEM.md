
# ═══════════════════════════════════════════════
# SKILL 7 — PLUGIN & THEME DEVELOPMENT RULES
# ═══════════════════════════════════════════════

```
VAANAR SENA PLUGIN STANDARDS:

1. PLUGIN ENTRY FILE HEADER (required):
   <?php
   /**
    * Ramayan CMS — Plugin: [Plugin Name]
    * finggu.com | Author: sudarshanpjadhav
    *
    * Plugin Name: My Plugin
    * Plugin Slug: my-plugin
    * Version: 1.0.0
    * Author: Your Name
    * Requires RAM: 1.0.0
    * Requires PHP: 8.1
    */

2. PLUGIN MUST NEVER:
   - Directly access $wpdb or any WP function
   - Use global variables without ram_ prefix
   - Modify core files
   - Include files outside its own directory
   - Use eval(), base64_decode() on any input
   - Store sensitive data in plain text

3. PLUGIN MUST ALWAYS:
   - Check RAM_VERSION is defined before doing anything
   - Use Akashvani hooks for all integrations
   - Use PatalDB for all DB access
   - Use ram_get_option/ram_update_option for settings
   - Provide activation hook that creates its tables
   - Provide deactivation hook that cleans up if needed
   - Prefix all its own functions/classes uniquely

4. PLUGIN DB TABLES:
   - Use prefix: ram_{plugin_slug}_tablename
   - Example: ram_ramshop_products, ram_ramshop_orders
   - Always create in activation hook, never on every load

5. LANKA THEME STANDARDS:
   - theme.json required with all metadata
   - Never query DB directly — use ram_get_posts(), ram_get_option() etc.
   - Never output unescaped data
   - Must support: post-thumbnails, menus, widgets minimum
   - Templates use .html.php extension
   - Template parts in /parts/ subdirectory
   - All assets versioned (cache-busting)
```

---
