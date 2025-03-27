<?php

return [
    'auth' => [
        'guard' => env('FILAMENT_AUTH_GUARD', 'web'),
        'pages' => [
            'login' => \Filament\Http\Livewire\Auth\Login::class,
        ],
    ],
    
    'pages' => [
        'namespace' => 'App\\Filament\\Pages',
        'path' => app_path('Filament/Pages'),
        'register' => [
            // App\Filament\Pages\Dashboard::class,
        ],
    ],
    
    'resources' => [
        'namespace' => 'App\\Filament\\Resources',
        'path' => app_path('Filament/Resources'),
        'register' => [],
    ],
    
    'widgets' => [
        'namespace' => 'App\\Filament\\Widgets',
        'path' => app_path('Filament/Widgets'),
        'register' => [
            // App\Filament\Widgets\StatsOverview::class,
        ],
    ],
    
    'livewire' => [
        'namespace' => 'App\\Filament',
        'path' => app_path('Filament'),
    ],
    
    'dark_mode' => [
        'enabled' => true,
        'default' => false,
    ],
    
    'database_notifications' => [
        'enabled' => false,
        'polling_interval' => '30s',
    ],
    
    'layout' => [
        'actions' => [
            'modal' => [
                'actions' => [
                    'alignment' => 'left',
                ],
            ],
        ],
        'forms' => [
            'actions' => [
                'alignment' => 'left',
                'are_sticky' => false,
            ],
            'have_inline_labels' => false,
        ],
        'sidebar' => [
            'is_collapsible_on_desktop' => true,
            'groups' => [
                'are_collapsible' => true,
            ],
            'width' => null,
            'collapsed_width' => null,
        ],
        'footer' => [
            'should_show_logo' => true,
        ],
    ],

    'panels' => [
    \App\Providers\Filament\AdminPanelProvider::class,
],

];