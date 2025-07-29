<?php

namespace App\Channels\OperationSystems;

use App\Channels\OperationSystems\Drivers\Linux;
use App\Channels\OperationSystems\Drivers\Mac;
use App\Channels\OperationSystems\Drivers\Windows;


use Illuminate\Support\Facades\Process;
use InvalidArgumentException;

class OperationSystemService implements IOperationSystem
{
    private IOperationSystem $os;

    public function __construct()
    {
        $this->os = $this->detect();
    }

    private function detect(): IOperationSystem
    {
        // detect os
        return new Linux();
    }

    public function enableApacheModules(array $modules): true
    {
        $modulesList = implode(' ', array_map('escapeshellarg', $modules));
        $cmd = "a2enmod $modulesList";
        return $this->runCommand($cmd);
    }

    public function runCommand(string $command): true
    {
        $result = Process::run($command);

        if ($result->failed()) {
            $result->throw();
        }

        return true;
    }

    public function install(array $applications)
    {
        return $this->os->install($applications);
    }

    public function startService(string $name): true
    {
        return $this->os->startService($name);
    }

    public function restartService(string $name): true
    {
        return $this->os->restartService($name);
    }

    public function reloadService(string $name): true
    {
        return $this->os->reloadService($name);
    }

    public function isRunningService(string $name): bool
    {
        return $this->os->isRunningService($name);
    }
}
