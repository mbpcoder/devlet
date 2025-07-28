<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VerifyOSCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'devlet:os-verify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify Installing Packages and Dependencies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $packageManager = $this->detectPackageManager();

        $basePackages = config('devlet.dependencies.base_packages');
        $apacheModules = config('devlet.dependencies.apache_modules');

        $dependencies = $basePackages;

        $allOk = true;

        foreach ($dependencies as $package) {
            if ($this->isPackageInstalled($packageManager, $package)) {
                $this->info("✔ Package installed: $package");
            } else {
                $this->error("✘ Package missing: $package");
                $allOk = false;
            }
        }

        // Check apache modules (only for apt systems here)
        if ($packageManager === 'apt') {
            foreach ($apacheModules as $module) {
                if ($this->isApacheModuleEnabled($module)) {
                    $this->info("✔ Apache module enabled: $module");
                } else {
                    $this->error("✘ Apache module disabled: $module");
                    $allOk = false;
                }
            }
        } else {
            $this->info("Apache module check is only implemented for apt-based systems.");
        }

        return $allOk ? \Symfony\Component\Console\Command\Command::SUCCESS : Command::FAILURE;
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

    private function isPackageInstalled(string $packageManager, string $package): bool
    {
        if ($packageManager === 'apt') {
            exec("dpkg -s $package 2>/dev/null", $output, $status);
            return $status === 0;
        }

        if ($packageManager === 'yum') {
            exec("rpm -q $package 2>/dev/null", $output, $status);
            return $status === 0;
        }

        return false;
    }

    private function isApacheModuleEnabled(string $module): bool
    {
        exec("apache2ctl -M 2>/dev/null", $output, $status);
        if ($status !== 0) {
            return false;
        }

        foreach ($output as $line) {
            if (stripos($line, $module . '_module') !== false) {
                return true;
            }
        }
        return false;
    }
}
