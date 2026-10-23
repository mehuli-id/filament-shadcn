<?php

namespace Mehuli\FilamentShadcn\Tests\Unit;

use Mehuli\FilamentShadcn\Support\SafeUrl;
use Mehuli\FilamentShadcn\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class SafeUrlTest extends TestCase
{
    public static function allowed(): array
    {
        return [
            ['https://mehuli.id/a?b=1'], ['http://localhost:8000'], ['/admin/users'], ['#bagian'], ['?q=1'],
            ['halaman/2'], ['mailto:halo@mehuli.id'], ['tel:+6281234'], ['//cdn.mehuli.id/x.png'], ['/p?next=javascript:x'],
            // Entitas HTML tidak didekode browser di sini karena Blade meng-escape '&' → URL relatif biasa.
            ['javascript&colon;alert(1)'],
        ];
    }

    public static function blocked(): array
    {
        return [
            ['javascript:alert(1)'], ['JaVaScRiPt:alert(1)'], [" java\tscript:alert(1)"], ["\x00javascript:alert(1)"],
            ["java\nscript:alert(1)"], ['vbscript:msgbox(1)'], ['data:text/html,<script>alert(1)</script>'],
            ['blob:https://mehuli.id/1'], ['file:///etc/passwd'], ['ftp://x'], ['  '], [''],
        ];
    }

    #[DataProvider('allowed')]
    public function test_url_aman_diterima(string $url): void
    {
        $this->assertSame(trim($url), SafeUrl::filter($url));
    }

    #[DataProvider('blocked')]
    public function test_url_berbahaya_ditolak(string $url): void
    {
        $this->assertNull(SafeUrl::filter($url));
    }

    public function test_bukan_string_ditolak(): void
    {
        $this->assertNull(SafeUrl::filter(null));
        $this->assertNull(SafeUrl::filter(['javascript:alert(1)']));
    }

    public function test_config_skema_tidak_peka_huruf_besar(): void
    {
        config(['filament-shadcn.allowed_url_schemes' => ['HTTPS']]);

        $this->assertNotNull(SafeUrl::filter('https://mehuli.id'));
        $this->assertNull(SafeUrl::filter('http://mehuli.id'));
        $this->assertNull(SafeUrl::filter('javascript:alert(1)'));
    }
}
