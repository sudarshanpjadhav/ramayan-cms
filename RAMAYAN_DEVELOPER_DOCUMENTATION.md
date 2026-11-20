# Ramayan CMS Developer Documentation

This index connects the complete implementation guides for building themes and plugins.

## Guides

- [Theme Creation Guide](RAMAYAN_THEME_CREATION_GUIDE.md)
- [Plugin Creation Guide](RAMAYAN_PLUGIN_CREATION_GUIDE.md)
- [Visible Naming Convention](RAMAYAN_VISIBLE_NAMING.md)
- [Main Installation and Development README](README.md)

## Theme Development

Themes use the Lanka directory and Chitrakoot template engine.

Start here:

1. [Requirements and naming](RAMAYAN_THEME_CREATION_GUIDE.md#2-requirements)
2. [Create the theme folder](RAMAYAN_THEME_CREATION_GUIDE.md#4-create-the-theme-folder)
3. [Create metadata](RAMAYAN_THEME_CREATION_GUIDE.md#6-add-theme-metadata)
4. [Create header and footer](RAMAYAN_THEME_CREATION_GUIDE.md#10-create-the-header-and-footer)
5. [Create templates](RAMAYAN_THEME_CREATION_GUIDE.md#11-create-the-index-template)
6. [Escape and secure output](RAMAYAN_THEME_CREATION_GUIDE.md#16-output-escaping-rules)
7. [Test and package](RAMAYAN_THEME_CREATION_GUIDE.md#25-testing-checklist)

Theme folder:

```text
lanka/theme-slug/
```

## Plugin Development

Plugins use the Vaanar directory and Akashvani hook system.

Start here:

1. [Requirements](RAMAYAN_PLUGIN_CREATION_GUIDE.md#2-requirements)
2. [Create the plugin folder](RAMAYAN_PLUGIN_CREATION_GUIDE.md#3-plugin-directory-structure)
3. [Add plugin metadata](RAMAYAN_PLUGIN_CREATION_GUIDE.md#4-plugin-header)
4. [Register lifecycle hooks](RAMAYAN_PLUGIN_CREATION_GUIDE.md#6-activation-and-deactivation)
5. [Use options and hooks](RAMAYAN_PLUGIN_CREATION_GUIDE.md#7-plugin-options)
6. [Add dashboard or editor features](RAMAYAN_PLUGIN_CREATION_GUIDE.md#9-add-an-ayodhya-dashboard-widget)
7. [Add database access](RAMAYAN_PLUGIN_CREATION_GUIDE.md#12-database-access)
8. [Test and package](RAMAYAN_PLUGIN_CREATION_GUIDE.md#20-testing-checklist)

Plugin folder:

```text
vaanar/plugin-slug/
```

Plugin entry file:

```text
vaanar/plugin-slug/plugin-slug.php
```

## Core Systems

| System | Ramayan Name | Responsibility |
|---|---|---|
| Themes | Lanka | Theme discovery and assets |
| Templates | Chitrakoot | Template rendering |
| Routing | Pushpak | Page and content routing |
| Plugins | Vaanar | Plugin discovery and lifecycle |
| Hooks | Akashvani | Actions and filters |
| Database | Patal | Database access |
| Security | Sudarshana | Escaping, nonces, sanitization |
| SEO | Saraswati | Metadata and sitemap |
| Media | Nandan | Media upload and library |
| Admin | Ayodhya | Content and system administration |
| Installer | Swayamvar | Guided installation |

## Mandatory Development Rules

1. PHP 8.1 or newer.
2. No direct core-file modification.
3. No jQuery.
4. Use PatalDB or Ramayan helper functions for database access.
5. Escape all dynamic output.
6. Validate and verify state-changing requests.
7. Use plugin/theme-prefixed functions and options.
8. Use lowercase kebab-case directory names.
9. Keep assets inside the theme or plugin directory.
10. Never include credentials or runtime data.

## Bundled Examples

- [Ramayan Modern Theme](lanka/ram-default)
- [Ramayan Aurora Theme](lanka/ram-aurora)
- [Ramayan Business Theme](lanka/ram-business)
- [Ramayan Dashboard Widgets Plugin](vaanar/ram-dashboard-widgets)
- [Ramayan SEO Meta Box Plugin](vaanar/ram-seo-meta-box)
