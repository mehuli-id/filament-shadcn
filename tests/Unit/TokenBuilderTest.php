<?php

namespace Mehuli\FilamentShadcn\Tests\Unit;

use Mehuli\FilamentShadcn\Support\ThemeSettings;
use Mehuli\FilamentShadcn\Support\TokenBuilder;
use Mehuli\FilamentShadcn\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class TokenBuilderTest extends TestCase
{
    public static function unsafeTokens(): array
    {
        return [
            ['url(https://evil.test/x.png)'], ['image-set("x.png" 1x)'], ['expression(alert(1))'], ['env(safe-area-inset-top)'],
            ['attr(data-x)'], ['red; background: url(x)'], ['red</style><script>alert(1)</script>'], ['red } body { display:none'],
            ['"quoted"'], ["'quoted'"], ['a:b'], ['//evil.test'], ['red /* sisa token mati'], ['red */'], [str_repeat('a', 121)],
        ];
    }

    #[DataProvider('unsafeTokens')]
    public function test_token_berbahaya_ditolak(string $value): void
    {
        $this->assertNull(TokenBuilder::safeTokenValue($value));
    }

    public function test_token_normal_diterima(): void
    {
        foreach (['oklch(0.55 0.22 263)', '#2563eb', '0.5rem', 'calc(1rem + 2px)', 'rgb(37, 99, 235)', '10%'] as $value) {
            $this->assertSame($value, TokenBuilder::safeTokenValue($value), $value);
        }
    }

    public function test_css_tidak_bisa_keluar_dari_blok_style(): void
    {
        // Nilai dari JSON halaman Tampilan / config yang diedit tangan (melewati validasi form).
        $settings = ThemeSettings::resolve([], [
            'tokens' => [
                'light' => ['primary' => 'red;}</style><script>alert(1)</script>', 'radius' => '1rem;}*{display:none', 'x<y' => 'blue'],
                'dark' => ['background' => 'url(https://evil.test)'],
            ],
        ]);

        $css = TokenBuilder::make($settings)->css();

        $this->assertStringNotContainsStringIgnoringCase('</style', $css);
        $this->assertStringNotContainsStringIgnoringCase('<script', $css);
        $this->assertStringNotContainsString('evil.test', $css);
        // Satu-satunya url() adalah ikon centang/radio buatan package (data:image/svg+xml).
        $this->assertDoesNotMatchRegularExpression('/url\((?!"data:image\/svg\+xml,)/', $css);
        $this->assertStringNotContainsString('display:none', $css);
        // Tepat dua blok: html:root{…} dan html.dark{…}.
        $this->assertSame(2, substr_count($css, '{'));
        $this->assertSame(2, substr_count($css, '}'));
    }

    public function test_atribut_data_hanya_nilai_dikenal(): void
    {
        $attrs = TokenBuilder::make(ThemeSettings::resolve([], ['style' => 'bukan-gaya', 'sidebar' => 'inset']))->dataAttributes();

        $this->assertSame('nova', $attrs['scStyle']);
    }
}
