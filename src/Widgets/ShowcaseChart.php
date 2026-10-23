<?php

namespace Mehuli\FilamentShadcn\Widgets;

use Illuminate\Support\Carbon;

/**
 * Contoh grafik untuk halaman Showcase (docs/mockup/gambar/Shadcn.png).
 * Memakai Widgets\ChartWidget: warna tiap seri dari --chart-1 … --chart-5,
 * isian gradasi, dan filter rentang bersegmen.
 */
class ShowcaseChart extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $heading = 'Pendapatan per kanal';

    protected ?string $maxHeight = '240px';

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = '90';

    protected function getFilters(): ?array
    {
        return [
            '90' => '3 bulan',
            '30' => '30 hari',
            '7' => '7 hari',
        ];
    }

    public function getDescription(): ?string
    {
        return match ($this->filter) {
            '30' => 'Total 30 hari terakhir',
            '7' => 'Total 7 hari terakhir',
            default => 'Total 3 bulan terakhir',
        };
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $days = (int) ($this->filter ?: 90);
        $labels = [];
        $website = [];
        $marketplace = [];

        // Data contoh yang stabil (tanpa acak) supaya tampilan bisa dibandingkan dengan mockup.
        for ($i = $days - 1; $i >= 0; $i--) {
            $n = 90 - $i;
            $labels[] = Carbon::today()->subDays($i)->translatedFormat('j M');
            $website[] = round(260 + 70 * sin($n / 6) + 40 * sin($n / 2.3) + $n * 1.6);
            $marketplace[] = round(110 + 25 * sin($n / 5 + 1) + 18 * sin($n / 1.7) + $n * 0.5);
        }

        return [
            'datasets' => [
                ['label' => 'Website', 'data' => $website],
                ['label' => 'Marketplace', 'data' => $marketplace],
            ],
            'labels' => $labels,
        ];
    }
}
