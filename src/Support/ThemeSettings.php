<?php

namespace Mehuli\FilamentShadcn\Support;

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
            'density' => $config['density'] ?? null,
            'font_sans' => $config['font']['sans'] ?? null,
            'font_mono' => $config['font']['mono'] ?? null,
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

                if (in_array($key, ['colors'], true) && is_array($value)) {
                    $values[$key] = array_merge($values[$key], array_filter($value));

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

        return Presets::palette($this->primaryValue(), $this->basePalette());
    }

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
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->values;
    }
}
