<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\ApacheService;
use App\Services\HostsService;
use App\Services\ProjectDetector;
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
    private ProjectDetector $detector;

    public function handle()
    {
        $this->vHostRoots = config('devlet.vhost_roots');

        $this->hostsService = new HostsService();
        $this->sslService = new SSLService();
        $this->apacheService = new ApacheService();
        $this->detector = new ProjectDetector();


        $this->run2();

        $this->info('Web server configuration completed');

        return self::SUCCESS;
    }

    public function run2()
    {
        $activeDomains = [];

        foreach ($this->vHostRoots as $vHostRoot) {
            $dirs = array_filter(glob(rtrim($vHostRoot, '/') . '/*'), 'is_dir');
            sort($dirs, SORT_NATURAL | SORT_FLAG_CASE); // Alphabetical, case-insensitive

            foreach ($dirs as $projectPath) {
                $this->info('📁 Project: ' . $projectPath);

                $config = $this->parseDevletFile($projectPath);

                $domain = $config['domain'] ?? $this->normalizeDomain(basename($projectPath));
                $phpVersion = $config['php'] ?? $this->detectPhpVersionFromComposer($projectPath) ?? phpversion();

                $type = $this->detector->detect($projectPath);
                $docRoot = $this->detector->getDocRoot($type, $projectPath);
                $this->info("🔍 Detected project type: $type");

                if (!is_dir($docRoot)) {
                    $this->info("❌ Missing doc root: $docRoot — skipping $domain");
                    continue;
                }

                $project = new Project($projectPath, $phpVersion, $domain, $docRoot);

                $sslResult = $this->sslService->generate($project->domain);
                if ($sslResult === false) {
                    $this->info("❌ SSL failed for {$project->domain} — skipping");
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

    }

    private function parseDevletFile(string $projectPath): array
    {
        $config = [];
        $file = $projectPath . '/.devlet';

        if (!file_exists($file)) {
            return $config;
        }

        $this->info('🔍 Found .devlet file');
        $ini = parse_ini_file($file);

        if (isset($ini['domain']) && is_string($ini['domain']) && trim($ini['domain']) !== '') {
            $config['domain'] = $this->normalizeDomain(trim($ini['domain']));
            $this->info("🔗 Using domain from config: {$config['domain']}");
        }

        if (isset($ini['php']) && is_string($ini['php'])) {
            $config['php'] = trim($ini['php']);
            $this->info("🧪 Using PHP from config: {$config['php']}");
        }

        return $config;
    }

    private function detectPhpVersionFromComposer(string $projectPath): ?string
    {
        $composerFile = $projectPath . '/composer.json';
        if (!file_exists($composerFile)) {
            return null;
        }

        $json = json_decode(file_get_contents($composerFile), true);
        if (!isset($json['require']['php'])) {
            return null;
        }

        $phpConstraint = $json['require']['php'];
        preg_match('/(\d+\.\d+)/', $phpConstraint, $matches);
        if (!isset($matches[1])) {
            return null;
        }

        $version = $matches[1];
        $this->info("🧪 Detected PHP version from composer.json: $version");

        return $version;
    }

    private function normalizeDomain(string $name): string
    {
        // Convert CamelCase or PascalCase to kebab-case
        $kebab = preg_replace('/([a-z])([A-Z])/', '$1-$2', $name);
        $kebab = preg_replace('/([A-Z])([A-Z][a-z])/', '$1-$2', $kebab);

        // Replace . and _ with -
        $kebab = str_replace(['.', '_'], '-', $kebab);

        // Convert to lowercase
        $kebab = strtolower($kebab);

        // Remove duplicate dashes
        $kebab = preg_replace('/-+/', '-', $kebab);

        if (str_contains($name, '.')) {
            return $kebab;
        }

        return $kebab . '.local';
    }
}
