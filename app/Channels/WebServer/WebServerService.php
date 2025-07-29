<?php


namespace App\Channels\WebServer;

use App\Channels\OperationSystems\OperationSystemService;
use App\Channels\WebServer\Drivers\Apache;
use App\Channels\WebServer\Drivers\Nginx;

use InvalidArgumentException;

readonly class WebServerService implements IWebServer
{
    private IWebServer $webServer;

    public function __construct(
        private OperationSystemService $os = new OperationSystemService(),
    )
    {
        $this->webServer = $this->driver();
    }

    public function driver(string|null $name = null): IWebServer
    {
        if ($name === null) {
            $name = config('devlet.webserver', 'apache');
        }

       return match ($name) {
            'apache' => new Apache($this->os),
            'nginx' => new Nginx($this->os),
            default => throw new InvalidArgumentException("Unsupported web server driver: {$name}"),
        };
    }

    public function start(): true
    {
        return $this->webServer->start();
    }

    public function stop(): true
    {
        return $this->webServer->stop();
    }

    public function restart(): true
    {
        return $this->webServer->restart();
    }

    public function reload(): true
    {
        return $this->webServer->reload();
    }

    public function status(): string
    {
        return $this->webServer->status();
    }

    public function isRunning(): bool
    {
        return $this->webServer->isRunning();
    }

    public function name(): string
    {
        return $this->webServer->name();
    }


}
