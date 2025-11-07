<?php

namespace App\Filament\Resources\ExerciseAttemptResource\Pages;

use App\Filament\Resources\ExerciseAttemptResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExerciseAttempt extends EditRecord
{
    protected static string $resource = ExerciseAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
