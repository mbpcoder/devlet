<?php

namespace App\Channels\OperationSystems;

use App\Channels\OperationSystems\Drivers\Linux;
use App\Channels\OperationSystems\Drivers\Mac;
use App\Channels\OperationSystems\Drivers\Windows;


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
