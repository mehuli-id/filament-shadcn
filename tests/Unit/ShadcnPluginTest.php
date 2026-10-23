<?php

namespace Mehuli\FilamentShadcn\Tests\Unit;

use Mehuli\FilamentShadcn\ShadcnPlugin;
use Mehuli\FilamentShadcn\Tests\TestCase;

class ShadcnPluginTest extends TestCase
{
    public function test_lebar_sidebar_divalidasi(): void
    {
        // Filament mencetak nilai ini tanpa escape di <style>.
        foreach (['16rem;}body{display:none', '16rem</style>', 'calc(100vw)', 'expression(1)', '', '9999999rem'] as $width) {
            $this->assertSame('16rem', ShadcnPlugin::make()->sidebarWidth($width)->getSidebarWidth(), $width);
        }

        $this->assertSame('18rem', ShadcnPlugin::make()->sidebarWidth('18rem')->getSidebarWidth());
        $this->assertSame('280px', ShadcnPlugin::make()->sidebarWidth('280px')->getSidebarWidth());
    }

    public function test_tautan_header_disaring(): void
    {
        $links = ShadcnPlugin::make()->headerLinks([
            ['label' => 'Dokumentasi', 'url' => 'https://filamentphp.com'],
            ['label' => 'Jahat', 'url' => 'javascript:alert(1)'],
            ['label' => '', 'url' => 'https://x.test'],
            ['label' => 'Relatif', 'url' => '/admin/bantuan'],
        ])->getHeaderLinks();

        $this->assertSame(['Dokumentasi', 'Relatif'], array_column($links, 'label'));
        $this->assertTrue($links[0]['external']);
        $this->assertFalse($links[1]['external']);
    }

    public function test_tombol_masuk_sosial_disaring(): void
    {
        config(['filament-shadcn.auth.social' => [
            ['label' => 'Google', 'url' => '/auth/google/redirect', 'icon' => 'google'],
            ['label' => 'Jahat', 'url' => 'data:text/html,x', 'icon' => 'github'],
            ['label' => 'TanpaUrl', 'icon' => 'github'],
        ]]);

        $this->assertSame(['Google'], array_column(ShadcnPlugin::make()->getSocialLoginProviders(), 'label'));
    }

    public function test_halaman_tampilan_bawaan_hanya_lokal(): void
    {
        $this->app['env'] = 'production';
        $this->assertFalse(ShadcnPlugin::make()->canAccessAppearance());

        $this->app['env'] = 'local';
        $this->assertTrue(ShadcnPlugin::make()->canAccessAppearance());

        $this->app['env'] = 'production';
        $this->assertTrue(ShadcnPlugin::make()->canCustomizeAppearance(fn (): bool => true)->canAccessAppearance());
    }

    public function test_showcase_bawaan_mati(): void
    {
        $this->assertFalse(ShadcnPlugin::make()->hasShowcase());
    }
}
