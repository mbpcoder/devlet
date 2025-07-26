<?php
declare(strict_types=1);

namespace DevLet;

final class DevLetConfigurator
{
    public function __construct(
        private readonly string        $projectsDir,
        private readonly HostsManager  $hostsManager,
        private readonly SSLManager    $sslManager,
        private readonly ApacheManager $apacheManager,
        private readonly LoggerService $logger,
        private readonly string        $defaultPhpPath = '/usr/bin/php' // change as needed
    )
    {
    }

    public function run(): void
    {
        $dirs = array_filter(glob(rtrim($this->projectsDir, '/') . '/*'), 'is_dir');
        sort($dirs, SORT_NATURAL | SORT_FLAG_CASE); // Alphabetical, case-insensitive

        $activeDomains = [];

        foreach ($dirs as $projectPath) {
            $domain = null;
            $phpPath = null;

            $this->log('📁 Project: ' . $projectPath);

            $devletFile = $projectPath . '/.devlet';
            if (file_exists($devletFile)) {
                $this->log('🔍 Found .devlet file');

                $config = parse_ini_file($devletFile);

                if (isset($config['domain']) && is_string($config['domain']) && trim($config['domain']) !== '') {
                    $customDomain = trim($config['domain']);
                    $domain = $this->normalizeDomain($customDomain);
                    $this->log("🔗 Using domain from config: $domain");
                }

                if (isset($config['php']) && is_string($config['php']) && $this->validatePhpPath(trim($config['php']))) {
                    $phpPath = trim($config['php']);
                    $this->log("🧪 Using PHP from config: $phpPath");
                }
            } else {
                $this->log('ℹ️  No .devlet file found');
            }

            $phpPath = $phpPath ?? $this->defaultPhpPath;
            $domain = $domain ?? $this->normalizeDomain(basename($projectPath));

            if (!$this->validatePhpPath($phpPath)) {
                $this->log("❌ Invalid PHP path: $phpPath — skipping $domain");
                die;
                continue;
            }

            $isLaravel = file_exists($projectPath . '/artisan');
            $docRoot = $isLaravel ? $projectPath . '/public' : $projectPath;

            if (!is_dir($docRoot)) {
                $this->log("❌ Missing doc root: $docRoot — skipping $domain");
                die;
                continue;
            }

            $project = new Project($projectPath, $phpPath, $domain, $docRoot);

            $sslResult = $this->sslManager->generate($project->domain);
            if ($sslResult === false) {
                $this->log("❌ SSL failed for {$project->domain} — skipping");
                die;
                continue;

            }

            [$certPath, $keyPath] = $sslResult;

            $vhostConf = $this->apacheManager->createVhost($project, $certPath, $keyPath);
            $this->apacheManager->enableSite($vhostConf);

            $this->log("✅ Configured: {$project->domain}\n");

            $activeDomains[] = $project->domain;

            var_dump($activeDomains);

            $this->hostsManager->syncEntries($activeDomains);

            die;
        }


        $this->log("✅ Hosts file updated with active domains.");

        $this->apacheManager->restartApache();
        $this->log("🔁 Apache restarted.");
    }

    private function validatePhpPath(string $phpPath): bool
    {
        return is_file($phpPath) && is_executable($phpPath);
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
