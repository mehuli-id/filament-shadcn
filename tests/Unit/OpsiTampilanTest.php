<?php

namespace Mehuli\FilamentShadcn\Tests\Unit;

use Filament\FontProviders\GoogleFontProvider;
use Filament\FontProviders\LocalFontProvider;
use Filament\Support\Colors\Color;
use Mehuli\FilamentShadcn\ShadcnPlugin;
use Mehuli\FilamentShadcn\Support\ThemeSettings;
use Mehuli\FilamentShadcn\Support\TokenBuilder;
use Mehuli\FilamentShadcn\Tests\TestCase;

/**
 * Opsi tampilan tambahan (radius kartu/kontrol, latar halaman, font & tebal judul,
 * warna tambahan, palet utama, label kapital, angka tabular). Semuanya opsional:
 * tanpa diisi, token & atribut yang dicetak sama dengan sebelumnya.
 */
class OpsiTampilanTest extends TestCase
{
    public function test_tanpa_opsi_baru_tidak_ada_token_atau_atribut_baru(): void
    {
        $builder = TokenBuilder::make(ThemeSettings::resolve());
        $css = $builder->css();

        foreach (['--sc-font-heading', '--sc-heading-weight', '--page:', '--sidebar-active'] as $token) {
            $this->assertStringNotContainsString($token, $css, $token);
        }

        $this->assertSame(['scStyle', 'scSidebar', 'scAuth', 'scAuthImage', 'scPrimary'], array_keys($builder->dataAttributes()));
        $this->assertSame(['gray', 'primary', 'danger', 'success', 'warning', 'info'], array_keys(ThemeSettings::resolve()->filamentColors()));
    }

    public function test_radius_kartu_dan_kontrol_tersendiri(): void
    {
        $shared = TokenBuilder::make(ThemeSettings::resolve(['radius' => 0.875, 'radii' => ['card' => 1.25, 'control' => 0.75]]))->shared();

        $this->assertSame('20px', $shared['sc-radius-card']);
        $this->assertSame('22px', $shared['sc-radius-inset']);
        $this->assertSame('12px', $shared['sc-radius-control']);
        // Turunan lain tetap dari radius dasar.
        $this->assertSame('0.875rem', $shared['radius']);
        $this->assertSame('12px', $shared['sc-radius-item']);
    }

    public function test_radius_tersendiri_bisa_nol_dan_menang_atas_gaya_pil(): void
    {
        $shared = TokenBuilder::make(ThemeSettings::resolve(['style' => 'maia', 'radii' => ['card' => 0, 'control' => 0.5]]))->shared();

        $this->assertSame('0px', $shared['sc-radius-card']);
        $this->assertSame('0px', $shared['sc-radius-inset']);
        $this->assertSame('8px', $shared['sc-radius-control']);
    }

    public function test_radius_tersendiri_yang_tidak_sah_diabaikan(): void
    {
        $bawaan = TokenBuilder::make(ThemeSettings::resolve())->shared();
        $shared = TokenBuilder::make(ThemeSettings::resolve(['radii' => ['card' => '1rem;}', 'control' => 'besar']]))->shared();

        $this->assertSame($bawaan['sc-radius-card'], $shared['sc-radius-card']);
        $this->assertSame($bawaan['sc-radius-control'], $shared['sc-radius-control']);
    }

    public function test_token_latar_halaman_dan_item_aktif_sidebar(): void
    {
        $builder = TokenBuilder::make(ThemeSettings::resolve(['tokens' => [
            'light' => ['page' => '#f4f5fa', 'sidebar-active' => '#eef0ff', 'sidebar-active-foreground' => '#4338ca'],
            'dark' => ['page' => '#0b0e14'],
        ]]));

        $this->assertSame('#f4f5fa', $builder->light()['page']);
        $this->assertSame('#eef0ff', $builder->light()['sidebar-active']);
        $this->assertSame('#0b0e14', $builder->dark()['page']);
        // --background tetap putih untuk modal & tombol outline.
        $this->assertSame('oklch(1 0 0)', $builder->light()['background']);
    }

    public function test_font_judul(): void
    {
        $builder = TokenBuilder::make(ThemeSettings::resolve(['font_heading' => 'Outfit']));

        $this->assertStringStartsWith('"Outfit", var(--font-family)', $builder->shared()['sc-font-heading']);
        $this->assertSame('on', $builder->dataAttributes()['scHeadingFont']);
    }

    public function test_font_judul_berbahaya_ditolak(): void
    {
        foreach (['Outfit"; } body { display:none', '</style>', "Outfit'", ''] as $name) {
            $builder = TokenBuilder::make(ThemeSettings::resolve(['font_heading' => $name]));

            $this->assertArrayNotHasKey('sc-font-heading', $builder->shared(), $name);
            $this->assertArrayNotHasKey('scHeadingFont', $builder->dataAttributes(), $name);
        }
    }

    public function test_tebal_judul_label_kapital_dan_angka_tabular(): void
    {
        $builder = TokenBuilder::make(ThemeSettings::resolve(['typography' => [
            'heading_weight' => 800,
            'uppercase_labels' => true,
            'tabular_nums' => 'true',
        ]]));

        $this->assertSame('800', $builder->shared()['sc-heading-weight']);
        $this->assertSame('on', $builder->dataAttributes()['scHeadingWeight']);
        $this->assertSame('on', $builder->dataAttributes()['scUpper']);
        $this->assertSame('on', $builder->dataAttributes()['scNums']);
    }

