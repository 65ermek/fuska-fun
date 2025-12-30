<?php

return [

    'default' => 'ipapi',

    'services' => [

        'ipapi' => [
            'class' => \Torann\GeoIP\Services\IPApi::class,
            'secure' => true,
            'key' => null,  // API-ключ не требуется
        ],

        'ipgeolocation' => [
            'class' => \Torann\GeoIP\Services\IPGeoLocation::class,
            'key' => env('IPGEOLOCATION_API_KEY'),
        ],

        'maxmind_database' => [
            'class' => \Torann\GeoIP\Services\MaxMindDatabase::class,
            'database' => storage_path('app/geoip.mmdb'),
        ],

        'maxmind_api' => [
            'class' => \Torann\GeoIP\Services\MaxMindWebService::class,
            'user_id' => env('MAXMIND_USER'),
            'license_key' => env('MAXMIND_LICENSE'),
        ],

    ],

    'cache' => 'all',
    'cache_tags' => [],
    'cache_expires' => 60,
];
