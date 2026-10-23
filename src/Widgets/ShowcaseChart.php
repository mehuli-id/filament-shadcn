<?php

namespace Mehuli\FilamentShadcn\Widgets;

use Filament\Widgets\ChartWidget;

/**
 * Contoh grafik untuk halaman Showcase. Warna garis mengikuti token
 * --chart-1 … --chart-5 lewat CSS tema (warna 'primary', 'success', …).
 */
class ShowcaseChart extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $heading = 'Pendapatan per kanal';

    protected ?string $description = '12 bulan terakhir, dalam juta rupiah';

    protected ?string $maxHeight = '280px';

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = 'year';

    protected function getFilters(): ?array
    {
        return [
            'quarter' => '3 bulan',
            'year' => '12 bulan',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $labels = ['Nov', 'Des', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt'];
        $website = [92, 108, 97, 101, 113, 108, 121, 129, 125, 137, 146, 160];
        $marketplace = [50, 60, 54, 58, 63, 61, 67, 72, 70, 77, 82, 89];

        if ($this->filter === 'quarter') {
            $labels = array_slice($labels, -3);
            $website = array_slice($website, -3);
            $marketplace = array_slice($marketplace, -3);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Website',
                    'data' => $website,
                    'fill' => true,
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Marketplace',
                    'data' => $marketplace,
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
