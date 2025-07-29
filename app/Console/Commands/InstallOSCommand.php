<?php

namespace App\Console\Commands;

use App\Channels\OperationSystems\OperationSystemService;
use App\Channels\WebServer\WebServerService;
use Illuminate\Console\Command;

class InstallOSCommand extends Command
{
    protected $signature = 'devlet:os-install';

    protected $description = 'Installing Packages and Dependencies';

    public function __construct(
        private readonly OperationSystemService $os,
        private readonly WebServerService       $webServerService
    )
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $basePackages = config('devlet.dependencies.base_packages');
        $apacheModules = config('devlet.dependencies.apache_modules');

        $this->info("🔧 Installing packages: " . implode(', ', $basePackages));

        $this->os->install($basePackages);

        $this->info("✅ Packages was successfully installed.");

        $this->info("Web server modules enabling.");

        $this->webServerService->enableModules($apacheModules);

        $this->info("✅ Web Server modules successfully enabled.");

        if ($this->webServerService->isRunning()) {
            $this->webServerService->reload();
            $this->info('✅ Web Server is successfully reloaded.');
        } else {
            $this->webServerService->start();
            $this->info('✅ Web Server is successfully started.');
        }
        $this->info("✅ Initialization completed successfully!");
        return self::SUCCESS;
    }
}
