<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Menggunakan request-based singleton untuk menghindari duplikasi
        $this->app->singleton('app.categories', function ($app) {
            return Cache::remember('all_categories', 60*60, function () {
                return Category::all();
            });
        });

        // Composer untuk layout utama dan semua view terkait
        View::composer(['*'], function ($view) {
            $view->with('categories', app('app.categories'));
        });
    }
}