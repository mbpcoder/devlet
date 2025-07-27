<?php

namespace DevLet\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'verify', description: 'Verify required OS packages.')]
class VerifyOSCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $packageManager = $this->detectPackageManager();

        $basePackages = config('base_packages');
        $phpVersions = config('php_versions');
        $phpPackagesPatterns = config('php_packages');
        $apacheModules = config('apache_modules');

        $dependencies = $basePackages;

        foreach ($phpVersions as $version) {
            foreach ($phpPackagesPatterns as $type => $pattern) {
                $dependencies[] = sprintf($pattern, $version);
            }
        }

        $allOk = true;

        foreach ($dependencies as $package) {
            if ($this->isPackageInstalled($packageManager, $package)) {
                $output->writeln("✔ Package installed: $package");
            } else {
                $output->writeln("<error>✘ Package missing: $package</error>");
                $allOk = false;
            }
        }

        // Check apache modules (only for apt systems here)
        if ($packageManager === 'apt') {
            foreach ($apacheModules as $module) {
                if ($this->isApacheModuleEnabled($module)) {
                    $output->writeln("✔ Apache module enabled: $module");
                } else {
                    $output->writeln("<error>✘ Apache module disabled: $module</error>");
                    $allOk = false;
                }
            }
        } else {
            $output->writeln("<info>Apache module check is only implemented for apt-based systems.</info>");
        }

        return $allOk ? Command::SUCCESS : Command::FAILURE;
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
