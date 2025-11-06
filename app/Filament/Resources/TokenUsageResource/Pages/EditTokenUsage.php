<?php

namespace App\Filament\Resources\TokenUsageResource\Pages;

use App\Filament\Resources\TokenUsageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTokenUsage extends EditRecord
{
    protected static string $resource = TokenUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
