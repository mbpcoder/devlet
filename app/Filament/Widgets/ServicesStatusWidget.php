<?php

namespace App\Filament\Widgets;

use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ServicesStatusWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $services = Service::all();

        return $services->map(function (Service $service) {
            $color = match ($service->status) {
                'running' => 'success',
                'stopped' => 'danger',
                default => 'warning',
            };

            $icon = match ($service->type) {
                'web_server' => 'heroicon-o-globe-alt',
                'database' => 'heroicon-o-circle-stack',
                'cache' => 'heroicon-o-bolt',
                'mail' => 'heroicon-o-envelope',
                default => 'heroicon-o-cog-6-tooth',
            };

            $description = $service->version
                ? 'v' . $service->version . ($service->port ? ' · :' . $service->port : '')
                : ($service->port ? ':' . $service->port : '');

            return Stat::make($service->name, __('devlet.services.status.' . $service->status))
                ->description($description)
                ->color($color)
                ->icon($icon);
        })->toArray();
    }
}
