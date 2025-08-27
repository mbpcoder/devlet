<?php

namespace App\Filament\Widgets;

use App\Models\Host;
use Filament\Widgets\Widget;

class HostsCountWidget extends Widget
{
    protected string $view = 'filament.widgets.hosts-count-widget';

    protected int|string|array $columnSpan = 'half';

    public string $projectDirectory = 'c://projects/';
    public bool $isLoading = false;

    protected static bool $isLazy = false;

    protected function getViewData(): array
    {
        return [
            'projectDirectory' => $this->projectDirectory,
            'hostsCount' => Host::query()->count(),
            'isLoading' => $this->isLoading,
        ];
    }

    public function reloadHosts()
    {
        // Optional: Show notification
        $this->dispatch('reload-complete');
    }

}
