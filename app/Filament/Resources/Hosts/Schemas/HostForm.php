<?php

namespace App\Filament\Resources\Hosts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class HostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('full_path')
                    ->required(),
                TextInput::make('php_version'),
                TextInput::make('domain')
                    ->required(),
                TextInput::make('document_root')
                    ->required(),
                Select::make('web_server')
                    ->options(['apache2' => 'Apache2', 'nginx' => 'Nginx'])
                    ->default('apache2')
                    ->required(),
            ]);
    }
}
