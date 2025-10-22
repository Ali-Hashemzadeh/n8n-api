<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\File;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Define the path to your v1 routes
            $v1RoutePath = base_path('routes/api/v1');

            // Check if the directory exists
            if (File::isDirectory($v1RoutePath)) {

                // Scan for all .php files in that directory
                $v1RouteFiles = File::files($v1RoutePath);

                // Loop through each file and register it
                foreach ($v1RouteFiles as $file) {
                    Route::middleware('api')
                        ->prefix('api/v1')
                        ->name('api.v1.')
                        ->group($file->getPathname());
                }
            }
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
