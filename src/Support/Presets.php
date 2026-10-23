<?php

namespace Mehuli\FilamentShadcn\Support;

use Filament\Support\Colors\Color;

/**
 * Daftar pilihan bawaan: gaya (style), warna dasar, dan warna aksen.
 * Semua nilai di sini hanya "default" — bisa ditimpa lewat config,
 * plugin, atau halaman Tampilan.
 */
class Presets
{
    /**
     * Gaya komponen, padanan style shadcn/ui.
     *
     * radius       : radius dasar (rem)
     * density      : pengali skala jarak Tailwind (--spacing)
     * control      : tinggi tombol & input (rem)
     * card_padding : padding kartu/section (rem)
     * pill         : tombol, input & badge berbentuk pil
     * font / mono  : font bawaan gaya
     * text         : ukuran teks dasar komponen (rem)
     *
     * Label tiap gaya ada di terjemahan `appearance.styles.{nama}`.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function styles(): array
    {
        return [
            'vega' => [
                'radius' => 0.625,
                'density' => 1.0,
                'control' => 2.25,
                'card_padding' => 1.5,
                'pill' => false,
                'font' => 'Geist',
                'mono' => 'Geist Mono',
                'text' => 0.875,
            ],
            'nova' => [
                'radius' => 0.5,
                'density' => 0.92,
                'control' => 2.0,
                'card_padding' => 1.0,
                'pill' => false,
                'font' => 'Geist',
                'mono' => 'Geist Mono',
                'text' => 0.875,
            ],
            'maia' => [
                'radius' => 1.0,
                'density' => 1.0,
                'control' => 2.25,
                'card_padding' => 1.5,
                'pill' => true,
                'font' => 'Geist',
                'mono' => 'Geist Mono',
                'text' => 0.875,
            ],
            'lyra' => [
                'radius' => 0.0,
                'density' => 0.95,
                'control' => 2.0,
                'card_padding' => 1.0,
                'pill' => false,
                'font' => 'Geist Mono',
                'mono' => 'Geist Mono',
                'text' => 0.8125,
            ],
            'mira' => [
                'radius' => 0.375,
                'density' => 0.85,
                'control' => 1.75,
                'card_padding' => 0.75,
                'pill' => false,
                'font' => 'Geist',
                'mono' => 'Geist Mono',
                'text' => 0.8125,
            ],
        ];
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function baseColors(): array
    {
        $colors = [];

        // Mauve, Olive, Mist & Taupe baru ada di Filament versi terbaru.
        foreach (['neutral', 'zinc', 'slate', 'stone', 'gray', 'mauve', 'olive', 'mist', 'taupe'] as $name) {
            $constant = Color::class . '::' . ucfirst($name);

            if (defined($constant)) {
                $colors[$name] = constant($constant);
            }
        }

        return $colors;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function accentColors(): array
    {
        return [
            'red' => Color::Red,
            'orange' => Color::Orange,
            'amber' => Color::Amber,
            'yellow' => Color::Yellow,
            'lime' => Color::Lime,
            'green' => Color::Green,
            'emerald' => Color::Emerald,
            'teal' => Color::Teal,
            'cyan' => Color::Cyan,
            'sky' => Color::Sky,
            'blue' => Color::Blue,
            'indigo' => Color::Indigo,
            'violet' => Color::Violet,
            'purple' => Color::Purple,
            'fuchsia' => Color::Fuchsia,
            'pink' => Color::Pink,
            'rose' => Color::Rose,
        ];
    }

    /**
     * Warna grafik bawaan shadcn/ui.
     *
     * @return array{light: list<string>, dark: list<string>}
     */
    public static function charts(): array
    {
        return [
            'light' => [
                'oklch(0.646 0.222 41.116)',
                'oklch(0.6 0.118 184.704)',
                'oklch(0.398 0.07 227.392)',
                'oklch(0.828 0.189 84.429)',
                'oklch(0.769 0.188 70.08)',
            ],
            'dark' => [
                'oklch(0.488 0.243 264.376)',
                'oklch(0.696 0.17 162.48)',
                'oklch(0.769 0.188 70.08)',
                'oklch(0.627 0.265 303.9)',
                'oklch(0.645 0.246 16.439)',
            ],
        ];
    }

    /**
     * Ubah nama warna atau kode warna menjadi palet 50–950.
     *
     * @return array<int, string>
     */
    public static function palette(string $color, ?array $fallback = null): array
    {
        $key = strtolower(trim($color));

        $named = static::accentColors() + static::baseColors();

        if (array_key_exists($key, $named)) {
            return $named[$key];
        }

        if (static::isColorValue($key)) {
            return Color::generatePalette($color);
        }

        return $fallback ?? Color::Zinc;
    }

    public static function isColorValue(string $color): bool
    {
        $color = strtolower(trim($color));

        return str_starts_with($color, '#')
            || str_starts_with($color, 'rgb(')
            || str_starts_with($color, 'oklch(');
    }
}
