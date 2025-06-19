<?php

namespace App\Filament\Resources\CustomerRequestResource\Pages;

use App\Filament\Resources\CustomerRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerRequest extends CreateRecord
{
    protected static string $resource = CustomerRequestResource::class;
}
