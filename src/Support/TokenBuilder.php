<?php

namespace Mehuli\FilamentShadcn\Support;

use Filament\Support\Colors\Color;
use Throwable;

/**
 * Menyusun token CSS bergaya shadcn/ui (--background, --primary, --border, …)
 * dari pengaturan tema, lalu mengeluarkannya sebagai <style> di <head>.
 *
 * Nama token sama persis dengan shadcn/ui, jadi tema dari
 * ui.shadcn.com/themes atau tweakcn bisa langsung ditempel ke config `tokens`.
 */
class TokenBuilder
{
    protected const WHITE = 'oklch(1 0 0)';

    protected const NEAR_WHITE = 'oklch(0.985 0 0)';

    public function __construct(
        protected ThemeSettings $settings,
    ) {}

    public static function make(ThemeSettings $settings): static
    {
        return new static($settings);
    }

    /**
     * @return array<string, string>
     */
    public function light(): array
    {
        $base = $this->settings->basePalette();
        [$primary, $primaryForeground, $ring] = $this->primary(dark: false);
        $danger = $this->settings->statusPalette('danger');
        $success = $this->settings->statusPalette('success');
        $warning = $this->settings->statusPalette('warning');
        $info = $this->settings->statusPalette('info');
        $charts = $this->charts('light');

        $tokens = [
            'background' => static::WHITE,
            'foreground' => $base[950],
            'card' => static::WHITE,
            'card-foreground' => $base[950],
            'popover' => static::WHITE,
            'popover-foreground' => $base[950],
            'primary' => $primary,
            'primary-foreground' => $primaryForeground,
            'secondary' => $base[100],
            'secondary-foreground' => $base[900],
            'muted' => $base[100],
            'muted-foreground' => $base[500],
            'accent' => $base[100],
            'accent-foreground' => $base[900],
            'destructive' => $danger[600],
            'destructive-foreground' => static::NEAR_WHITE,
            'success' => $success[600],
            'success-foreground' => static::NEAR_WHITE,
            'warning' => $warning[500],
            'warning-foreground' => $base[950],
            'info' => $info[600],
            'info-foreground' => static::NEAR_WHITE,
            'border' => $base[200],
            'input' => $base[200],
            'ring' => $ring,
            'sidebar' => $base[50],
            'sidebar-foreground' => $base[950],
            'sidebar-primary' => $primary,
            'sidebar-primary-foreground' => $primaryForeground,
            'sidebar-accent' => $base[100],
            'sidebar-accent-foreground' => $base[900],
            'sidebar-border' => $base[200],
            'sidebar-ring' => $ring,
        ];

        foreach ($charts as $index => $chart) {
            $tokens['chart-' . ($index + 1)] = $chart;
        }

        return $this->withOverrides($tokens, 'light');
    }

    /**
     * @return array<string, string>
     */
    public function dark(): array
    {
        $base = $this->settings->basePalette();
        [$primary, $primaryForeground, $ring] = $this->primary(dark: true);
        $danger = $this->settings->statusPalette('danger');
        $success = $this->settings->statusPalette('success');
        $warning = $this->settings->statusPalette('warning');
        $info = $this->settings->statusPalette('info');
        $charts = $this->charts('dark');

        $tokens = [
            'background' => $base[950],
            'foreground' => $base[50],
            'card' => $base[900],
            'card-foreground' => $base[50],
            'popover' => $base[900],
            'popover-foreground' => $base[50],
            'primary' => $primary,
            'primary-foreground' => $primaryForeground,
            'secondary' => $base[800],
            'secondary-foreground' => $base[50],
            'muted' => $base[800],
            'muted-foreground' => $base[400],
            'accent' => $base[800],
            'accent-foreground' => $base[50],
            'destructive' => $danger[400],
            'destructive-foreground' => $base[950],
            'success' => $success[400],
            'success-foreground' => $base[950],
            'warning' => $warning[400],
            'warning-foreground' => $base[950],
            'info' => $info[400],
            'info-foreground' => $base[950],
            'border' => 'oklch(1 0 0 / 10%)',
            'input' => 'oklch(1 0 0 / 15%)',
            'ring' => $ring,
            'sidebar' => $base[900],
            'sidebar-foreground' => $base[50],
            'sidebar-primary' => $primary,
            'sidebar-primary-foreground' => $primaryForeground,
            'sidebar-accent' => $base[800],
            'sidebar-accent-foreground' => $base[50],
            'sidebar-border' => 'oklch(1 0 0 / 10%)',
            'sidebar-ring' => $ring,
        ];

        foreach ($charts as $index => $chart) {
            $tokens['chart-' . ($index + 1)] = $chart;
        }

        return $this->withOverrides($tokens, 'dark');
    }

