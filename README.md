
# DevLet — Local Development Environment Auto-Configurator

**DevLet** is a powerful PHP-based CLI tool for automating the setup of Apache virtual hosts with SSL and PHP-FPM support in local development environments.

It simplifies project onboarding by generating domain mappings, handling SSL with [mkcert](https://github.com/FiloSottile/mkcert), and auto-detecting the required PHP version.

---

## 🚀 Features

- 🔍 **Project Auto-Discovery** — Scans project directories and detects type (e.g., Laravel, plain PHP)
- 📝 **`.devlet` File Support** — Define custom domains and PHP versions per project
- 📦 **Composer Integration** — Auto-detects PHP version from `composer.json` if `.devlet` is missing
- 🔐 **Local SSL with mkcert** — Automatically generates trusted certificates for each domain
- 🌐 **Apache VirtualHost Generation** — Creates vhost files with proper DocumentRoot, redirects, SSL, and PHP-FPM setup
- ⚙️ **Hosts File Sync** — Automatically adds the project's domain to `/etc/hosts` (Linux/WSL)
- 🧠 **www to non-www Redirects** — Configures Apache to redirect `www.domain` → `domain` with HTTPS

---

## 📁 Example `.devlet` File

Create a `.devlet` file in the root of your project to customize your local domain and PHP version:

```ini
domain=api.local
php=8.2
```

If this file is not present, DevLet will look inside `composer.json` and extract the PHP version constraint automatically (e.g., `"php": "^8.2"`).

---

## 🧪 Apache Config Example

Generated vhost config:

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

## ✅ Requirements

Before using DevLet, ensure the following are installed and configured:

### Apache Modules

Enable these modules:

```bash
a2enmod rewrite
a2enmod ssl
a2enmod proxy
a2enmod proxy_fcgi
```

### PHP-FPM

Ensure PHP-FPM is installed and running, e.g., `/run/php/php8.2-fpm.sock`.

You can install multiple PHP versions using:

```bash
sudo apt install php8.2-fpm php8.3-fpm ...
```

### mkcert

Install `mkcert` for local SSL generation:

```bash
# macOS (brew)
brew install mkcert
mkcert -install

# Ubuntu (via go)
sudo apt install libnss3-tools
go install filippo.io/mkcert@latest
mkcert -install
```

---

## 🔧 How It Works

1. Scan projects in your workspace
2. Parse `.devlet` or fallback to `composer.json`
3. Normalize domain names
4. Generate and trust SSL certificates
5. Create Apache config files
6. Enable vhost and reload Apache
7. Sync `/etc/hosts` to point to your project domain

---

## 💡 Notes

- DevLet is designed for **local development** only.
- SSL files are stored in `/etc/ssl/certs/` and `/etc/ssl/private/`.
- Config files are named with a `devlet-` prefix.
- If the project directory is removed, DevLet will automatically clean up related Apache config files.

---

## 🤝 Contributing

Contributions, issues, and PRs are welcome!  
Feel free to fork and improve this tool.

---

## 📜 License

MIT License © [Your Name]
