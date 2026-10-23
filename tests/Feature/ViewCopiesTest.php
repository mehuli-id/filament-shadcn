<?php

namespace Mehuli\FilamentShadcn\Tests\Feature;

use Mehuli\FilamentShadcn\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;

/**
 * Package menyalin dua view Filament (dengan sedikit perubahan). Bila Filament mengubah
 * file aslinya, tes ini gagal supaya salinan ditinjau dan sha1 di kepala file diperbarui.
 */
class ViewCopiesTest extends TestCase
{
    public static function copies(): array
    {
        return [
            'chart' => ['resources/views/widgets/chart.blade.php', \Filament\Widgets\ChartWidget::class, 'resources/views/chart-widget.blade.php'],
            'user-menu' => ['resources/views/overrides/filament-panels/components/user-menu.blade.php', \Filament\FilamentServiceProvider::class, 'resources/views/components/user-menu.blade.php'],
        ];
    }

    #[DataProvider('copies')]
    public function test_salinan_view_masih_cocok_dengan_filament(string $copy, string $filamentClass, string $original): void
    {
        $packageRoot = dirname((new ReflectionClass($filamentClass))->getFileName());

        while (! file_exists($packageRoot . '/composer.json')) {
            $packageRoot = dirname($packageRoot);
        }

        $source = $packageRoot . '/' . $original;
        $copyPath = dirname(__DIR__, 2) . '/' . $copy;

        preg_match('/sha1 ([0-9a-f]{40})/', file_get_contents($copyPath), $match);
        $this->assertNotEmpty($match, "Kepala {$copy} harus mencatat sha1 file sumber.");

        $this->assertSame(
            $match[1],
            sha1_file($source),
            "Filament mengubah {$original}. Bandingkan dengan {$copy}, salin ulang perubahan Filament, lalu perbarui sha1 di kepala file.",
        );
    }
}
