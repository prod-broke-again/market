<?php

namespace App\Filament\Resources\ChatResource\Pages;

use App\Filament\Resources\ChatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChat extends EditRecord
{
    protected static string $resource = ChatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Просмотр чата'),
            Actions\DeleteAction::make()
                ->label('Удалить чат')
                ->visible(fn () => auth()->user()->hasRole('super_admin')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Чат успешно обновлен';
    }

    protected function getDeletedNotificationTitle(): ?string
    {
        return 'Чат успешно удален';
    }
}
