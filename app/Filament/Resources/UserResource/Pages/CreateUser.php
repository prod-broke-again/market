<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Хешируем пароль
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Устанавливаем email_verified_at если нужно
        if (!isset($data['email_verified_at']) && auth()->user()->hasRole('admin')) {
            $data['email_verified_at'] = now();
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Пользователь успешно создан';
    }
}
