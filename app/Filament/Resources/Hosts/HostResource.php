<?php

namespace App\Filament\Resources\Hosts;

use App\Filament\Resources\Hosts\Pages\CreateHost;
use App\Filament\Resources\Hosts\Pages\EditHost;
use App\Filament\Resources\Hosts\Pages\ListHosts;
use App\Filament\Resources\Hosts\Tables\HostsTable;
use App\Models\Host;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HostResource extends Resource
{
    protected static ?string $model = Host::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
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
                TextInput::make('web_server')
                    ->required()
                    ->default('apache2'),
                TextInput::make('framework'),
                Toggle::make('active')
                    ->required(),
                Toggle::make('ssl_enabled')
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return HostsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHosts::route('/'),
            'create' => CreateHost::route('/create'),
            'edit' => EditHost::route('/{record}/edit'),
        ];
    }
}
