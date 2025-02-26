<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Broadcasting
    |--------------------------------------------------------------------------
    |
    | By uncommenting the Laravel Echo configuration, you may connect Filament
    | to any Pusher-compatible websockets server.
    |
    | This will allow your users to receive real-time notifications.
    |
    */

    'broadcasting' => [

        // 'echo' => [
        //     'broadcaster' => 'pusher',
        //     'key' => env('VITE_PUSHER_APP_KEY'),
        //     'cluster' => env('VITE_PUSHER_APP_CLUSTER'),
        //     'wsHost' => env('VITE_PUSHER_HOST'),
        //     'wsPort' => env('VITE_PUSHER_PORT'),
        //     'wssPort' => env('VITE_PUSHER_PORT'),
        //     'authEndpoint' => '/broadcasting/auth',
        //     'disableStats' => true,
        //     'encrypted' => true,
        //     'forceTLS' => true,
        // ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | This is the storage disk Filament will use to store files. You may use
    | any of the disks defined in the `config/filesystems.php`.
    |
    */

    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Assets Path
    |--------------------------------------------------------------------------
    |
    | This is the directory where Filament's assets will be published to. It
    | is relative to the `public` directory of your Laravel application.
    |
    | After changing the path, you should run `php artisan filament:assets`.
    |
    */

    'assets_path' => null,

    /*
    |--------------------------------------------------------------------------
    | Cache Path
    |--------------------------------------------------------------------------
    |
    | This is the directory that Filament will use to store cache files that
    | are used to optimize the registration of components.
    |
    | After changing the path, you should run `php artisan filament:cache-components`.
    |
    */

    'cache_path' => base_path('bootstrap/cache/filament'),

    /*
    |--------------------------------------------------------------------------
    | Livewire Loading Delay
    |--------------------------------------------------------------------------
    |
    | This sets the delay before loading indicators appear.
    |
    | Setting this to 'none' makes indicators appear immediately, which can be
    | desirable for high-latency connections. Setting it to 'default' applies
    | Livewire's standard 200ms delay.
    |
    */

    'livewire_loading_delay' => 'default',


    'default_theme' => 'light',

    'widgets' => [
        App\Filament\Widgets\StatsOverviewWidget::class,
        App\Filament\Widgets\OpportunitiesChart::class,
        App\Filament\Widgets\LatestOpportunities::class,
    ],

    'colors' => [
        'primary' => [
            50 => '240, 249, 255',
            100 => '224, 242, 254',
            200 => '186, 230, 253',
            300 => '125, 211, 252',
            400 => '56, 189, 248',
            500 => '14, 165, 233',
            600 => '2, 132, 199',
            700 => '3, 105, 161',
            800 => '7, 89, 133',
            900 => '12, 74, 110',
            950 => '8, 47, 73',
        ],
        'secondary' => [
            50 => '248, 250, 252',
            100 => '241, 245, 249',
            200 => '226, 232, 240',
            300 => '203, 213, 225',
            400 => '148, 163, 184',
            500 => '100, 116, 139',
            600 => '71, 85, 105',
            700 => '51, 65, 85',
            800 => '30, 41, 59',
            900 => '15, 23, 42',
            950 => '2, 6, 23',
        ],
        'success' => [
            50 => '240, 253, 244',
            100 => '220, 252, 231',
            200 => '187, 247, 208',
            300 => '134, 239, 172',
            400 => '74, 222, 128',
            500 => '34, 197, 94',
            600 => '22, 163, 74',
            700 => '21, 128, 61',
            800 => '22, 101, 52',
            900 => '20, 83, 45',
            950 => '5, 46, 22',
        ],
        'warning' => [
            50 => '255, 247, 237',
            100 => '255, 237, 213',
            200 => '254, 215, 170',
            300 => '253, 186, 116',
            400 => '251, 146, 60',
            500 => '249, 115, 22',
            600 => '234, 88, 12',
            700 => '194, 65, 12',
            800 => '154, 52, 18',
            900 => '124, 45, 18',
            950 => '67, 20, 7',
        ],
        'danger' => [
            50 => '254, 242, 242',
            100 => '254, 226, 226',
            200 => '254, 202, 202',
            300 => '252, 165, 165',
            400 => '248, 113, 113',
            500 => '239, 68, 68',
            600 => '220, 38, 38',
            700 => '185, 28, 28',
            800 => '153, 27, 27',
            900 => '127, 29, 29',
            950 => '69, 10, 10',
        ],
        'info' => [
            50 => '240, 249, 255',
            100 => '224, 242, 254',
            200 => '186, 230, 253',
            300 => '125, 211, 252',
            400 => '56, 189, 248',
            500 => '14, 165, 233',
            600 => '2, 132, 199',
            700 => '3, 105, 161',
            800 => '7, 89, 133',
            900 => '12, 74, 110',
            950 => '8, 47, 73',
        ],
        'gray' => [
            50 => '249, 250, 251',
            100 => '243, 244, 246',
            200 => '229, 231, 235',
            300 => '209, 213, 219',
            400 => '156, 163, 175',
            500 => '107, 114, 128',
            600 => '75, 85, 99',
            700 => '55, 65, 81',
            800 => '31, 41, 55',
            900 => '17, 24, 39',
            950 => '3, 7, 18',
        ],
    ],

];
