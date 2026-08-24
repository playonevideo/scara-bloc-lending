<?php

namespace App\Filament\Resources\StaircaseResource\Pages;

use App\Filament\Resources\StaircaseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStaircase extends EditRecord
{
    protected static string $resource = StaircaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
