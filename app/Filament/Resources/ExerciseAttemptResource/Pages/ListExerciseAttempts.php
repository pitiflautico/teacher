<?php

namespace App\Filament\Resources\ExerciseAttemptResource\Pages;

use App\Filament\Resources\ExerciseAttemptResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExerciseAttempts extends ListRecords
{
    protected static string $resource = ExerciseAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
