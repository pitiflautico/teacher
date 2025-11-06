<?php

namespace App\Filament\Resources\TokenUsageResource\Pages;

use App\Filament\Resources\TokenUsageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTokenUsages extends ListRecords
{
    protected static string $resource = TokenUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
