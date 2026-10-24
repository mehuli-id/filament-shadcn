# CLAUDE.md — mehuli/filament-shadcn

Package tema Filament × shadcn/ui milik PT. Mehuli Data Media.

## Aturan kerja
- Balas dalam Bahasa Indonesia.
- Hindari hard-code: semua yang bisa berubah (warna, label, daftar, ukuran, path) harus lewat config/.env/terjemahan.
- Teks UI lewat `resources/lang/{id,en}/filament-shadcn.php`.
- Sebelum setiap rilis/tag: `vendor/bin/phpunit` harus lolos, lalu uji tampilan `cd tests/ui && npm test` (Chromium + Safari) dan `npm run selftest`; keduanya harus lolos. Pengguna memeriksa hasil di Safari.
- Setiap bug tampilan yang ditemukan pengguna: perbaiki, lalu tambahkan aturannya di `tests/ui/checks.js` + kasusnya di `tests/ui/selftest.mjs` supaya tidak terulang.

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
- `tests/Unit`, `tests/Feature` — tes PHP (Testbench): keamanan, installer, `ViewCopiesTest` (sha1 view Filament yang disalin).
- `.github/dependabot.yml` — PR pembaruan mingguan (Composer, npm tests/ui) & bulanan (Actions).
- `.github/workflows/tests.yml` — CI: tes PHP 8.3/8.4 + audit, lalu aplikasi uji baru (`tests/ui/ci-app.sh`) + uji tampilan Chromium.
- `tests/ui/` — uji tampilan otomatis (aturan di `checks.js`, runner `run.mjs` untuk Chromium & Safari, uji-diri `selftest.mjs`).
- `docs/mockup/` — mockup desain acuan: `gambar/*.png` (cepat dilihat) dan `sumber/*.dc.html` (interaktif, buka di browser). Tampilan package harus mengikuti mockup ini.

## Keamanan (jangan dilonggarkan)
- Nama font divalidasi `ThemeSettings::FONT_NAME_PATTERN` (Filament mencetaknya tanpa escape di `<style>`).
- Nilai token kustom lewat `TokenBuilder::safeTokenValue()` (tanpa url(), kutip, titik dua, `//`).
- Warna utama kustom: `ThemeSettings::COLOR_VALUE_PATTERN`.
- Upload gambar login tanpa SVG (`appearance_page.image_types`).
- Semua `href` dari data/config lewat `Support\SafeUrl::filter()` (daftar izin `allowed_url_schemes`).
- Halaman Showcase: `Pages\Concerns\AuthorizesShowcase` (hanya yang boleh mengatur tampilan). Upload apa pun: `acceptedFileTypes(appearance_page.image_types)`, jangan `->image()` saja (menerima SVG).
- Teks bawaan komponen dari terjemahan `ui.*`, jangan ditulis langsung di Blade.

## Status
- v1.1.0 (lihat CHANGELOG.md): tampilan mengikuti `docs/mockup/` — shell (sidebar 16rem, header di kartu konten), `Widgets\Stat`, `Widgets\ChartWidget`, tabel, login muted/split + `Pages\Auth\Login`, halaman Contoh dasbor/form. Diuji di `../shadcn-playground` (Laravel 13.38, Filament 5.10.3) di 1440/820/390px, mode gelap, gaya maia, `->shell(false)`.
- Repo GitHub: `mehuli-id/filament-shadcn` (**publik**, lisensi MIT), branch `main`, tag terakhir `v1.1.8`. Commit baru memakai email kantor `support@mehuli.id`.
- Starter kit terpisah: `mehuli-id/starter-kit` (publik, MIT, template repository, lokal di `../starter-kit`; Laravel 13 + Filament 5 + package ini, locale id, Asia/Jakarta, admin via seeder `ADMIN_*`, akses panel `is_admin`).
- `resources/views/widgets/chart.blade.php` adalah salinan view chart Filament 5.x (hanya filter diganti tombol) — periksa ulang saat memperbarui Filament.
- Selisih kecil yang tersisa dengan mockup: di daftar ponsel nomor & status belum satu baris; ikon menu pengguna masih chevron (mockup ⋮). Alur OAuth untuk `auth.social` belum ada (hanya tombol).
- `resources/views/overrides/filament-panels/components/user-menu.blade.php` adalah salinan view Filament 5.x (tambah email) — periksa ulang saat memperbarui Filament.

## Pekerjaan lanjutan yang tercatat
- Setiap rilis baru: tag di sini, lalu `composer update mehuli/filament-shadcn` di starter kit (kini `^1.1.8`). CI kedua repo harus hijau.
- Package publik: starter kit & proyek lain memasangnya tanpa token (repo vcs publik; nanti Packagist). Secret `FILAMENT_SHADCN_TOKEN` tidak diperlukan lagi.
- Belum dikerjakan: alur OAuth (Socialite) untuk `auth.social` di starter kit; polesan mockup (kalender ‹ ›, ikon ⋮ menu pengguna, nomor & status satu baris di daftar ponsel).
- Catatan uji: `php artisan serve` mewarisi `APP_ENV` lama setelah `.env` diubah, jadi restart penuh server saat menguji production.
