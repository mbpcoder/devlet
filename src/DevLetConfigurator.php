<?php
declare(strict_types=1);

namespace DevLet;

final readonly class DevLetConfigurator
{
    public function __construct(
        private string          $projectsDir,
        private HostsManager    $hostsManager,
        private SSLManager      $sslManager,
        private ApacheManager   $apacheManager,
        private LoggerService   $logger,
        private ProjectDetector $detector)
    {
    }

    public function run(): void
    {
        $dirs = array_filter(glob(rtrim($this->projectsDir, '/') . '/*'), 'is_dir');
        sort($dirs, SORT_NATURAL | SORT_FLAG_CASE); // Alphabetical, case-insensitive

        $activeDomains = [];

        foreach ($dirs as $projectPath) {
            $this->log('📁 Project: ' . $projectPath);

            $config = $this->parseDevletFile($projectPath);

            $domain = $config['domain'] ?? $this->normalizeDomain(basename($projectPath));
            $phpVersion = $config['php'] ?? $this->detectPhpVersionFromComposer($projectPath) ?? phpversion();

            $type = $this->detector->detect($projectPath);
            $docRoot = $this->detector->getDocRoot($type, $projectPath);
            $this->log("🔍 Detected project type: $type");

            if (!is_dir($docRoot)) {
                $this->log("❌ Missing doc root: $docRoot — skipping $domain");
                continue;
            }

            $project = new Project($projectPath, $phpVersion, $domain, $docRoot);

            $sslResult = $this->sslManager->generate($project->domain);
            if ($sslResult === false) {
                $this->log("❌ SSL failed for {$project->domain} — skipping");
                continue;
            }

            [$certPath, $keyPath] = $sslResult;

            $vhostConf = $this->apacheManager->createVhost($project, $certPath, $keyPath);
            $this->apacheManager->enableSite($vhostConf);

            $this->log("✅ Configured: {$project->domain}\n");

            $activeDomains[] = $project->domain;
        }

        $this->hostsManager->syncEntries($activeDomains);

        $this->log("✅ Hosts file updated with active domains.");

        $this->apacheManager->restartApache();
        $this->log("🔁 Apache restarted.");
    }

    private function parseDevletFile(string $projectPath): array
    {
        $config = [];
        $file = $projectPath . '/.devlet';

        if (!file_exists($file)) {
            return $config;
        }

        $this->log('🔍 Found .devlet file');
        $ini = parse_ini_file($file);

        if (isset($ini['domain']) && is_string($ini['domain']) && trim($ini['domain']) !== '') {
            $config['domain'] = $this->normalizeDomain(trim($ini['domain']));
            $this->log("🔗 Using domain from config: {$config['domain']}");
        }

        if (isset($ini['php']) && is_string($ini['php'])) {
            $config['php'] = trim($ini['php']);
            $this->log("🧪 Using PHP from config: {$config['php']}");
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
        $this->log("🧪 Detected PHP version from composer.json: $version");

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

    private function log(string $message, string $level = 'info'): void
    {
        $this->logger->log($message, $level);
    }
}
