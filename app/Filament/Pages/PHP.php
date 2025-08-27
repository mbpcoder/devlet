<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PHP extends Page
{
    protected string $view = 'filament.pages.php';
    protected static ?string $title = 'PHP';

    protected static ?int $navigationSort = 4;
}
