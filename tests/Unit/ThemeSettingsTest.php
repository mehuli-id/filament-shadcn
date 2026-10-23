<?php

namespace Mehuli\FilamentShadcn\Tests\Unit;

use Mehuli\FilamentShadcn\Support\Presets;
use Mehuli\FilamentShadcn\Support\ThemeSettings;
use Mehuli\FilamentShadcn\Tests\TestCase;

class ThemeSettingsTest extends TestCase
{
    public function test_nama_font_berbahaya_ditolak(): void
    {
        foreach (["Inter';}</style><script>alert(1)</script>", 'Inter"', 'Inter;', 'Inter{', '<Inter>', 'url(x)', str_repeat('a', 70)] as $font) {
            $this->assertDoesNotMatchRegularExpression(ThemeSettings::FONT_NAME_PATTERN, $font, $font);
        }

        foreach (['Inter', 'Plus Jakarta Sans', 'Geist Mono', 'IBM Plex Sans 2'] as $font) {
            $this->assertMatchesRegularExpression(ThemeSettings::FONT_NAME_PATTERN, $font, $font);
        }
    }

    public function test_font_berbahaya_dari_json_tidak_dipakai(): void
    {
        $settings = ThemeSettings::resolve([], ['font_sans' => "Inter';}</style><script>alert(1)</script>"]);

        $this->assertStringNotContainsString('<', $settings->fontSans());
        $this->assertStringNotContainsString("'", $settings->fontSans());
    }

    public function test_nilai_warna(): void
    {
        foreach (['#fff', '#2563eb', 'rgb(37, 99, 235)', 'oklch(0.55 0.22 263)', 'oklch(55% 0.22 263)'] as $color) {
            $this->assertTrue(Presets::isColorValue($color), $color);
        }

        foreach (['#zzz', 'rgb(1,2)', 'oklch(1 2 3);}', 'rgb(1,2,3) url(x)', '#2563eb;', 'red', 'oklch(garbage'] as $color) {
            $this->assertFalse(Presets::isColorValue($color), $color);
        }
    }
}
