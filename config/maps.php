<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Maps Configuration
    |--------------------------------------------------------------------------
    |
    | This file is for storing the configuration for maps integration.
    | You can choose between Google Maps and Yandex Maps.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default Map Provider
    |--------------------------------------------------------------------------
    |
    | This value determines the default map provider to use.
    | Supported: "google", "yandex"
    |
    */

    'default_provider' => env('MAPS_DEFAULT_PROVIDER', 'google'),

    /*
    |--------------------------------------------------------------------------
    | Map Providers
    |--------------------------------------------------------------------------
    |
    | Here you may configure the map providers for your application.
    |
    */

    'providers' => [

        'google' => [
            'api_key' => env('GOOGLE_MAPS_API_KEY'),
            'libraries' => ['places'],
            'language' => 'ru',
            'region' => 'RU',
        ],

        'yandex' => [
            'api_key' => env('YANDEX_MAPS_API_KEY'),
            'language' => 'ru_RU',
            'version' => '2.1',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Default Map Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for maps across the application.
    |
    */

    'defaults' => [
        'latitude' => 55.7558,  // Moscow
        'longitude' => 37.6176, // Moscow
        'zoom' => 12,
        'height' => '400px',
        'width' => '100%',
    ],

    /*
    |--------------------------------------------------------------------------
    | Map Features
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific map features.
    |
    */

    'features' => [
        'autocomplete' => true,
        'geocoding' => true,
        'reverse_geocoding' => true,
        'current_location' => true,
        'draggable_marker' => true,
        'click_to_place' => true,
    ],

]; 