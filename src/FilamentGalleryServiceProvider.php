<?php

declare(strict_types=1);

namespace WwGallery\FilamentGallery;

use Illuminate\Support\ServiceProvider;

final class FilamentGalleryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/filament-gallery.php',
            'filament-gallery',
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-gallery');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament-gallery');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/../config/filament-gallery.php' => config_path('filament-gallery.php'),
        ], 'filament-gallery-config');

        $this->publishes([
            __DIR__ . '/../resources/lang' => $this->app->langPath('vendor/filament-gallery'),
        ], 'filament-gallery-translations');
    }
}
