<?php

namespace Mehuli\FilamentShadcn\Support;

use Filament\Support\Colors\Color;

/**
 * Hasil akhir pengaturan tema setelah menggabungkan:
 * config → pengaturan plugin (kode) → halaman Tampilan (JSON).
 */
class ThemeSettings
{
    /**
     * @param  array<string, mixed>  $values
     */
    public function __construct(
        protected array $values,
    ) {}

    /**
     * @param  array<string, mixed>  $pluginOverrides
     * @param  array<string, mixed>  $stored
     */
    public static function resolve(array $pluginOverrides = [], array $stored = []): static
    {
        $config = (array) config('filament-shadcn', []);

        // Nilai kosong di .env (mis. SHADCN_STYLE=) dianggap "pakai bawaan".
        $or = static fn (mixed $value, mixed $default): mixed => ($value === null || $value === '') ? $default : $value;

        $values = [
            'style' => $or($config['style'] ?? null, 'nova'),
            'base_color' => $or($config['base_color'] ?? null, 'zinc'),
            'primary' => $or($config['primary'] ?? null, 'default'),
            'colors' => array_merge([
                'danger' => 'red',
                'success' => 'emerald',
                'warning' => 'amber',
                'info' => 'blue',
            ], array_filter((array) ($config['colors'] ?? []))),
            'radius' => $config['radius'] ?? null,
            'radii' => array_filter((array) ($config['radii'] ?? []), static fn (mixed $value): bool => $value !== null && $value !== ''),
            'density' => $config['density'] ?? null,
            'font_sans' => $config['font']['sans'] ?? null,
            'font_mono' => $config['font']['mono'] ?? null,
            'font_heading' => $config['font']['heading'] ?? null,
            'primary_palette' => array_filter((array) ($config['primary_palette'] ?? [])),
            'extra_colors' => array_filter((array) ($config['extra_colors'] ?? [])),
            'typography' => array_filter((array) ($config['typography'] ?? []), static fn (mixed $value): bool => $value !== null && $value !== ''),
            'font_provider' => $config['font']['provider'] ?? null,
            'sidebar' => $or($config['layout']['sidebar'] ?? null, 'inset'),
            'auth_layout' => $or($config['auth']['layout'] ?? null, 'muted'),
            'auth_image' => $config['auth']['image'] ?? null,
            'auth_image_position' => $or($config['auth']['image_position'] ?? null, 'end'),
            'auth_tagline' => $config['auth']['tagline'] ?? null,
            'auth_tagline_author' => $config['auth']['tagline_author'] ?? null,
            'charts' => $config['charts'] ?? null,
            'tokens' => [
                'light' => (array) ($config['tokens']['light'] ?? []),
                'dark' => (array) ($config['tokens']['dark'] ?? []),
            ],
        ];

        foreach ([$pluginOverrides, $stored] as $layer) {
            foreach ($layer as $key => $value) {
                if ($value === null || $value === '') {
                    continue;
                }

                if (in_array($key, ['colors', 'radii', 'primary_palette', 'extra_colors', 'typography'], true) && is_array($value)) {
                    // array_replace (bukan array_merge) supaya kunci angka seperti shade 600 tidak dinomori ulang;
                    // hanya null/'' yang dibuang, angka 0 & false tetap nilai yang sah.
                    $values[$key] = array_replace($values[$key], array_filter($value, static fn (mixed $item): bool => $item !== null && $item !== ''));

                    continue;
                }

                if ($key === 'tokens' && is_array($value)) {
                    $values['tokens']['light'] = array_merge($values['tokens']['light'], (array) ($value['light'] ?? []));
                    $values['tokens']['dark'] = array_merge($values['tokens']['dark'], (array) ($value['dark'] ?? []));

                    continue;
                }

                $values[$key] = $value;
            }
        }

        if (! array_key_exists($values['style'], Presets::styles())) {
            $values['style'] = 'nova';
        }

        return new static($values);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->values[$key] ?? $default;
    }

    /**
     * @return array<string, mixed>
     */
    public function style(): array
    {
        return Presets::styles()[$this->values['style']];
    }

    public function styleName(): string
    {
        return $this->values['style'];
    }

    public function radius(): float
    {
        $radius = $this->values['radius'];

        return is_numeric($radius) ? max(0, (float) $radius) : (float) $this->style()['radius'];
    }

    /**
     * Radius kartu / kontrol tersendiri (rem). null = turunan radius dasar (perilaku bawaan).
     */
    public function radiusFor(string $part): ?float
    {
        $value = $this->values['radii'][$part] ?? null;

        return is_numeric($value) ? max(0, (float) $value) : null;
    }

    public function density(): float
    {
        $density = $this->values['density'];

        return is_numeric($density) ? min(1.25, max(0.75, (float) $density)) : (float) $this->style()['density'];
    }

    public function fontSans(): string
    {
        return static::safeFontName($this->values['font_sans'] ?? null) ?? $this->style()['font'];
    }

    public function fontMono(): string
    {
        return static::safeFontName($this->values['font_mono'] ?? null) ?? $this->style()['mono'];
    }

    /**
     * Font judul (halaman, section, modal, angka statistik). null = sama dengan font isi.
     */
    public function fontHeading(): ?string
    {
        return static::safeFontName($this->values['font_heading'] ?? null);
    }

