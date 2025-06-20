<?php

namespace App\Filament\Resources\AddressResource\Pages;

use App\Filament\Resources\AddressResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAddress extends EditRecord
{
    protected static string $resource = AddressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Просмотр адреса'),
            Actions\DeleteAction::make()
                ->label('Удалить адрес')
                ->visible(fn () => auth()->user()->hasRole('admin')),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
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

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Адрес успешно обновлен';
    }

    protected function getDeletedNotificationTitle(): ?string
    {
        return 'Адрес успешно удален';
    }
}
