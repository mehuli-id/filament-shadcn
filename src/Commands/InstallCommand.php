<?php

namespace Mehuli\FilamentShadcn\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

/**
 * php artisan shadcn:install [--panel=admin] [--config] [--force]
 *
 * Memasang tema shadcn/ui ke satu panel Filament:
 *   1. membuat resources/css/filament/{panel}/theme.css dari stub
 *   2. menambahkan file itu ke input vite.config.js
 *   3. menambahkan ->viteTheme() dan ->plugin(ShadcnPlugin::make()) ke Panel Provider
 *      (dan menghapus ->colors([...]) bawaan, karena warnanya diatur plugin)
 *   4. (opsional) menerbitkan config/filament-shadcn.php
 *
 * Aman dijalankan berulang: langkah yang sudah terpasang dilewati.
 */
class InstallCommand extends Command
{
    protected $signature = 'shadcn:install
        {--panel= : ID panel Filament (bawaan: admin)}
        {--provider= : Path Panel Provider kalau bukan app/Providers/Filament/{Panel}PanelProvider.php}
        {--config : Terbitkan config/filament-shadcn.php}
        {--force : Timpa theme.css yang sudah ada}';

    protected $description = 'Pasang tema Filament × shadcn/ui ke panel Filament';

    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $panel = $this->option('panel')
            ?: ($this->input->isInteractive() ? $this->ask('ID panel Filament', 'admin') : 'admin');
        $panel = Str::kebab($panel);

        $providerPath = $this->option('provider')
            ?: app_path('Providers/Filament/' . Str::studly($panel) . 'PanelProvider.php');

        if (! $this->files->exists($providerPath)) {
            $this->components->warn("Panel Provider tidak ditemukan: {$this->relative($providerPath)}");

            if (! $this->input->isInteractive() || ! $this->confirm("Buat panel \"{$panel}\" sekarang (make:filament-panel)?", true)) {
                $this->components->error('Buat panel dulu: php artisan make:filament-panel ' . $panel);

                return self::FAILURE;
            }

            $this->call('make:filament-panel', ['id' => $panel]);

            if (! $this->files->exists($providerPath)) {
                $this->components->error('Panel Provider masih belum ada. Hentikan.');

                return self::FAILURE;
            }
        }

        $themePath = "resources/css/filament/{$panel}/theme.css";

        $this->components->task("Membuat {$themePath}", fn () => $this->writeTheme($themePath));
        $this->components->task('Menambahkan tema ke vite.config.js', fn () => $this->updateVite($themePath));
        $this->components->task("Memasang plugin di {$this->relative($providerPath)}", fn () => $this->updateProvider($providerPath, $themePath));

        if ($this->option('config') || ($this->input->isInteractive() && $this->confirm('Terbitkan config/filament-shadcn.php?', false))) {
            $this->callSilently('vendor:publish', ['--tag' => 'filament-shadcn-config']);
            $this->components->info('config/filament-shadcn.php diterbitkan.');
        }

        $this->newLine();
        $this->components->info('Tema shadcn/ui terpasang. Langkah berikutnya:');
        $this->components->bulletList([
            'npm install && npm run build   (atau npm run dev)',
            'Atur warna/gaya lewat .env (SHADCN_STYLE, SHADCN_BASE_COLOR, SHADCN_PRIMARY, …)',
            'atau buka halaman Tampilan di panel untuk mengubahnya tanpa kode.',
        ]);

        return self::SUCCESS;
    }

    protected function writeTheme(string $themePath): bool
    {
        $target = base_path($themePath);

        if ($this->files->exists($target) && ! $this->option('force')) {
            return true; // sudah ada — biarkan
        }

        $stub = $this->files->get(__DIR__ . '/../../stubs/theme.css');
        $this->files->ensureDirectoryExists(dirname($target));
        $this->files->put($target, str_replace('filament/admin/theme.css', "filament/{$this->panelFromPath($themePath)}/theme.css", $stub));

        return true;
    }

    protected function updateVite(string $themePath): bool
    {
        foreach (['vite.config.js', 'vite.config.ts', 'vite.config.mjs'] as $name) {
            $path = base_path($name);

            if (! $this->files->exists($path)) {
                continue;
            }

            $contents = $this->files->get($path);

            if (str_contains($contents, $themePath)) {
                return true;
            }

            // input: ['resources/css/app.css', 'resources/js/app.js']
            $updated = preg_replace_callback(
                '/(input\s*:\s*\[)(.*?)(\])/s',
                function (array $m) use ($themePath): string {
                    $items = rtrim($m[2]);
                    $multiline = str_contains($items, "\n");
                    $sep = $items === '' ? '' : (str_ends_with($items, ',') ? '' : ',');

                    return $multiline
                        ? $m[1] . $items . $sep . "\n                '{$themePath}',\n            " . $m[3]
                        : $m[1] . $items . $sep . ($items === '' ? '' : ' ') . "'{$themePath}'" . $m[3];
                },
                $contents,
                1,
                $count,
            );

            if (! $count) {
                $this->components->warn("Tidak menemukan `input: [...]` di {$name}. Tambahkan '{$themePath}' secara manual.");

                return false;
            }

            $this->files->put($path, $updated);

            return true;
        }

        $this->components->warn("vite.config.js tidak ditemukan. Tambahkan '{$themePath}' ke input Vite secara manual.");

        return false;
    }

    protected function updateProvider(string $path, string $themePath): bool
    {
        $contents = $this->files->get($path);

        // use statement
        if (! str_contains($contents, 'Mehuli\\FilamentShadcn\\ShadcnPlugin')) {
            $use = 'use Mehuli\\FilamentShadcn\\ShadcnPlugin;';

            $contents = preg_match('/^use /m', $contents)
                ? preg_replace('/^use /m', $use . "\nuse ", $contents, 1)
                : preg_replace('/^(namespace [^;]+;)\n/m', "$1\n\n{$use}\n", $contents, 1);
        }

        // Warna diatur plugin: hapus ->colors([...]) bawaan dari make:filament-panel.
        $contents = preg_replace('/\n\s*->colors\(\[.*?\]\)/s', '', $contents, 1);

        if (! str_contains($contents, 'Color::')) {
            $contents = str_replace("use Filament\\Support\\Colors\\Color;\n", '', $contents);
        }

        $additions = [];

        if (! str_contains($contents, '->viteTheme(')) {
            $additions[] = "->viteTheme('{$themePath}')";
        }

        if (! str_contains($contents, 'ShadcnPlugin::make()')) {
            $additions[] = '->plugin(ShadcnPlugin::make())';
        }

        if ($additions) {
            // Sisipkan setelah ->path('...') (atau ->id('...') bila tidak ada path).
            // Baris utuh ->path(...) — boleh berisi tanda kurung, mis. ->path(config('panel.path')).
            $anchor = preg_match('/^(\s*)->path\(.*$/m', $contents) ? 'path' : 'id';

            $contents = preg_replace_callback(
                '/^(\s*)->' . $anchor . '\(.*$/m',
                fn (array $m): string => $m[0] . implode('', array_map(fn ($line) => "\n" . $m[1] . $line, $additions)),
                $contents,
                1,
                $count,
            );

            if (! $count) {
                $this->components->warn('Tidak bisa menyisipkan otomatis. Tambahkan secara manual: ' . implode(' ', $additions));

                return false;
            }
        }

        $this->files->put($path, $contents);

        return true;
    }

    protected function panelFromPath(string $themePath): string
    {
        return basename(dirname($themePath));
    }

    protected function relative(string $path): string
    {
        return ltrim(Str::after($path, base_path()), '/');
    }
}
