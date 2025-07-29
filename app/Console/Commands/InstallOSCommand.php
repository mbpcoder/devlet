<?php

namespace App\Console\Commands;

use App\Channels\WebServer\WebServerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class InstallOSCommand extends Command
{
    protected $signature = 'devlet:os-install';

    protected $description = 'Installing Packages and Dependencies';

    public function __construct(
        private readonly WebServerService $webServerService
    )
    {
        parent::__construct();
    }

    public function handle()
    {
        $packageManager = $this->detectPackageManager();

        $basePackages = config('devlet.dependencies.base_packages');
        $apacheModules = config('devlet.dependencies.apache_modules');

        $this->info("🔧 Installing packages: " . implode(', ', $basePackages));

        $installCmd = $this->buildInstallCommand($packageManager, $basePackages);

        $this->info("▶ Running install command: $installCmd");

        $result = Process::run($installCmd);

        if (!$result->successful()) {
            $this->error('❌ Failed to install packages:');
            $this->error($result->errorOutput());
            return self::FAILURE;
        }

        // Enable Apache modules
        if ($packageManager === 'apt') {
            foreach ($apacheModules as $module) {
                $cmd = "a2enmod $module";
                $this->info("▶ Enabling Apache module: $module");

                $enableResult = Process::run($cmd);

                if (!$enableResult->successful()) {
                    $this->error("❌ Failed to enable Apache module $module");
                    $this->error($enableResult->errorOutput());
                }
            }


            if ($this->webServerService->isRunning()) {
                $this->webServerService->reload();
                $this->info('✅ Web Server is successfully reloaded.');
            } else {
                $this->webServerService->start();
                $this->info('✅ Web Server is successfully started.');
            }


        } elseif ($packageManager === 'yum') {
            $this->info("⚠️ Ensure Apache modules " . implode(', ', $apacheModules) . " are enabled manually on yum-based systems.");
        }

        $this->info("✅ Initialization completed successfully!");
        return self::SUCCESS;
    }

    private function detectPackageManager(): string
    {
        if (Process::run('command -v apt-get')->successful()) {
            return 'apt';
        }

        if (Process::run('command -v yum')->successful()) {
            return 'yum';
        }

        throw new \RuntimeException("Unsupported package manager");
    }

    private function buildInstallCommand(string $packageManager, array $packages): string
    {
        $packagesString = implode(' ', $packages);

        if ($packageManager === 'apt') {
            return "apt-get update && apt-get install -y $packagesString";
        }

        if ($packageManager === 'yum') {
            return "yum update -y && yum install -y $packagesString";
        }

        throw new \RuntimeException("Unsupported package manager");
    }
}
