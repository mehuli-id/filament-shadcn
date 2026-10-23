<?php

namespace Mehuli\FilamentShadcn\Widgets;

use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget as FilamentChartWidget;

/**
 * Grafik bergaya shadcn (docs/mockup/gambar/Shadcn.png):
 *   - tiap seri memakai token --chart-1 … --chart-5 (ikut mode terang/gelap),
 *   - garis halus tanpa titik dengan isian gradasi, sumbu Y disembunyikan,
 *   - legenda kotak kecil di bawah, filter sebagai tombol bersegmen.
 *
 * Warna yang ditulis sendiri di dataset (borderColor/backgroundColor) tetap dipakai.
 * Timpa getOptions() untuk opsi Chart.js lain; shadcnOptions() bisa dipakai sebagai dasar.
 */
abstract class ChartWidget extends FilamentChartWidget
{
    protected string $view = 'filament-shadcn::widgets.chart';

    protected function getOptions(): array | RawJs | null
    {
        return $this->shadcnOptions();
    }

    protected function shadcnOptions(): RawJs
    {
        $fill = $this->getType() === 'line' ? 'true' : 'false';

        return RawJs::make(<<<JS
            (() => {
                const token = (index) => getComputedStyle(document.documentElement)
                    .getPropertyValue(`--chart-\${(index % 5) + 1}`).trim() || 'currentColor'
                const mix = (color, percent) => `color-mix(in oklab, \${color} \${percent}%, transparent)`
                const gradient = (context) => {
                    const area = context.chart.chartArea
                    const color = token(context.datasetIndex)

                    if (! area) {
                        return mix(color, 20)
                    }

                    // Gradasi dari puncak seri ini (bukan puncak grafik), seperti di mockup.
                    const values = (context.chart.data.datasets[context.datasetIndex]?.data ?? []).map(Number).filter(Number.isFinite)
                    const peak = values.length ? context.chart.scales.y?.getPixelForValue(Math.max(...values)) : null
                    const top = Number.isFinite(peak) ? Math.max(area.top, Math.min(peak, area.bottom - 1)) : area.top
                    const fill = context.chart.ctx.createLinearGradient(0, top, 0, area.bottom)
                    fill.addColorStop(0, mix(color, 45))
                    fill.addColorStop(1, mix(color, 4))

                    return fill
                }

                return {
                    datasets: {
                        line: {
                            borderColor: (context) => token(context.datasetIndex),
                            backgroundColor: gradient,
                            pointBackgroundColor: (context) => token(context.datasetIndex),
                            // Seri di atas mengisi sampai seri berikutnya, seri terakhir sampai dasar.
                            fill: (context) => {$fill} && (context.datasetIndex < context.chart.data.datasets.length - 1 ? '+1' : 'origin'),
                            tension: 0.35,
                            borderWidth: 2,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                        },
                        bar: {
                            backgroundColor: (context) => token(context.datasetIndex),
                            borderRadius: 4,
                        },
                    },
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                boxHeight: 10,
                                useBorderRadius: true,
                                borderRadius: 2,
                                generateLabels: (chart) => chart.constructor.defaults.plugins.legend.labels
                                    .generateLabels(chart)
                                    .map((item) => ({ ...item, fillStyle: token(item.datasetIndex), strokeStyle: token(item.datasetIndex), lineWidth: 0 })),
                            },
                        },
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { maxTicksLimit: 6 } },
                        y: { display: false },
                    },
                }
            })()
            JS);
    }
}
