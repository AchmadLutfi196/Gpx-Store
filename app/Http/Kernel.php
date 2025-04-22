<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // ...

    /**
     * The application's route middleware.
     *
     * @var array<string, class-string|string>
     */
  
     protected $routeMiddleware = [
        // Other middleware entries...
        
    ];
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\SweetAlertMiddleware::class, // Tambahkan ini
        ],
    ];

    
}