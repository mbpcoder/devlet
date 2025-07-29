<?php

namespace App\Channels\OperationSystems\Drivers;

use App\Channels\OperationSystems\IOperationSystem;

class Windows implements IOperationSystem
{

    public function restartService(string $name):true
    {
        return true;
    }

    public function reloadService(string $name): true
    {
        return true;
    }

    public function isRunningService(string $name): true
    {
        return true;
    }

    public function startService(string $name): true
    {
        return true;
    }

    public function install(array $applications)
    {
        // TODO: Implement install() method.
    }
}
