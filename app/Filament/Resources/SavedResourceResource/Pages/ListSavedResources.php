<?php

namespace App\Filament\Resources\SavedResourceResource\Pages;

use App\Filament\Resources\SavedResourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSavedResources extends ListRecords
{
    protected static string $resource = SavedResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('find_more')
                ->label(__('Find More Resources'))
                ->icon('heroicon-m-magnifying-glass')
                ->url(route('filament.admin.pages.web-resource-finder')),
        ];
    }
}
