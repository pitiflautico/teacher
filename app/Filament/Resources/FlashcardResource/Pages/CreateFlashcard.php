<?php

namespace App\Filament\Resources\FlashcardResource\Pages;

use App\Filament\Resources\FlashcardResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFlashcard extends CreateRecord
{
    protected static string $resource = FlashcardResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['easiness_factor'] = 250; // Default 2.5
        $data['interval'] = 0;
        $data['repetitions'] = 0;
        $data['next_review_at'] = now();

        return $data;
    }
}
