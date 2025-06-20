<?php

namespace App\Filament\Resources\UsersInfoResource\Pages;

use App\Filament\Resources\UsersInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateUsersInfo extends CreateRecord
{
    protected static string $resource = UsersInfoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Если создает не админ (т.е. продавец), то присваиваем его ID
        if (!Auth::user()->hasRole('admin')) {
            $data['user_id'] = Auth::id();
        }

        return $data;
    }
}
