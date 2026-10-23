<?php

namespace Mehuli\FilamentShadcn;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Mehuli\FilamentShadcn\Commands\InstallCommand;
use Mehuli\FilamentShadcn\Support\AppearanceRepository;
use Mehuli\FilamentShadcn\Widgets\ShowcaseChart;
use Mehuli\FilamentShadcn\Widgets\ShowcaseStats;

class FilamentShadcnServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/filament-shadcn.php', 'filament-shadcn');

        $this->app->singleton(AppearanceRepository::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-shadcn');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament-shadcn');

        // Komponen Blade shadcn: <x-shadcn::progress />, <x-shadcn::accordion>, dst.
        Blade::anonymousComponentPath(__DIR__ . '/../resources/views/components', 'shadcn');

        // Widget contoh untuk halaman Showcase.
        if (class_exists(Livewire::class)) {
            Livewire::component('filament-shadcn.showcase-stats', ShowcaseStats::class);
            Livewire::component('filament-shadcn.showcase-chart', ShowcaseChart::class);
        }

        if ($this->app->runningInConsole()) {
            $this->commands([InstallCommand::class]);

            $this->publishes([
                __DIR__ . '/../config/filament-shadcn.php' => config_path('filament-shadcn.php'),
            ], 'filament-shadcn-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/filament-shadcn'),
            ], 'filament-shadcn-views');

            $this->publishes([
                __DIR__ . '/../resources/lang' => $this->app->langPath('vendor/filament-shadcn'),
            ], 'filament-shadcn-translations');

            // Salin CSS ke proyek kalau ingin mengubah CSS-nya langsung.
            $this->publishes([
                __DIR__ . '/../resources/css' => resource_path('css/vendor/filament-shadcn'),
            ], 'filament-shadcn-css');
        }
    }
}
