<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between">
            <div class="space-y-2">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                    Hosts Overview
                </h3>

                <div class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-folder" class="h-4 w-4" />
                        <span>Project Directory: {{ $projectDirectory }}</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-server" class="h-4 w-4" />
                        <span>Total Hosts: {{ $hostsCount }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center">
                <x-filament::button
                    wire:click="reloadHosts"
                    wire:loading.attr="disabled"
                    icon="heroicon-o-arrow-path"
                    :label="__('Reload')"
                    :tooltip="__('Re-scan project directory for hosts')"
                    color="gray"
                    class="relative"
                >
                    <x-filament::loading-indicator
                        wire:loading
                        class="absolute h-5 w-5"
                    />
                    <span wire:loading.remove>Reload</span>
                </x-filament::button>
            </div>
        </div>

        @if($isLoading)
            <x-filament::loading-indicator class="h-5 w-5 animate-spin text-primary-500 mt-4" />
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
