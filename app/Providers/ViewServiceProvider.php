<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\Category;
use App\Models\Brand;

class ViewServiceProvider extends ServiceProvider
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
        // Singleton untuk categories
        $this->app->singleton('app.categories', function ($app) {
            return Cache::remember('all_categories', 60*60, function () {
                return Category::all();
            });
        });

        // Singleton untuk brands
        $this->app->singleton('app.brands', function ($app) {
            return Cache::remember('all_brands', 60*60, function () {
                return Brand::all();
            });
        });

        // Singleton untuk brand lookup (agar tidak query berulang kali untuk brand yang sama)
        $this->app->singleton('app.brand.lookup', function ($app) {
            $brands = app('app.brands');
            $lookup = [];
            foreach ($brands as $brand) {
                $lookup[$brand->id] = $brand;
            }
            return $lookup;
        });

        // Composer untuk layout utama dan semua view
        View::composer('*', function ($view) {
            $view->with([
                'globalCategories' => app('app.categories'),
                'globalBrands' => app('app.brands'),
                'brandLookup' => app('app.brand.lookup'),
            ]);
        });
    }
}