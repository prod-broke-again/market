<?php

namespace App\Filament\Resources\UsersInfoResource\Pages;

use App\Filament\Resources\UsersInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUsersInfo extends EditRecord
{
    protected static string $resource = UsersInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
