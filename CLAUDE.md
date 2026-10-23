# CLAUDE.md — mehuli/filament-shadcn

Package tema Filament × shadcn/ui milik PT. Mehuli Data Media.

## Aturan kerja
- Balas dalam Bahasa Indonesia.
- Hindari hard-code: semua yang bisa berubah (warna, label, daftar, ukuran, path) harus lewat config/.env/terjemahan.
- Teks UI lewat `resources/lang/{id,en}/filament-shadcn.php`.

## Stack
- PHP 8.2+, Laravel 11/12/13, Filament 5 (kompatibel 4), Livewire 4, Tailwind v4.
- CSS tanpa @layer di `resources/css/*.css`, diimpor oleh `index.css`; hook ke kelas `fi-*` Filament.
- Token shadcn (`--background`, `--primary`, …) dibuat oleh `src/Support/TokenBuilder.php`, dicetak di `<head>` lewat render hook.

## Struktur penting
- `src/ShadcnPlugin.php` — API fluent, mendaftarkan halaman & render hook.
- `src/Support/ThemeSettings.php` — urutan prioritas: config/.env → kode plugin → halaman Tampilan (JSON di disk `local`).
- `src/Pages/Appearance.php` — halaman Tampilan. Akses: `->canCustomizeAppearance()`, bawaan hanya `APP_ENV=local`.
- `src/Commands/InstallCommand.php` — `php artisan shadcn:install [--panel=] [--config] [--force]`.
- `resources/views/components/` — 28 komponen Blade `<x-shadcn::…>` (Alpine.js).
- `stubs/` — theme.css & contoh AdminPanelProvider.

## Keamanan (jangan dilonggarkan)
- Nama font divalidasi `ThemeSettings::FONT_NAME_PATTERN` (Filament mencetaknya tanpa escape di `<style>`).
- Nilai token kustom lewat `TokenBuilder::safeTokenValue()` (tanpa url(), kutip, titik dua, `//`).
- Warna utama kustom: `ThemeSettings::COLOR_VALUE_PATTERN`.
- Upload gambar login tanpa SVG (`appearance_page.image_types`).
- `href` komponen lewat `Support\SafeUrl::filter()` (tolak javascript:, data:, …).
- Teks bawaan komponen dari terjemahan `ui.*`, jangan ditulis langsung di Blade.

## Status
- v1.0.2 (lihat CHANGELOG.md). Sudah diuji di proyek uji `../shadcn-playground` (Laravel 13.38, Filament 5.10.3, path repository symlink): `shadcn:install`, `npm run build`, halaman Tampilan (simpan/reset/validasi font), login `split` + upload (SVG ditolak, gambar lama dihapus saat diganti/reset), akses production (`canCustomizeAppearance`), ponsel 390px & tablet 820px tanpa overflow.
- Repo GitHub: `mehuli-id/filament-shadcn` (privat), branch `main`, tag `v1.0.2`.
- Starter kit terpisah (repo belum dibuat per 2026-10-23): `mehuli-id/starter-kit` (Laravel 13 + Filament 5 + package ini, locale id, Asia/Jakarta, admin via seeder `ADMIN_*`, akses panel `is_admin`).

## Pekerjaan lanjutan yang tercatat
- `composer require mehuli/filament-shadcn:^1.0` lewat repo `vcs` sudah diuji (v1.0.2). Berikutnya: buat starter kit dan pakai `^1.0.2`.
- Catatan uji: `php artisan serve` mewarisi `APP_ENV` lama setelah `.env` diubah, jadi restart penuh server saat menguji production.
