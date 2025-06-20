<?php

namespace App\Filament\Resources\UsersInfoResource\Pages;

use App\Filament\Resources\UsersInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsersInfos extends ListRecords
{
    protected static string $resource = UsersInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
