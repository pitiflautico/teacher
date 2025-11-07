<?php

namespace App\Filament\Resources\CalendarEventResource\Pages;

use App\Filament\Resources\CalendarEventResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCalendarEvent extends CreateRecord
{
    protected static string $resource = CalendarEventResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
