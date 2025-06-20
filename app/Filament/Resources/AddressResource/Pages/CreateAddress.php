<?php

namespace App\Filament\Resources\AddressResource\Pages;

use App\Filament\Resources\AddressResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAddress extends CreateRecord
{
    protected static string $resource = AddressResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Автоматически устанавливаем пользователя как текущего
        if (!isset($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }

        // Обрабатываем координаты из карты
        if (isset($data['coordinates'])) {
            $data['latitude'] = $data['coordinates']['latitude'] ?? null;
            $data['longitude'] = $data['coordinates']['longitude'] ?? null;
            $data['address'] = $data['coordinates']['address'] ?? $data['address'] ?? '';
            unset($data['coordinates']);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Адрес успешно создан';
    }
}
