<?php

namespace App\Channels\WebServer;

interface IWebServer
{
    public function start(): true;

    public function stop(): true;

    public function restart(): true;

    public function reload(): true;

    public function status(): string;

    public function isRunning(): bool;

    public function name(): string;
}
