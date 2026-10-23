<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Mehuli\FilamentShadcn\ShadcnPlugin;

/**
 * Contoh panel dengan tema shadcn/ui. Bagian yang penting:
 *   ->viteTheme(...)   memuat CSS tema
 *   ->plugin(...)      memasang token warna, font, halaman Tampilan, dll.
 *
 * Jangan isi ->colors() / ->font() lagi di sini: plugin yang mengaturnya.
 */
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\Mehuli\FilamentShadcn\Pages\Auth\Login::class)
            ->passwordReset()
            ->profile()
            ->brandName('Panel Admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->darkMode(true)
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->plugin(
                ShadcnPlugin::make()
                    // Semua opsi di bawah boleh dihapus — nilai bawaan dari config/filament-shadcn.php.
                    ->style('nova')                 // vega | nova | maia | lyra | mira
                    ->baseColor('zinc')             // neutral | zinc | slate | stone | gray | …
                    ->primary('default')            // 'default' | 'blue' | '#2563eb' | 'oklch(...)'
                    ->sidebarLayout('inset')        // inset | sidebar | floating
                    ->authLayout('muted')           // muted | split | plain
                    ->brand(subtitle: 'Toko Utama')  // kotak ikon + nama + subjudul di sidebar & login
                    ->canCustomizeAppearance(fn (): bool => (bool) auth()->user()?->is_admin)
                    ->showcase(app()->isLocal()),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
