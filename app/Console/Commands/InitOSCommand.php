<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InitOSCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'devlet:os-init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installing Packages and Dependencies';

    /**
     * Execute the console command.
     * @throws \Throwable
     */
    public function handle()
    {
        $packageManager = $this->detectPackageManager();

        $basePackages = config('dependencies.base_packages');
        $apacheModules = config('dependencies.apache_modules');

        $this->info("🔧 Installing packages: " . implode(', ', $basePackages));

        $installCmd = $this->buildInstallCommand($packageManager, $basePackages);

        $this->info("▶ Running install command: $installCmd");
        exec($installCmd, $outputLines, $status);

        if ($status !== 0) {
            $this->error('Failed to install packages');
            return self::FAILURE;
        }

        // Enable Apache modules
        if ($packageManager === 'apt') {
            foreach ($apacheModules as $module) {
                $cmd = "a2enmod $module";
                $this->info("▶ Enabling Apache module: $module");
                exec($cmd, $out, $ret);
                if ($ret !== 0) {
                    $this->error("Failed to enable Apache module $module");
                }
            }
            // Reload Apache
            exec('systemctl reload apache2');
        } elseif ($packageManager === 'yum') {
            // For yum (e.g. CentOS), enabling Apache modules differs; generally, modules are loaded in config files.
            // You might want to write logic to ensure required modules are enabled or leave instructions.
            $this->info("Ensure Apache modules {$apacheModules} are enabled manually on yum-based systems.");
        }

        $this->info("Initialization completed successfully!");
        return self::SUCCESS;
    }

    private function detectPackageManager(): string
    {
        if (shell_exec('command -v apt-get')) {
            return 'apt';
        }

        if (shell_exec('command -v yum')) {
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
            return "yum install -y $packagesString";
        }

        throw new \RuntimeException("Unsupported package manager");
    }
}
