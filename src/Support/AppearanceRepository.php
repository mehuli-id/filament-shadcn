<?php

namespace Mehuli\FilamentShadcn\Support;

use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Menyimpan pilihan dari halaman "Tampilan" ke sebuah file JSON,
 * jadi tidak perlu migrasi database. Lokasi diatur di config
 * `filament-shadcn.appearance_page.storage_disk` dan `storage_path`.
 */
class AppearanceRepository
{
    /**
     * Kunci yang boleh disimpan dari halaman Tampilan.
     */
    public const KEYS = [
        'style',
        'base_color',
        'primary',
        'colors',
        'radius',
        'density',
        'sidebar',
        'auth_layout',
        'auth_image',
        'auth_image_path',
        'font_sans',
        'font_mono',
        'tokens',
    ];

    /**
     * @var array<string, mixed> | null
     */
    protected ?array $cache = null;

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        try {
            $disk = Storage::disk($this->disk());

            if (! $disk->exists($this->path())) {
                return $this->cache = [];
            }

            $data = json_decode((string) $disk->get($this->path()), true);

            return $this->cache = is_array($data) ? $this->onlyAllowed($data) : [];
        } catch (Throwable) {
            return $this->cache = [];
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function save(array $data): void
    {
        $data = $this->onlyAllowed($data);

        Storage::disk($this->disk())->put(
            $this->path(),
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        );

        $this->cache = $data;
    }

    public function reset(): void
    {
        Storage::disk($this->disk())->delete($this->path());

        $this->cache = [];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function onlyAllowed(array $data): array
    {
        $data = array_intersect_key($data, array_flip(static::KEYS));

        // Nilai kosong berarti "ikut pengaturan bawaan".
        return array_filter($data, fn (mixed $value): bool => $value !== null && $value !== '' && $value !== []);
    }

    protected function disk(): string
    {
        return (string) config('filament-shadcn.appearance_page.storage_disk', 'local');
    }

    protected function path(): string
    {
        return (string) config('filament-shadcn.appearance_page.storage_path', 'filament-shadcn/appearance.json');
    }
}
