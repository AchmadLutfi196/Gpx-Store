<?php

namespace App\Providers;

use App\Filament\Resources\ContactMessageResource;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Use proper method to register groups based on your Filament version
        if (class_exists(\Filament\Facades\Filament::class)) {
            \Filament\Facades\Filament::serving(function () {
                // For Filament v2.x
                \Filament\Facades\Filament::registerNavigationGroups([
                    'Shop',
                    'Customer Service',
                    'Settings',
                ]);
            });
        } elseif (class_exists(\Filament\Support\Facades\FilamentView::class)) {
            // For Filament v3.x
            \Filament\Support\Facades\FilamentView::registerRenderHook(
                'panels::head.start',
                fn (): string => '<meta name="description" content="Gpx-Store Admin Panel">'
            );
        }
    }
}