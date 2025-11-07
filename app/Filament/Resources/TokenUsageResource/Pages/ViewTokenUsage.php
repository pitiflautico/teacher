<?php

namespace App\Filament\Resources\TokenUsageResource\Pages;

use App\Filament\Resources\TokenUsageResource;
use Filament\Resources\Pages\ViewRecord;

class ViewTokenUsage extends ViewRecord
{
    protected static string $resource = TokenUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
