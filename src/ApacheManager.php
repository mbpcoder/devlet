<?php
declare(strict_types=1);

namespace DevLet;

final readonly class ApacheManager
{
    public function __construct(
        private string $vhostsDir = '/etc/apache2/sites-available'
    )
    {
    }

    public function createVhost(Project $project, string $certPath, string $keyPath): string
    {
        $fileName = $project->domain . '.conf';
        $filePath = $this->vhostsDir . '/' . $fileName;

        $content = <<<APACHE
<VirtualHost *:80>
    ServerName {$project->domain}
    Redirect permanent / https://{$project->domain}/
</VirtualHost>

<VirtualHost *:443>
    ServerName {$project->domain}

    DocumentRoot "{$project->docRoot}"

    SSLEngine on
    SSLCertificateFile $certPath
    SSLCertificateKeyFile $keyPath

    <Directory "{$project->docRoot}">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/{$project->domain}-error.log
    CustomLog \${APACHE_LOG_DIR}/{$project->domain}-access.log combined
</VirtualHost>
APACHE;

        file_put_contents($filePath, $content);
        echo "Created Apache vhost config: $filePath\n";

        return $fileName;
    }

    public function enableSite(string $siteConf): bool
    {
        exec("a2ensite $siteConf", $out, $ret);
        if ($ret === 0) {
            echo "Enabled site $siteConf\n";
            return true;
        }

        echo "Failed to enable site $siteConf\n";
        return false;
    }

    public function restartApache(): bool
    {
        exec("systemctl restart apache2", $out, $ret);
        if ($ret === 0) {
            echo "Apache restarted successfully\n";
            return true;
        }
        echo "Failed to restart Apache\n";
        return false;
    }
}