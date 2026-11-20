# Ramayan CMS

Ramayan CMS is a PHP 8.1+ content management system built around a Ramayana-inspired ecosystem.

**Current version:** `1.0.0-alpha`

It includes:

- Ayodhya administration panel
- Swayamvar guided installation wizard
- Patal database layer
- Vaanar plugin system
- Lanka theme system
- Brahmaand API
- Sudarshana security helpers
- Saraswati SEO tools
- Akashvani hooks
- Nandan media library

## Screenshots

### Frontend

![Ramayan CMS frontend](screenshots/frontend.png)

## Requirements

- PHP 8.1 or newer
- MySQL/MariaDB
- PDO MySQL PHP extension
- mbstring PHP extension
- JSON PHP extension
- Apache or a compatible PHP web server
- The project root must be writable during installation

## Installation

1. Download or clone this repository.
2. Place the project in your Apache document root or subdirectory.
3. Open the project in a browser.
4. Ramayan CMS will open the Swayamvar setup wizard.
5. Confirm the server requirements.
6. Enter MySQL database host, name, username, password, and table prefix.
7. Enter the site name and URL.
8. Create the first Ayodhya gatekeeper account.
9. Review and install.
10. Open Ayodhya to begin publishing.

The installer creates `ramayan-config.php` automatically. This file is intentionally excluded from Git and must not be committed.

## Configuration

Start from:

```text
ramayan-config-sample.php
```

Copy it to:

```text
ramayan-config.php
```

Then supply the database credentials and generate unique security keys.

Alternatively, use the Swayamvar installer to create this file automatically.

## Theme Development

Themes are stored in:

```text
lanka/
```

For complete instructions, see:

- [RAMAYAN_THEME_CREATION_GUIDE.md](RAMAYAN_THEME_CREATION_GUIDE.md)
- [RAMAYAN_VISIBLE_NAMING.md](RAMAYAN_VISIBLE_NAMING.md)

## Plugin Development

Plugins are stored in:

```text
vaanar/
```

See:

- [RAMAYAN_PLUGIN_CREATION_GUIDE.md](RAMAYAN_PLUGIN_CREATION_GUIDE.md)
- [RAMAYAN_DEVELOPER_DOCUMENTATION.md](RAMAYAN_DEVELOPER_DOCUMENTATION.md)

## Project Structure

```text
ayodhya/                 Administration panel
lanka/                   Themes
ram/                     Core CMS
swayamvar/               Installation wizard
vaanar/                  Plugins
uploads/                 Uploaded media
cache/                   Runtime cache
```

## Security

- Never commit `ramayan-config.php`.
- Never commit real database passwords or security keys.
- Never commit uploads or runtime logs.
- Use Sudarshana escaping helpers in themes and plugins.
- Use PatalDB or prepared statements for database access.
- Use nonce validation for state-changing forms.

## Development

The project is currently at version `1.0.0-alpha`. It is an alpha release, so database compatibility and public APIs may change.

Run PHP syntax checks for changed PHP files:

```powershell
php -l index.php
php -l swayamvar/index.php
php -l swayamvar/install.php
```
