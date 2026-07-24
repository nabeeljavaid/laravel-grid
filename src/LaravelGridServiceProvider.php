<?php

namespace NabeelJavaid\LaravelGrid;

use Illuminate\Support\ServiceProvider;

class LaravelGridServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-grid.php',
            'laravel-grid'
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'laravel-grid');

        $this->publishes([
            __DIR__ . '/../config/laravel-grid.php' => config_path('laravel-grid.php'),
        ], 'laravel-grid-config');

        $this->publishes([
            __DIR__ . '/resources/css' => public_path('vendor/laravel-grid/css'),
            __DIR__ . '/resources/js'  => public_path('vendor/laravel-grid/js'),
        ], 'laravel-grid-assets');

        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/laravel-grid'),
        ], 'laravel-grid-views');
    }
}
