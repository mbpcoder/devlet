<?php

namespace App\Console\Commands;

use App\Channels\OperationSystems\OperationSystemService;
use App\Channels\WebServer\WebServerService;
use Illuminate\Console\Command;

class InstallOSCommand extends Command
{
    protected $signature = 'devlet:os-install';

    protected $description = 'Installing Packages and Dependencies';

    public function handle(): int
    {
        $os = new OperationSystemService();
        $webServerService = new WebServerService();

        $basePackages = config('devlet.dependencies.base_packages');
        $apacheModules = config('devlet.dependencies.apache_modules');

        $this->info("🔧 Installing packages: " . implode(', ', $basePackages));

       $os->install($basePackages);

        $this->info("✅ Packages was successfully installed.");

        $this->info("Web server modules enabling.");

        $webServerService->enableModules($apacheModules);

        $this->info("✅ Web Server modules successfully enabled.");

        if ($webServerService->isRunning()) {
            $webServerService->reload();
            $this->info('✅ Web Server is successfully reloaded.');
        } else {
            $webServerService->start();
            $this->info('✅ Web Server is successfully started.');
        }
        $this->info("✅ Initialization completed successfully!");
        return self::SUCCESS;
    }
}
