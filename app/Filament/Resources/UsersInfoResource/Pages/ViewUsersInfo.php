<?php

namespace App\Filament\Resources\UsersInfoResource\Pages;

use App\Filament\Resources\UsersInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUsersInfo extends ViewRecord
{
    protected static string $resource = UsersInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
} 