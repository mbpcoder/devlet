<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Project;

final class ApacheService
{
    public function __construct(
        private readonly string $vhostsDir = '/etc/apache2/sites-available',
        private string|null     $vhostPrefix = null,
        private string|null     $stubFile = null
    )
    {
        $this->stubFile = base_path('stubs/vhost.stub');
        $this->vhostPrefix = config('devlet.vhost_prefix');
    }


    public function createVhost(Project $project, string $certPath, string $keyPath): ?string
    {
        // Auto-delete config if project directory no longer exists
        if (!is_dir($project->docRoot)) {
            $autoFileName = $this->vhostPrefix . $project->domain . '.conf';
            $autoFilePath = $this->vhostsDir . '/' . $autoFileName;

            if (file_exists($autoFilePath)) {
                unlink($autoFilePath);
                echo "❌ Project directory missing. Deleted: $autoFilePath\n";
            } else {
                echo "⚠️ Project directory missing. No config to delete for: {$project->domain}\n";
            }

            return null;
        }

        $stub = file_get_contents($this->stubFile);
        if ($stub === false) {
            throw new \RuntimeException("Unable to read Apache vhost stub file.");
        }

        $fileName = $this->vhostPrefix . $project->domain . '.conf';
        $filePath = $this->vhostsDir . '/' . $fileName;

        $content = str_replace(
            ['{{domain}}', '{{docRoot}}', '{{certPath}}', '{{keyPath}}', '{{phpVersion}}'],
            [$project->domain, $project->docRoot, $certPath, $keyPath, $project->phpVersion],
            $stub
        );

        file_put_contents($filePath, $content);
        echo "✅ Created Apache vhost config: $filePath\n";

        return $fileName;
    }

    public function enableSite(string $siteConf): bool
    {
        exec("a2ensite $siteConf", $out, $ret);
        if ($ret === 0) {
            echo "✅ Enabled site $siteConf\n";
            return true;
        }

        echo "❌ Failed to enable site $siteConf\n";
        return false;
    }

    public function restartApache(): bool
    {
        exec("systemctl restart apache2", $out, $ret);
        if ($ret === 0) {
            echo "🔁 Apache restarted successfully\n";
            return true;
        }

        echo "❌ Failed to restart Apache\n";
        return false;
    }
}
