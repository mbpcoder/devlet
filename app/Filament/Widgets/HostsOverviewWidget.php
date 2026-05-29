<?php

namespace App\Filament\Widgets;

use App\Models\Host;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HostsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        return [
            Stat::make(__('devlet.widgets.hosts_overview.total'), Host::count())
                ->icon('heroicon-o-server')
                ->color('primary'),

            Stat::make(__('devlet.widgets.hosts_overview.active'), Host::where('active', true)->count())
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make(__('devlet.widgets.hosts_overview.ssl'), Host::where('ssl_enabled', true)->count())
                ->icon('heroicon-o-lock-closed')
                ->color('info'),
        ];
    }
}
