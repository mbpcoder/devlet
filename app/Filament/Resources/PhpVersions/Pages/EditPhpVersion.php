<?php

namespace App\Filament\Resources\PhpVersions\Pages;

use App\Filament\Resources\PhpVersions\PhpVersionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPhpVersion extends EditRecord
{
    protected static string $resource = PhpVersionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
