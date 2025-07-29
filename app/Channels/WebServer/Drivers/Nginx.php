<?php

namespace App\Channels\WebServer\Drivers;

use App\Channels\WebServer\IWebServer;

class Nginx implements IWebServer
{
    public function enableModules(array $modules)
    {
        return true;
    }

    public function start(): true
    {
        return true;
    }

    public function stop(): true
    {
        return true;
    }

    public function restart(): true
    {
        return true;
    }

    public function status(): string
    {
        return "";
    }

    public function isRunning(): bool
    {
        return true;
    }

    public function name(): string
    {
        return 'nginx';
    }

    public function reload(): true
    {
        return true;
    }
}
