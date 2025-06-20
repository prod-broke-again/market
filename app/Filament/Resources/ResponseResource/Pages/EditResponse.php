<?php

namespace App\Filament\Resources\ResponseResource\Pages;

use App\Filament\Resources\ResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditResponse extends EditRecord
{
    protected static string $resource = ResponseResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];
        
        // Только админы могут удалять отклики
        if (auth()->user()->hasRole('admin')) {
            $actions[] = Actions\DeleteAction::make();
        }
        
        return $actions;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Продавцы не могут менять статус
        if (auth()->user()->hasRole('seller')) {
            unset($data['status']);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Отклик успешно обновлен';
    }

    protected function getDeletedNotificationTitle(): ?string
    {
        return 'Отклик успешно удален';
    }
}