    public function test_tebal_judul_yang_tidak_sah_diabaikan(): void
    {
        foreach ([850, 0, 1000, 'tebal', '800;}'] as $weight) {
            $settings = ThemeSettings::resolve(['typography' => ['heading_weight' => $weight]]);

            $this->assertNull($settings->headingWeight(), (string) $weight);
            $this->assertArrayNotHasKey('sc-heading-weight', TokenBuilder::make($settings)->shared());
        }
    }

    public function test_palet_utama_tersendiri(): void
    {
        $settings = ThemeSettings::resolve(['primary' => 'amber', 'primary_palette' => [
            600 => '#b45309',
            500 => '#f59e0b',
            700 => 'url(x)',
            1234 => '#000000',
        ]]);
        $palette = $settings->primaryPalette();
        $amber = Color::Amber;

        $this->assertSame(Color::convertToOklch('#b45309'), $palette[600]);
        $this->assertSame(Color::convertToOklch('#f59e0b'), $palette[500]);
        $this->assertSame($amber[700], $palette[700]);
        $this->assertArrayNotHasKey(1234, $palette);
        // Token --primary memakai shade 600 dari palet itu.
        $this->assertSame(Color::convertToOklch('#b45309'), TokenBuilder::make($settings)->light()['primary']);
    }

    public function test_palet_utama_diabaikan_bila_primary_bawaan(): void
    {
        $settings = ThemeSettings::resolve(['primary_palette' => [600 => '#b45309']]);

        $this->assertSame(ThemeSettings::resolve()->primaryPalette(), $settings->primaryPalette());
    }

    public function test_warna_tambahan(): void
    {
        $colors = ThemeSettings::resolve(['extra_colors' => [
            'teal' => 'teal',
            'merek' => '#7c3aed',
            'primary' => 'red',
            'Bahaya Besar' => 'red',
            'jahat' => 'url(x)',
            'tidak-dikenal' => 'warnaaneh',
        ]])->filamentColors();

        $this->assertArrayHasKey('teal', $colors);
        $this->assertArrayHasKey('merek', $colors);
        $this->assertCount(11, $colors['teal']);
        $this->assertArrayNotHasKey('bahaya besar', $colors);
        $this->assertArrayNotHasKey('jahat', $colors);
        $this->assertArrayNotHasKey('tidak-dikenal', $colors);
        // Warna bawaan tidak bisa ditimpa lewat warna tambahan.
        $this->assertNotSame(ThemeSettings::resolve()->statusPalette('danger'), $colors['primary']);
        $this->assertSame(ThemeSettings::resolve()->primaryPalette(), $colors['primary']);
    }

    public function test_opsi_dari_config(): void
    {
        config([
            'filament-shadcn.radii' => ['card' => 1.25, 'control' => null],
            'filament-shadcn.font.heading' => 'Outfit',
            'filament-shadcn.typography' => ['heading_weight' => '700', 'uppercase_labels' => false, 'tabular_nums' => null],
            'filament-shadcn.extra_colors' => ['sky' => 'sky'],
        ]);

        $settings = ThemeSettings::resolve();

        $this->assertSame(1.25, $settings->radiusFor('card'));
        $this->assertNull($settings->radiusFor('control'));
        $this->assertSame('Outfit', $settings->fontHeading());
        $this->assertSame(700, $settings->headingWeight());
        $this->assertFalse($settings->uppercaseLabels());
        $this->assertFalse($settings->tabularNums());
        $this->assertArrayHasKey('sky', $settings->filamentColors());
    }

    public function test_api_fluent_plugin(): void
    {
        $settings = ShadcnPlugin::make()
            ->radii(card: 1.25, control: 0.75)
            ->font(heading: 'Outfit')
            ->primary('amber')
            ->primaryPalette([600 => '#b45309'])
            ->extraColors(['teal' => 'teal'])
            ->typography(headingWeight: 800, uppercaseLabels: true, tabularNums: true)
            ->settings();

        $this->assertSame(1.25, $settings->radiusFor('card'));
        $this->assertSame(0.75, $settings->radiusFor('control'));
        $this->assertSame('Outfit', $settings->fontHeading());
        $this->assertSame(Color::convertToOklch('#b45309'), $settings->primaryPalette()[600]);
        $this->assertArrayHasKey('teal', $settings->filamentColors());
        $this->assertSame(800, $settings->headingWeight());
        $this->assertTrue($settings->uppercaseLabels());
        $this->assertTrue($settings->tabularNums());
    }

    public function test_font_judul_dimuat_lewat_penyedia_font(): void
    {
        $this->assertNull(ShadcnPlugin::make()->headingFontHtml());
        $this->assertNull(ShadcnPlugin::make()->font(sans: 'Outfit', heading: 'Outfit')->headingFontHtml());
        $this->assertNull(ShadcnPlugin::make()->font(heading: 'Outfit', provider: LocalFontProvider::class)->headingFontHtml());

        $this->assertStringContainsString('fonts.bunny.net/css?family=outfit', (string) ShadcnPlugin::make()->font(heading: 'Outfit')->headingFontHtml());
        $this->assertStringContainsString('fonts.googleapis.com', (string) ShadcnPlugin::make()->font(heading: 'Outfit', provider: GoogleFontProvider::class)->headingFontHtml());
    }
}
