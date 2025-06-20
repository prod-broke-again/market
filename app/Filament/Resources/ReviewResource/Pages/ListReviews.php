<?php

namespace App\Filament\Resources\ReviewResource\Pages;

use App\Filament\Resources\ReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReviews extends ListRecords
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Создать отзыв')
                ->icon('heroicon-o-plus')
                ->visible(fn () => auth()->user()->hasRole(['admin', 'buyer'])),
        ];
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
