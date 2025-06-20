<?php

namespace App\Filament\Widgets;

use App\Models\Address;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;

class MapWidget extends Widget
{
    protected static string $view = 'filament.widgets.map-widget';

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $addresses = Cache::remember('map_addresses', 300, function () {
            return Address::whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->with(['user', 'product', 'shop'])
                ->get()
                ->map(function ($address) {
                    return [
                        'id' => $address->id,
                        'address' => $address->address,
                        'latitude' => $address->latitude,
                        'longitude' => $address->longitude,
                        'user' => $address->user?->name,
                        'product' => $address->product?->name,
                        'shop' => $address->shop?->name,
                    ];
                });
        });

        return [
            'addresses' => $addresses,
            'apiKey' => config('services.google.maps_api_key') ?? env('GOOGLE_MAPS_API_KEY'),
        ];
    }
} 