    /**
     * Tebal judul 100–900 (kelipatan 100). null = bawaan gaya.
     */
    public function headingWeight(): ?int
    {
        $weight = $this->values['typography']['heading_weight'] ?? null;

        if (! is_numeric($weight)) {
            return null;
        }

        $weight = (int) $weight;

        return ($weight >= 100 && $weight <= 900 && $weight % 100 === 0) ? $weight : null;
    }

    public function uppercaseLabels(): bool
    {
        return filter_var($this->values['typography']['uppercase_labels'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    public function tabularNums(): bool
    {
        return filter_var($this->values['typography']['tabular_nums'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Nama font dicetak Filament apa adanya di dalam <style> ('{!! … !!}'),
     * jadi hanya huruf, angka, spasi, titik, dan tanda hubung yang diterima.
     */
    public static function safeFontName(mixed $name): ?string
    {
        if (! is_string($name)) {
            return null;
        }

        $name = trim($name);

        return preg_match(static::FONT_NAME_PATTERN, $name) ? $name : null;
    }

    public const FONT_NAME_PATTERN = '/^[\pL\pN][\pL\pN .\-]{0,63}$/u';

    /**
     * Kode warna yang diterima: #rgb, #rrggbb, rgb(r, g, b), oklch(l c h).
     */
    public const COLOR_VALUE_PATTERN = '/^(#[0-9a-f]{3}|#[0-9a-f]{6}|rgb\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*\)|oklch\(\s*[\d.]+%?\s+[\d.]+\s+[\d.]+\s*\))$/i';

    public function fontProvider(): ?string
    {
        return $this->values['font_provider'] ?: null;
    }

    /**
     * @return array<int, string>
     */
    public function basePalette(): array
    {
        $base = Presets::baseColors();

        return $base[$this->values['base_color']] ?? Presets::palette((string) $this->values['base_color'], $base['zinc']);
    }

    public function isDefaultPrimary(): bool
    {
        $primary = strtolower(trim((string) $this->values['primary']));

        if (in_array($primary, ['', 'default', 'base', 'inverted'], true)) {
            return true;
        }

        // Nama warna yang tidak dikenal atau kode warna yang tidak valid → bawaan.
        return ! array_key_exists($primary, Presets::accentColors() + Presets::baseColors())
            && ! preg_match(static::COLOR_VALUE_PATTERN, $primary);
    }

    public function primaryValue(): string
    {
        return (string) $this->values['primary'];
    }

    /**
     * @return array<int, string>
     */
    public function primaryPalette(): array
    {
        if ($this->isDefaultPrimary()) {
            return $this->basePalette();
        }

        return $this->withPaletteOverrides(Presets::palette($this->primaryValue(), $this->basePalette()));
    }

    /**
     * Shade palet utama yang diberikan sendiri (mis. palet Amber yang digeser). Shade yang tidak
     * diisi atau bukan kode warna yang sah memakai hasil hitungan biasa.
     *
     * @param  array<int, string>  $palette
     * @return array<int, string>
     */
    protected function withPaletteOverrides(array $palette): array
    {
        foreach ((array) $this->values['primary_palette'] as $shade => $color) {
            if (! array_key_exists((int) $shade, $palette) || ! is_string($color) || ! Presets::isColorValue($color)) {
                continue;
            }

            $palette[(int) $shade] = Color::convertToOklch(trim($color));
        }

        return $palette;
    }

    /**
     * Warna Filament tambahan di luar 6 bawaan, mis. ['teal' => 'teal', 'merek' => '#7c3aed'].
     * Nama: huruf kecil/angka/tanda hubung, tidak boleh menimpa warna bawaan.
     *
     * @return array<string, array<int, string>>
     */
    public function extraColors(): array
    {
        $reserved = ['gray', 'primary', 'danger', 'success', 'warning', 'info'];
        $colors = [];

        foreach ((array) $this->values['extra_colors'] as $name => $value) {
            $name = strtolower(trim((string) $name));

            if (! preg_match(static::COLOR_NAME_PATTERN, $name) || in_array($name, $reserved, true) || ! is_string($value)) {
                continue;
            }

            $key = strtolower(trim($value));

            if (! array_key_exists($key, Presets::accentColors() + Presets::baseColors()) && ! Presets::isColorValue($value)) {
                continue;
            }

            $colors[$name] = Presets::palette($value);
        }

        return $colors;
    }

    public const COLOR_NAME_PATTERN = '/^[a-z][a-z0-9-]{0,31}$/';

    /**
     * @return array<int, string>
     */
    public function statusPalette(string $name): array
    {
        $fallbacks = ['danger' => 'red', 'success' => 'emerald', 'warning' => 'amber', 'info' => 'blue'];

        return Presets::palette((string) ($this->values['colors'][$name] ?? $fallbacks[$name] ?? 'zinc'));
    }

    /**
     * Palet untuk `$panel->colors()`.
     *
     * @return array<string, array<int, string>>
     */
    public function filamentColors(): array
    {
        return [
            'gray' => $this->basePalette(),
            'primary' => $this->primaryPalette(),
            'danger' => $this->statusPalette('danger'),
            'success' => $this->statusPalette('success'),
            'warning' => $this->statusPalette('warning'),
            'info' => $this->statusPalette('info'),
        ] + $this->extraColors();
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->values;
    }
}
