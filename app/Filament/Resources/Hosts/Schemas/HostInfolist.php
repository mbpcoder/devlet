<?php

namespace App\Filament\Resources\Hosts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class HostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('full_path'),
                TextEntry::make('php_version'),
                TextEntry::make('domain'),
                TextEntry::make('document_root'),
                TextEntry::make('web_server'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