    /**
     * Token yang sama di mode terang & gelap: radius, ukuran, kerapatan.
     *
     * @return array<string, string>
     */
    public function shared(): array
    {
        $style = $this->settings->style();
        $radius = $this->settings->radius();
        $px = $radius * 16;

        // Skala radius Tailwind dipetakan ke --radius ala shadcn
        // (sm = r-4px, md = r-2px, lg = r, xl = r+4px). Radius 0 = semua 0.
        $scale = fn (float $offset): string => $px <= 0 ? '0px' : max(0, round($px + $offset, 2)) . 'px';

        $control = $style['pill'] ? '9999px' : $scale(-2);

        $tokens = [
            'radius' => $radius . 'rem',
            'radius-xs' => $scale(-6),
            'radius-sm' => $scale(-4),
            'radius-md' => $scale(-2),
            'radius-lg' => $scale(0),
            'radius-xl' => $scale(4),
            'radius-2xl' => $scale(8),
            'radius-3xl' => $scale(12),
            'spacing' => round(0.25 * $this->settings->density(), 4) . 'rem',
            'sc-radius-control' => $control,
            'sc-radius-badge' => $style['pill'] ? '9999px' : $scale(-4),
            'sc-radius-item' => $scale(-2),
            'sc-radius-card' => $scale(4),
            'sc-control-height' => $style['control'] . 'rem',
            'sc-card-padding' => $style['card_padding'] . 'rem',
            'sc-text' => $style['text'] . 'rem',
        ];

        // Token bersama yang ditimpa lewat config (mis. 'radius').
        foreach (['radius'] as $key) {
            if (filled($override = $this->settings->get('tokens')['light'][$key] ?? null)) {
                $tokens[$key] = (string) $override;
            }
        }

        return $tokens;
    }

    public function css(): string
    {
        $light = $this->light();
        $dark = $this->dark();

        $light += $this->checkImages($light['primary-foreground']);
        $dark += $this->checkImages($dark['primary-foreground']);

        // Spesifisitas `html:root` & `html.dark` sengaja lebih tinggi dari
        // `:root` milik Filament/Tailwind supaya token ini selalu menang.
        return 'html:root{' . $this->declarations($this->shared() + $light) . '}'
            . 'html.dark{' . $this->declarations($dark) . '}';
    }

    /**
     * Atribut data di <html> yang dipakai CSS untuk variasi tata letak.
     *
     * @return array<string, string>
     */
    public function dataAttributes(): array
    {
        return [
            'scStyle' => $this->settings->styleName(),
            'scSidebar' => (string) $this->settings->get('sidebar', 'inset'),
            'scAuth' => (string) $this->settings->get('auth_layout', 'muted'),
            'scAuthImage' => (string) $this->settings->get('auth_image_position', 'end'),
            'scPrimary' => $this->settings->isDefaultPrimary() ? 'default' : 'color',
        ];
    }

    /**
     * @return array{0: string, 1: string, 2: string}
     */
    protected function primary(bool $dark): array
    {
        $base = $this->settings->basePalette();

        if ($this->settings->isDefaultPrimary()) {
            return $dark
                ? [$base[200], $base[900], $base[500]]
                : [$base[900], $base[50], $base[400]];
        }

        $palette = $this->settings->primaryPalette();
        $value = $this->settings->primaryValue();

        // Kode warna dipakai apa adanya; nama warna pakai shade 600/500.
        $primary = Presets::isColorValue($value)
            ? Color::convertToOklch($value)
            : ($dark ? $palette[500] : $palette[600]);

        $foreground = $this->readableOn($primary, $base);

        return [$primary, $foreground, $dark ? $palette[700] : $palette[400]];
    }

