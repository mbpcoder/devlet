<?php

namespace App\Channels\WebServer\Drivers;

use App\Channels\OperationSystems\OperationSystemService;
use App\Channels\WebServer\IWebServer;

class Apache implements IWebServer
{
    public function __construct(
        private readonly OperationSystemService $os
    )
    {
    }

    public function start(): true
    {
        return $this->os->startService('apache2');
    }

    public function stop(): true
    {
        return $this->os->runCommand('sudo systemctl stop apache2');
    }

    public function restart(): true
    {
        return $this->os->restartService('apache2');
    }

    public function reload(): true
    {
        return $this->os->reloadService('apache2');
    }

    public function status(): string
    {
        return $this->os->runCommand('sudo systemctl status apache2')->output();
    }

    public function isRunning(): bool
    {
        return $this->os->isRunningService('apache2');
    }

    public function name(): string
    {
        return 'apache';
    }
}
