<?php

namespace Mehuli\FilamentShadcn\Widgets;

use Filament\Widgets\StatsOverviewWidget\Stat as FilamentStat;

/**
 * Kartu statistik bergaya shadcn (docs/mockup/gambar/Shadcn.png):
 *
 *   Stat::make('Total pendapatan', 'Rp 248,6 jt')
 *       ->trend('+12,4%', up: true)              // badge di kanan atas
 *       ->description('Naik bulan ini')           // baris tebal (ikon tren otomatis)
 *       ->hint('Pendapatan 6 bulan terakhir');    // baris kecil abu-abu
 *
 * Semua fitur Stat Filament (url, icon, chart, color) tetap bisa dipakai.
 */
class Stat extends FilamentStat
{
    protected string $view = 'filament-shadcn::widgets.stat';

    protected ?string $trend = null;

    protected ?bool $trendUp = null;

    protected ?string $hint = null;

    public function trend(?string $label, ?bool $up = null): static
    {
        $this->trend = $label;
        $this->trendUp = $up;

        return $this;
    }

    public function hint(?string $hint): static
    {
        $this->hint = $hint;

        return $this;
    }

    public function getTrend(): ?string
    {
        return $this->trend;
    }

    public function isTrendUp(): ?bool
    {
        return $this->trendUp;
    }

    public function getTrendIcon(): ?string
    {
        return match ($this->trendUp) {
            true => 'heroicon-m-arrow-trending-up',
            false => 'heroicon-m-arrow-trending-down',
            default => null,
        };
    }

    public function getHint(): ?string
    {
        return $this->hint;
    }
}
