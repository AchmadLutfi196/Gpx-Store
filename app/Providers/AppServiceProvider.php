<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Check if Filament exists and is properly loaded
        if (class_exists(\Filament\Facades\Filament::class)) {
            \Filament\Facades\Filament::serving(function () {
                // Safe customizations for Filament v2.x
                if (method_exists(\Filament\Facades\Filament::class, 'registerNavigationGroups')) {
                    \Filament\Facades\Filament::registerNavigationGroups([
                        'Shop',
                        'Customer Service',
                        'Settings',
                    ]);
                }
            });
        }
    }
}