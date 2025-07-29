<?php

namespace App\Channels\OperationSystems;

interface IOperationSystem
{

    public function startService(string $name): true;

    public function restartService(string $name): true;

    public function reloadService(string $name): true;

    public function isRunningService(string $name): bool;

}
