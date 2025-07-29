# DevLet — Local Development Environment Auto-Configurator

**DevLet** is a PHP-based CLI tool built for Laravel Artisan that automates the configuration of local development environments using Apache, SSL (via mkcert), and PHP-FPM.

It streamlines developer onboarding by managing domain setup, certificate generation, and system-level configuration based on a per-project `.devlet` file or `composer.json` fallback.

---

## 🚀 Features

- 🔍 **Project Auto-Discovery** — Scans source directories to detect Laravel or PHP projects
- 🧾 **`.devlet` Support** — Define project-specific domain and PHP version
- 📦 **Composer Integration** — Parses `composer.json` to extract PHP version if `.devlet` is missing
- 🔐 **SSL Certificates with mkcert** — Automatically generates trusted certificates for each domain (supports WSL)
- 🌐 **Apache VirtualHost Generator** — Generates VHost files with full PHP-FPM, SSL, redirect support
- 📓 **/etc/hosts Sync** — Adds domain entries to the local `/etc/hosts` file
- ↪️ **www to non-www Redirects** — Auto-redirect `www.domain` to `domain` securely over HTTPS
- 🧩 **Laravel 12 Artisan Commands** — Modular Laravel Artisan command structure

---

## 📁 Example `.devlet` File

Define custom domain and PHP version per project:

```ini
domain=api.local
php=8.2
```

If this file is missing, DevLet will fallback to `composer.json` and extract `"php": "^8.2"` from `require`.

---

## 🧪 Example Apache VHost (Generated)

```apache
<VirtualHost *:80>
    ServerName api.local
    ServerAlias www.api.local
    Redirect permanent / https://api.local/
</VirtualHost>

<VirtualHost *:443>
    ServerName api.local
    ServerAlias www.api.local

    DocumentRoot "/home/user/Projects/api/public"

    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/api.local.pem
    SSLCertificateKeyFile /etc/ssl/private/api.local-key.pem

    RewriteEngine On
    RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
    RewriteRule ^ https://%1%{REQUEST_URI} [L,R=301]

    <FilesMatch \.php$>
        SetHandler "proxy:unix:/run/php/php8.2-fpm.sock|fcgi://localhost/"
    </FilesMatch>

    <Directory "/home/user/Projects/api/public">
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/api.local-error.log
    CustomLog ${APACHE_LOG_DIR}/api.local-access.log combined
</VirtualHost>
```

---

## 🧰 Available Artisan Commands

### 🛠️ `devlet:os-install`
Installs necessary packages based on `config/devlet.php`.
Handles PHP, Apache, SSL tools, and more.

### ✅ `devlet:os-verify`
Verifies required software and system configuration.

### ⚙️ `devlet:configure`
Auto-configures VHosts, SSL, hosts file, and PHP-FPM mappings.

---

## 🧠 How It Works

1. Scan all project directories (from config)
2. Parse `.devlet` or fallback to `composer.json`
3. Normalize and generate domain names
4. Create and trust SSL certificates (even under WSL)
5. Write Apache config files using Laravel stubs
6. Enable site, reload Apache
7. Update `/etc/hosts` file

---

## 📂 Configurable via `config/devlet.php`

- Project source directories
- Default PHP fallback version
- Apache vhost path
- SSL cert locations
- Auto-reload flags

---

## 🪪 Notes

- DevLet is strictly for **local development**
- Certificates are stored in `/etc/ssl/certs/` and `/etc/ssl/private/`
- Apache configs are prefixed with `devlet-`
- Auto-cleanup stale configs when projects are removed

---

## 🤝 Contributing

Pull requests, ideas, and improvements are welcome!

---

## 📜 License

MIT License © Your Name
