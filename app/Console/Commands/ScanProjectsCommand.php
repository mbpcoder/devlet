<?php

namespace App\Console\Commands;

use App\Models\Host;
use App\Services\ComposerService;
use App\Services\DotDevletFileService;
use App\Services\ProjectInfoService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ScanProjectsCommand extends Command
{
    protected $signature = 'devlet:scan-projects';

    protected $description = 'Scans project directories and stores them in the hosts table';

    private DotDevletFileService $dotDevletFileService;
    private ComposerService $composerService;

    private ProjectInfoService $projectInfoService;

    public function handle()
    {
        $this->dotDevletFileService = new DotDevletFileService();
        $this->composerService = new ComposerService();
        $this->projectInfoService = new ProjectInfoService();

        $paths = config('devlet.vhost_roots');
        $webServer = config('devlet.web_server', 'apache2');

        foreach ($paths as $basePath) {
            if (!is_dir($basePath)) {
                $this->warn("Directory $basePath does not exist.");
                continue;
            }

            $projects = File::directories($basePath);

            $defaultPhpVersion = phpversion();
            // Extract first matching major.minor version from constraint (e.g., ^8.2 => 8.2)
            if (preg_match('/(\d+\.\d+)/', $defaultPhpVersion, $matches)) {
                $defaultPhpVersion = $matches[1];
            }

            foreach ($projects as $projectPath) {
                $config = $this->dotDevletFileService->parse($projectPath);

                $projectName = basename($projectPath);
                $phpVersion = $config['php'] ?? $this->composerService->detectPhpVersion($projectPath) ?? $defaultPhpVersion;
                $domain = $config['domain'] ?? normalizeDomain(basename($projectPath));

                $type = $this->projectInfoService->detectFramework($projectPath);
                $documentRoot = $this->projectInfoService->resolveDocumentRoot($type, $projectPath, $config['public_path']);

                if (!is_dir($documentRoot)) {
                    $this->error("❌ Missing doc root: $documentRoot — skipping $domain");
                    continue;
                }

                Host::query()->updateOrCreate(
                    ['full_path' => $projectPath],
                    [
                        'name' => $projectName,
                        'php_version' => $phpVersion,
                        'domain' => $domain,
                        'document_root' => $documentRoot,
                        'web_server' => $webServer,
                    ]
                );

                $this->info("✅ Scanned: $projectName");
            }
        }

        $this->info("🎉 Project scan complete.");
        return CommandAlias::SUCCESS;
    }
}
