<?php

namespace App\Filament\Resources\StaircaseResource\Pages;

use App\Filament\Resources\StaircaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStaircases extends ListRecords
{
    protected static string $resource = StaircaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
