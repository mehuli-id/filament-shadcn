<?php

namespace Mehuli\FilamentShadcn;

use Filament\Forms\Components\DateTimePicker;
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

    protected function registerViewOverrides(): void
    {
        $finder = $this->app['view']->getFinder();
        $hints = $finder->getHints()['filament-panels'] ?? null;

        if ($hints === null) {
            return;
        }

        $ours = __DIR__ . '/../resources/views/overrides/filament-panels';
        $appVendor = resource_path('views/vendor/filament-panels');
        $appFirst = array_values(array_filter($hints, fn (string $path): bool => $path === $appVendor));
        $rest = array_values(array_filter($hints, fn (string $path): bool => $path !== $appVendor && $path !== $ours));

        $finder->replaceNamespace('filament-panels', [...$appFirst, $ours, ...$rest]);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-shadcn');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament-shadcn');

        // Kalender tanggal Filament (bergaya shadcn) sebagai bawaan; ->native() di field tetap menang.
        if (! config('filament-shadcn.forms.native_date_pickers', false) && class_exists(DateTimePicker::class)) {
            DateTimePicker::configureUsing(fn (DateTimePicker $picker): DateTimePicker => $picker
                ->native(false)
                // Format tampilan mengikuti bahasa (mis. "24 Okt 2026 09:30"); ->displayFormat() di field menang.
                ->defaultDateDisplayFormat(fn (): string => __('filament-shadcn::filament-shadcn.forms.date_format'))
                ->defaultDateTimeDisplayFormat(fn (): string => __('filament-shadcn::filament-shadcn.forms.datetime_format'))
                ->defaultDateTimeWithSecondsDisplayFormat(fn (): string => __('filament-shadcn::filament-shadcn.forms.datetime_seconds_format'))
                ->defaultTimeDisplayFormat(fn (): string => __('filament-shadcn::filament-shadcn.forms.time_format'))
                ->defaultTimeWithSecondsDisplayFormat(fn (): string => __('filament-shadcn::filament-shadcn.forms.time_seconds_format'))
                // Kolom kosong tetap memberi petunjuk; ->placeholder() di field menimpa ini.
                ->placeholder(fn (DateTimePicker $component): string => __('filament-shadcn::filament-shadcn.forms.' . match (true) {
                    $component->hasDate() && $component->hasTime() => 'datetime_placeholder',
                    $component->hasTime() => 'time_placeholder',
                    default => 'date_placeholder',
                })));
        }

        // Override view Filament (resources/views/overrides): sesudah override milik aplikasi
        // (resources/views/vendor/filament-panels), sebelum view bawaan Filament.
        $this->app->booted(fn () => $this->registerViewOverrides());

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
