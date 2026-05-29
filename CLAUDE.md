# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What DevLet Is

DevLet is a **local development environment manager** — the goal is Laragon-like UX for Linux/WSL/Windows. It automates Apache VirtualHost creation, SSL certificates via mkcert, PHP-FPM routing, and `/etc/hosts` management per project. It is a Laravel 12 Artisan application with a Filament 5 admin panel, intended to ship as a PHAR binary.

## Commands

```bash
# Install dependencies
composer install

# Run the dev server (serves Filament panel at localhost:8000/admin)
php artisan serve

# Run all tests
composer test
# or
php artisan test

# Run a single test file
php artisan test tests/Feature/ExampleTest.php

# Code formatting (Laravel Pint)
./vendor/bin/pint

# Database
php artisan migrate
php artisan migrate:fresh

# DevLet CLI commands
php artisan devlet:os-install      # Install system packages (Apache, PHP-FPM, mkcert)
php artisan devlet:os-verify       # Verify all dependencies are installed
php artisan devlet:scan-projects   # Scan vhost_roots and store projects in hosts table
php artisan devlet:configure       # Full run: SSL + VHost generation + hosts file sync + Apache restart
```

## Architecture

### Two Parallel Systems

There are two separate systems that currently coexist but are not yet integrated:

1. **CLI pipeline** (`app/Console/Commands/` → `app/Services/`) — the original system; reads the filesystem, generates configs, runs system commands. Stateless — does not use the database.

2. **Filament panel + database** (`app/Filament/` + `app/Models/`) — the new system; stores host/service/cert state in SQLite. The panel lives at `/admin`.

These need to be connected: the CLI pipeline should write to the database, and the panel should be able to trigger CLI operations.

### Driver Pattern

OS and web server operations go through a two-level abstraction:

```
OperationSystemService  →  IOperationSystem  →  Linux / Mac / Windows (drivers)
WebServerService        →  IWebServer        →  Apache / Nginx (drivers)
```

`OperationSystemService` auto-detects the OS and delegates to the correct driver. The **Mac and Windows drivers are stubs** — they need real implementations. `OperationSystemService::detect()` currently always returns `Linux`.

### CLI Data Flow

```
devlet:configure
  → DotDevletFileService::parse()      reads .devlet INI file per project
  → ComposerService::detectPhpVersion() fallback from composer.json
  → ProjectInfoService::detectFramework() identifies laravel/wordpress/symfony/codeigniter
  → ProjectInfoService::resolveDocumentRoot() returns the public dir path
  → Project (value object)             immutable, normalises phpVersion to "8.x"
  → SSLService::generate()             calls mkcert, stores in /etc/ssl/certs/
  → ApacheService::createVhost()       renders stubs/vhost.stub, writes to /etc/apache2/sites-available/
  → ApacheService::enableSite()        calls a2ensite
  → HostsService::syncEntries()        writes 127.0.0.1 entries tagged with #devlet
  → ApacheService::restartApache()
```

### Models

- `Project` — **value object** (not Eloquent). Used only in the CLI pipeline. Normalises PHP version to `"8.x"` format.
- `Host` — Eloquent model, `hosts` table. Used by the Filament panel. Has `active`, `ssl_enabled`, `framework` fields.
- `Service`, `PhpVersion`, `Certificate`, `Setting` — Eloquent models for panel management.

### Filament Panel

Resources live under `app/Filament/Resources/{ModelName}/`. Each resource has its own subdirectory with `Pages/` and `Tables/` sub-folders (Filament 5 structure). Widgets are in `app/Filament/Widgets/`.

The panel provider is at `app/Providers/Filament/AdminPanelProvider.php`.

### Localisation

All UI strings should go through `__('devlet.key')`. Translation files:
- `lang/en/devlet.php` — English
- `lang/fa/devlet.php` — Persian (Farsi)

Persian requires RTL layout — this is not yet implemented in the panel provider.

### Per-Project Configuration (`.devlet` file)

Projects declare their domain and PHP version via an INI file in the project root:

```ini
domain=myapp.local
php=8.2
public_path=public
```

All three keys are optional. Fallbacks: `domain` → `normalizeDomain(dirname)`, `php` → parsed from `composer.json require.php`, `public_path` → framework-specific default.

### Key Config

`config/devlet.php` is the central config:
- `devlet.vhost_roots` — array of directories to scan (from `DEVLET_VHOST_ROOTS` env, comma-separated)
- `devlet.webserver` — `apache2` or `nginx` (from `DEVLET_WEBSERVER_DRIVER`)
- `devlet.vhost_prefix` — prefix for Apache config filenames (`devlet-auto-`)
- `devlet.dependencies` — packages to install and Apache modules to enable

### VHost Template

`stubs/vhost.stub` uses `{{placeholder}}` for PHP-replaced values and `${APACHE_VAR}` for Apache-native variables. PHP replaces: `{{domain}}`, `{{docRoot}}`, `{{phpVersion}}`, `{{certPath}}`, `{{keyPath}}`.

### WSL Support

`isRunningInWSL()` (in `app/helpers.php`) detects WSL via `/proc/version`. WSL-specific behaviour:
- `SSLService` copies the mkcert root CA into the Windows trust store
- `HostsService` also writes to `/mnt/c/Windows/System32/drivers/etc/hosts`
- `HostsService` uses `getWslIp()` instead of `127.0.0.1` for the hosts entry

### Database

SQLite only (`database/database.sqlite`). The file is gitignored. On a fresh clone, run `touch database/database.sqlite && php artisan migrate`. Default admin user for the panel: `admin@devlet.local` / `password` (create via `php artisan filament:make-user`).
