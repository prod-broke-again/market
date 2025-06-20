<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Просмотр пользователя'),
            Actions\DeleteAction::make()
                ->label('Удалить пользователя')
                ->visible(fn () => auth()->user()->hasRole('admin')),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Хешируем пароль только если он был изменен
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Убираем подтверждение пароля
        unset($data['password_confirmation']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Пользователь успешно обновлен';
    }

    protected function getDeletedNotificationTitle(): ?string
    {
        return 'Пользователь успешно удален';
    }
}
