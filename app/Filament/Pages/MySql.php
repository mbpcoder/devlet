<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class MySql extends Page
{
    protected string $view = 'filament.pages.mysql';

    protected static ?string $title = 'MySql';
    protected static ?int $navigationSort = 3;
}
