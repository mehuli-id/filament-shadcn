<?php

namespace Mehuli\FilamentShadcn\Widgets;

use Filament\Widgets\StatsOverviewWidget;

/**
 * Contoh kartu statistik (Section Cards shadcn) untuk halaman Showcase.
 * Memakai Widgets\Stat (badge tren + keterangan + petunjuk).
 */
class ShowcaseStats extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected function getColumns(): int | array | null
    {
        return ['default' => 2, 'lg' => 4];
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total pendapatan', 'Rp 248,6 jt')
                ->trend('+12,4%', up: true)
                ->description('Naik bulan ini')
                ->hint('Pendapatan 6 bulan terakhir'),
            Stat::make('Pesanan baru', '1.284')
                ->trend('+8,1%', up: true)
                ->description('Naik 8,1% periode ini')
                ->hint('Akuisisi perlu perhatian'),
            Stat::make('Pelanggan aktif', '4.672')
                ->trend('−2,0%', up: false)
                ->description('Turun 2% periode ini')
                ->hint('Retensi melebihi target'),
            Stat::make('Rasio konversi', '3,8%')
                ->trend('+0,6%', up: true)
                ->description('Performa stabil')
                ->hint('Sesuai proyeksi'),
        ];
    }
}
