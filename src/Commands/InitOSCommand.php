<?php

namespace DevLet\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'init', description: 'Initialize required OS packages.')]
class InitOSCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $packageManager = $this->detectPackageManager();

        $basePackages = config('base_packages');
        $phpVersions = config('php_versions');
        $phpPackagesPatterns = config('php_packages');
        $apacheModules = config('apache_modules');

        $dependencies = $basePackages;

        // Add php packages for all versions
        foreach ($phpVersions as $version) {
            foreach ($phpPackagesPatterns as $type => $pattern) {
                $dependencies[] = sprintf($pattern, $version);
            }
        }

        $output->writeln("🔧 Installing packages: " . implode(', ', $dependencies));

        $installCmd = $this->buildInstallCommand($packageManager, $dependencies);

        $output->writeln("▶ Running install command: $installCmd");
        exec($installCmd, $outputLines, $status);

        if ($status !== 0) {
            $output->writeln("<error>Failed to install packages</error>");
            return Command::FAILURE;
        }

        // Enable Apache modules
        if ($packageManager === 'apt') {
            foreach ($apacheModules as $module) {
                $cmd = "a2enmod $module";
                $output->writeln("▶ Enabling Apache module: $module");
                exec($cmd, $out, $ret);
                if ($ret !== 0) {
                    $output->writeln("<error>Failed to enable Apache module $module</error>");
                }
            }
            // Reload Apache
            exec('systemctl reload apache2');
        } elseif ($packageManager === 'yum') {
            // For yum (e.g. CentOS), enabling Apache modules differs; generally, modules are loaded in config files.
            // You might want to write logic to ensure required modules are enabled or leave instructions.
            $output->writeln("<info>Ensure Apache modules {$apacheModules} are enabled manually on yum-based systems.</info>");
        }

        $output->writeln("<info>Initialization completed successfully!</info>");
        return Command::SUCCESS;
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
