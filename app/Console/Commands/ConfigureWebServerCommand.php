<?php

namespace App\Console\Commands;

use App\Exceptions\SSLGenerationException;
use App\Models\Project;
use App\Services\ApacheService;
use App\Services\ComposerService;
use App\Services\DotDevletFileService;
use App\Services\HostsService;
use App\Services\ProjectDetector;
use App\Services\ProjectInfoService;
use App\Services\SSLService;
use Illuminate\Console\Command;

class ConfigureWebServerCommand extends Command
{
    protected $signature = 'devlet:configure';

    protected $description = 'Command description';

    private array $vHostRoots;
    private HostsService $hostsService;
    private SSLService $sslService;
    private ApacheService $apacheService;
    private ProjectInfoService $projectInfoService;
    private DotDevletFileService $dotDevletFileService;
    private ComposerService $composerService;

    public function handle()
    {
        $this->vHostRoots = config('devlet.vhost_roots');

        $this->hostsService = new HostsService();
        $this->sslService = new SSLService();
        $this->apacheService = new ApacheService();
        $this->projectInfoService = new ProjectInfoService();
        $this->dotDevletFileService = new DotDevletFileService();
        $this->composerService = new ComposerService();

        $activeDomains = [];

        foreach ($this->vHostRoots as $vHostRoot) {
            $dirs = array_filter(glob(rtrim($vHostRoot, '/') . '/*'), 'is_dir');
            sort($dirs, SORT_NATURAL | SORT_FLAG_CASE); // Alphabetical, case-insensitive

            foreach ($dirs as $projectPath) {

                $this->info('📁 Project: ' . $projectPath);

                $config = $this->dotDevletFileService->parse($projectPath);

                $phpVersion = $config['php'] ?? $this->composerService->detectPhpVersion($projectPath) ?? phpversion();
                $domain = $config['domain'] ?? normalizeDomain(basename($projectPath));

                $type = $this->projectInfoService->detectFramework($projectPath);
                $docRoot = $this->projectInfoService->resolveDocumentRoot($type, $projectPath, $config['public_path']);

                $this->info("🔍 Detected project type: $type");

                if (!is_dir($docRoot)) {
                    $this->error("❌ Missing doc root: $docRoot — skipping $domain");
                    continue;
                }

                $project = new Project($projectPath, $phpVersion, $domain, $docRoot);

                try {
                    $sslResult = $this->sslService->generate($project->domain);
                } catch (SSLGenerationException $e) {
                    $this->error("❌ SSL failed for {$project->domain} — skipping");
                    continue;
                }

                [$certPath, $keyPath] = $sslResult;

                $vhostConf = $this->apacheService->createVhost($project, $certPath, $keyPath);
                $this->apacheService->enableSite($vhostConf);

                $this->info("✅ Configured: {$project->domain}\n");

                $activeDomains[] = $project->domain;
            }
        }

        $this->hostsService->syncEntries($activeDomains);

        $this->info("✅ Hosts file updated with active domains.");

        $this->apacheService->restartApache();
        $this->info("🔁 Apache restarted.");

        $this->info('Web server configuration completed');

        return self::SUCCESS;
    }
}
