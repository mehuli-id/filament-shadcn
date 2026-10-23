<?php

namespace Mehuli\FilamentShadcn\Widgets;

use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Contoh kartu statistik (Section Cards shadcn) untuk halaman Showcase.
 */
class ShowcaseStats extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected function getStats(): array
    {
        return [
            Stat::make('Total pendapatan', 'Rp 248,6 jt')
                ->description('Naik 12,4% bulan ini')
                ->descriptionIcon(Heroicon::ArrowTrendingUp)
                ->color('success')
                ->chart([12, 14, 13, 16, 15, 18, 17, 19, 21, 20, 23, 25]),
            Stat::make('Pesanan baru', '1.284')
                ->description('42 pesanan hari ini')
                ->descriptionIcon(Heroicon::ArrowTrendingUp)
                ->color('success')
                ->chart([30, 32, 31, 35, 33, 36, 40, 38, 41, 43, 42, 46]),
            Stat::make('Pelanggan aktif', '4.672')
                ->description('Turun 2% periode ini')
                ->descriptionIcon(Heroicon::ArrowTrendingDown)
                ->color('danger')
                ->chart([50, 49, 51, 48, 47, 48, 46, 47, 45, 46, 45, 44]),
            Stat::make('Rasio konversi', '3,8%')
                ->description('Sesuai proyeksi'),
        ];
    }
}
