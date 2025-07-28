<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\Process;

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
        $result = Process::run("a2ensite " . escapeshellarg($siteConf));

        if ($result->successful()) {
            echo "✅ Enabled site $siteConf\n";
            return true;
        }

        echo "❌ Failed to enable site $siteConf\n";
        echo $result->errorOutput();
        return false;
    }

    public function restartApache(): bool
    {
        $result = Process::run('systemctl restart apache2');

        if ($result->successful()) {
            echo "🔁 Apache restarted successfully\n";
            return true;
        }

        echo "❌ Failed to restart Apache\n";
        echo $result->errorOutput();
        return false;
    }
}
