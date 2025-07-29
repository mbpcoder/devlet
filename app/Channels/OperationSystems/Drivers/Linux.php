<?php

namespace App\Channels\OperationSystems\Drivers;

use App\Channels\OperationSystems\IOperationSystem;
use Illuminate\Support\Facades\Process;
use RuntimeException;

class Linux implements IOperationSystem
{
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
