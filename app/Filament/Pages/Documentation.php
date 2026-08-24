<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Documentation extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Documentație';

    protected static ?string $title = 'Documentație';

    protected static ?string $slug = 'documentatie';

    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.documentation';
}
