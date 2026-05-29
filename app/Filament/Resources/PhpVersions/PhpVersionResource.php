<?php

namespace App\Filament\Resources\PhpVersions;

use App\Filament\Resources\PhpVersions\Pages\CreatePhpVersion;
use App\Filament\Resources\PhpVersions\Pages\EditPhpVersion;
use App\Filament\Resources\PhpVersions\Pages\ListPhpVersions;
use App\Filament\Resources\PhpVersions\Tables\PhpVersionsTable;
use App\Models\PhpVersion;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PhpVersionResource extends Resource
{
    protected static ?string $model = PhpVersion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('version')
                    ->required(),
                Toggle::make('is_default')
                    ->required(),
                TextInput::make('binary_path'),
                TextInput::make('fpm_socket'),
                Toggle::make('installed')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return PhpVersionsTable::configure($table);
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
            'index' => ListPhpVersions::route('/'),
            'create' => CreatePhpVersion::route('/create'),
            'edit' => EditPhpVersion::route('/{record}/edit'),
        ];
    }
}
