<?php

namespace App\Filament\Resources\ResponseResource\Pages;

use App\Filament\Resources\ResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateResponse extends CreateRecord
{
    protected static string $resource = ResponseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Автоматически устанавливаем продавца как текущего пользователя
        if (auth()->user()->hasRole('seller')) {
            $data['seller_id'] = auth()->id();
        }

        // Устанавливаем статус по умолчанию
        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Отклик успешно создан';
    }
}
