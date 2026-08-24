<?php

namespace App\Filament\Resources\CommunityRequestResource\Pages;

use App\Filament\Resources\CommunityRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommunityRequest extends EditRecord
{
    protected static string $resource = CommunityRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
