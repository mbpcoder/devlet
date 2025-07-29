<?php

namespace App\Channels\OperationSystems\Drivers;

use App\Channels\OperationSystems\IOperationSystem;
use Illuminate\Support\Facades\Process;
use RuntimeException;

class Linux implements IOperationSystem
{
    public function install(array $applications): bool
    {
        if (empty($applications)) {
            return true;
        }
        $packageManager = $this->detectPackageManager();
        $installCmd = $this->buildInstallCommand($packageManager, $applications);

        $result = Process::run($installCmd);

        if ($result->failed()) {
            $result->throw();
        }
        return true;
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

    public function startService(string $name): true
    {
        return $this->runServiceCommand('start', $name);
    }

    public function restartService(string $name): true
    {
        return $this->runServiceCommand('restart', $name);
    }

    public function reloadService(string $name): true
    {
        return $this->runServiceCommand('reload', $name);
    }

    public function isRunningService(string $name): bool
    {
        $isSystemd = $this->isSystemdUsable();

        $command = $isSystemd
            ? "systemctl is-active {$name}"
            : "service {$name} status";

        $result = Process::run($command);
        $output = strtolower($result->output());

        return $isSystemd
            ? trim($output) === 'active'
            : str_contains($output, 'is running') || str_contains($output, 'start/running');
    }


    public function isSystemdUsable(): bool
    {
        $result = Process::run('systemctl is-system-running');

        if (!$result->successful()) {
            return false;
        }

        $status = trim($result->output());
        $validStates = ['running', 'degraded', 'starting', 'maintenance'];

        return in_array($status, $validStates, true);
    }

    private function runServiceCommand(string $action, string $name): true
    {
        $command = $this->isSystemdUsable()
            ? "systemctl {$action} {$name}"
            : "service {$name} {$action}";

        $result = Process::run($command);

        if ($result->failed()) {
            $result->throw();
        }

        return true;
    }


}
