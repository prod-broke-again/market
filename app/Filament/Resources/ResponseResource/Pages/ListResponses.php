<?php

namespace App\Filament\Resources\ResponseResource\Pages;

use App\Filament\Resources\ResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListResponses extends ListRecords
{
    protected static string $resource = ResponseResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];
        
        // Только админы и продавцы могут создавать отклики
        if (auth()->user()->hasRole(['admin', 'seller'])) {
            $actions[] = Actions\CreateAction::make()
                ->label('Создать отклик')
                ->icon('heroicon-o-plus');
        }
        
        return $actions;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Можно добавить виджеты статистики
        ];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }
}
