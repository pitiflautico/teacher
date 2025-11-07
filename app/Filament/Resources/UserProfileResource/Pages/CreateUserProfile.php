<?php

namespace App\Filament\Resources\UserProfileResource\Pages;

use App\Filament\Resources\UserProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUserProfile extends CreateRecord
{
    protected static string $resource = UserProfileResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