    /**
     * @param  array<int, string>  $base
     */
    protected function readableOn(string $color, array $base): string
    {
        try {
            return Color::calculateContrastRatio($color, '#ffffff') >= 4.5
                ? static::NEAR_WHITE
                : $base[950];
        } catch (Throwable) {
            return static::NEAR_WHITE;
        }
    }

    /**
     * @return list<string>
     */
    protected function charts(string $mode): array
    {
        $custom = $this->settings->get('charts');

        if (is_array($custom) && ! empty($custom[$mode])) {
            return array_values(array_slice(array_pad((array) $custom[$mode], 5, Presets::charts()[$mode][0]), 0, 5));
        }

        return Presets::charts()[$mode];
    }

    /**
     * @param  array<string, string>  $tokens
     * @return array<string, string>
     */
    protected function withOverrides(array $tokens, string $mode): array
    {
        foreach ((array) ($this->settings->get('tokens')[$mode] ?? []) as $key => $value) {
            $key = ltrim((string) $key, '-');

            if ($key === '' || $key === 'radius' || ! is_scalar($value) || ! filled($value)) {
                continue;
            }

            if (($value = static::safeTokenValue((string) $value)) === null) {
                continue;
            }

            $tokens[$key] = $value;
        }

        return $tokens;
    }

    /**
     * Nilai token dari config / halaman Tampilan hanya boleh berupa warna,
     * ukuran, atau fungsi CSS biasa (oklch(), calc(), var()). Tanda kutip,
     * titik dua, backslash, dan url()/image-set() ditolak supaya token tidak
     * bisa memuat sumber dari luar atau keluar dari blok <style>.
     */
    public static function safeTokenValue(string $value): ?string
    {
        $value = trim($value);

        if (strlen($value) > 120 || ! preg_match('/^[a-zA-Z0-9 .,%#()\/+\-*]+$/', $value)) {
            return null;
        }

        if (preg_match('/\b(url|image|image-set|cross-fade|element|expression|env|attr)\s*\(/i', $value) || str_contains($value, '//')) {
            return null;
        }

        return $value;
    }

    /**
     * Ikon centang/titik untuk checkbox & radio, warnanya mengikuti
     * --primary-foreground (supaya tetap terbaca di mode gelap).
     *
     * @return array<string, string>
     */
    protected function checkImages(string $foreground): array
    {
        try {
            $hex = Color::convertToHex($foreground);
        } catch (Throwable) {
            $hex = '#ffffff';
        }

        if (! preg_match('/^#[0-9a-f]{6}$/i', $hex)) {
            $hex = '#ffffff';
        }

        $fill = '%23' . substr($hex, 1);

        $svg = fn (string $body): string => 'url("data:image/svg+xml,%3Csvg viewBox=\'0 0 16 16\' fill=\'' . $fill . '\' xmlns=\'http://www.w3.org/2000/svg\'%3E' . $body . '%3C/svg%3E")';

        return [
            'sc-check-image' => $svg('%3Cpath d=\'M12.207 4.793a1 1 0 0 1 0 1.414l-5 5a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L6.5 9.086l4.293-4.293a1 1 0 0 1 1.414 0z\'/%3E'),
            'sc-indeterminate-image' => $svg('%3Cpath d=\'M4.5 6.75a1.25 1.25 0 0 0 0 2.5h7a1.25 1.25 0 0 0 0-2.5h-7z\'/%3E'),
            'sc-radio-image' => $svg('%3Ccircle cx=\'8\' cy=\'8\' r=\'3\'/%3E'),
        ];
    }

    /**
     * @param  array<string, string>  $tokens
     */
    protected function declarations(array $tokens): string
    {
        $css = '';

        foreach ($tokens as $name => $value) {
            // Cegah nilai yang bisa "keluar" dari blok <style>.
            $value = str_replace(['<', '>', '{', '}', ';'], '', $value);
            $name = preg_replace('/[^a-z0-9-]/i', '', $name);

            $css .= "--{$name}:{$value};";
        }

        return $css;
    }
}
