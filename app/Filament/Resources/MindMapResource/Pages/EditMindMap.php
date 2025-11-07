<?php

namespace App\Filament\Resources\MindMapResource\Pages;

use App\Filament\Resources\MindMapResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMindMap extends EditRecord
{
    protected static string $resource = MindMapResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
