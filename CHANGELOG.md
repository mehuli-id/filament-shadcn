# Changelog

## v1.0.2 — 2026-10-23

Perbaikan (hasil uji di Laravel 13.38 + Filament 5.10.3 + Livewire 4.4.8):
- Label gaya (Vega, Nova, Maia, Lyra, Mira) di halaman Tampilan pindah ke terjemahan `appearance.styles.*`; sebelumnya tetap berbahasa Indonesia saat locale `en`.
- Gaya Maia (pil): KeyValue, Textarea, RichEditor, dan MarkdownEditor tidak lagi ikut berbentuk pil; memakai radius kartu.
- URL gambar login dibuat saat render dari `auth_image_path`, bukan dari URL absolut yang tersimpan, jadi tetap benar bila `APP_URL`/domain berubah.
- Section "Lanjutan" di halaman Tampilan: `->collapsible()->collapsed()` dihapus karena Filament 5 mengabaikannya pada section `->aside()`.

## v1.0.1 — 2026-10-23

Keamanan:
- Nama font dari halaman Tampilan divalidasi (mencegah XSS lewat `<style>` Filament).
- Gambar login tidak lagi menerima SVG; ada batas ukuran.
- Nilai token kustom dibatasi (tanpa `url()` dan sejenisnya); warna utama kustom divalidasi.
- Halaman Tampilan bawaannya hanya untuk `APP_ENV=local` sampai `->canCustomizeAppearance()` diatur.
- Atribut `href` di komponen `<x-shadcn::…>` dan URL item `command` menolak `javascript:`, `data:`, dan sejenisnya (`blocked_url_schemes`).

Perbaikan:
- `shadcn:install` merusak Panel Provider bila `->path()` berisi tanda kurung (mis. `->path(config('panel.path'))`).
- Gambar login lama dihapus saat diganti atau saat tampilan di-reset; foldernya bisa diatur (`appearance_page.image_directory`).
- Teks bawaan komponen (spinner, carousel, command, resizable, message-scroller, questionnaire) pindah ke file terjemahan `ui.*`, tidak lagi tertulis di kode.

## v1.0.0 — 2026-10-23

- Rilis pertama: plugin `ShadcnPlugin`, token & gaya shadcn/ui (vega, nova, maia, lyra, mira), halaman Tampilan, halaman Showcase.
- 28 komponen Blade `<x-shadcn::…>`, termasuk tampilan ponsel & tablet.
- Perintah `php artisan shadcn:install`.
