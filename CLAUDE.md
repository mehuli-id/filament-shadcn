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
- `docs/mockup/` — mockup desain acuan: `gambar/*.png` (cepat dilihat) dan `sumber/*.dc.html` (interaktif, buka di browser). Tampilan package harus mengikuti mockup ini.

## Keamanan (jangan dilonggarkan)
- Nama font divalidasi `ThemeSettings::FONT_NAME_PATTERN` (Filament mencetaknya tanpa escape di `<style>`).
- Nilai token kustom lewat `TokenBuilder::safeTokenValue()` (tanpa url(), kutip, titik dua, `//`).
- Warna utama kustom: `ThemeSettings::COLOR_VALUE_PATTERN`.
- Upload gambar login tanpa SVG (`appearance_page.image_types`).
- `href` komponen lewat `Support\SafeUrl::filter()` (tolak javascript:, data:, …).
- Teks bawaan komponen dari terjemahan `ui.*`, jangan ditulis langsung di Blade.

## Status
- v1.1.0 (lihat CHANGELOG.md): tampilan mengikuti `docs/mockup/` — shell (sidebar 16rem, header di kartu konten), `Widgets\Stat`, `Widgets\ChartWidget`, tabel, login muted/split + `Pages\Auth\Login`, halaman Contoh dasbor/form. Diuji di `../shadcn-playground` (Laravel 13.38, Filament 5.10.3) di 1440/820/390px, mode gelap, gaya maia, `->shell(false)`.
- Repo GitHub: `mehuli-id/filament-shadcn` (privat), branch `main`, tag terakhir `v1.1.3`.
- Starter kit terpisah: `mehuli-id/starter-kit` (privat, template repository, lokal di `../starter-kit`; Laravel 13 + Filament 5 + package ini, locale id, Asia/Jakarta, admin via seeder `ADMIN_*`, akses panel `is_admin`).
- `resources/views/widgets/chart.blade.php` adalah salinan view chart Filament 5.x (hanya filter diganti tombol) — periksa ulang saat memperbarui Filament.
- Selisih yang tersisa dengan mockup: tabel ponsel belum jadi daftar kartu; toolbar tabel (cari/Kolom/Tambah) belum satu baris dengan tab; kartu pengguna di sidebar belum menampilkan email; tombol masuk sosial (Google/Microsoft) belum ada.

## Pekerjaan lanjutan yang tercatat
- `composer require` lewat repo `vcs` sudah diuji (v1.0.2) dan dipakai starter kit (kini `^1.1.0`). Setiap rilis baru: tag di sini, lalu `composer update mehuli/filament-shadcn` di starter kit.
- Catatan uji: `php artisan serve` mewarisi `APP_ENV` lama setelah `.env` diubah, jadi restart penuh server saat menguji production.
