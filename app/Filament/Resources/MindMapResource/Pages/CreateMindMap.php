<?php

namespace App\Filament\Resources\MindMapResource\Pages;

use App\Filament\Resources\MindMapResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMindMap extends CreateRecord
{
    protected static string $resource = MindMapResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
