<?php

namespace App\Filament\Resources\SavedResourceResource\Pages;

use App\Filament\Resources\SavedResourceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSavedResource extends EditRecord
{
    protected static string $resource = SavedResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
