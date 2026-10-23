# Changelog

## v1.1.0 — 2026-10-23

Tampilan mengikuti `docs/mockup/`:
- Shell baru (bawaan aktif, `->shell(false)` / `SHADCN_SHELL=false` untuk topbar Filament): sidebar 16rem dengan blok merek dan menu pengguna di kaki sidebar, header di dalam kartu konten (judul/breadcrumb, pencarian, pilihan tema, lonceng, tautan), header ponsel dengan avatar.
- Opsi baru: `->sidebarWidth()`, `->brand(icon:, subtitle:)`, `->headerLinks()` (+ `SHADCN_SIDEBAR_WIDTH`, `SHADCN_BRAND_ICON`, `SHADCN_BRAND_SUBTITLE`). Lebar sidebar divalidasi (`ShadcnPlugin::SIDEBAR_WIDTH_PATTERN`).
- `Widgets\Stat`: badge tren, baris keterangan + petunjuk; versi ringkas dua kolom di ponsel.
- `Widgets\ChartWidget`: warna per seri dari `--chart-1…5`, isian gradasi, filter bersegmen.
- Token baru per gaya: `--sc-page-padding`, `--sc-gap`, `--sc-row-padding`, `--sc-radius-inset`; `--sc-radius-card` kini radius + 2px.
- Warna grafik 1–2 mengikuti mockup (oranye/teal, juga di mode gelap).
- Tabel: kepala tabel berlatar `--muted`, baris lebih rapat, badge berikon bergaris netral, tombol pagination bergaris.
- Login/daftar/lupa sandi: pilihan tema di kanan atas, merek di atas kartu (muted/plain), layout `split` jadi satu kartu 920px (form + gambar), teks persetujuan (`SHADCN_TERMS_URL`, `SHADCN_PRIVACY_URL`).
- `Pages\Auth\Login`: "Selamat datang kembali", subjudul dengan nama merek, tautan daftar di bawah form. `shadcn:install` mengganti `->login()` dengan halaman ini.
- Tinggi kontrol mengikuti mockup: vega 40px, nova 36px, maia 40px, lyra 36px; minimal 44px di ponsel. Padding kartu vega 20px.
- Label field berwarna foreground; tombol tampilkan kata sandi menyatu di dalam input.
- Halaman contoh baru (aktif bersama Showcase): **Contoh dasbor** dan **Contoh form**, disusun seperti artboard mockup untuk perbandingan di 1440/820/390px.
- Tabs rata kiri (seperti shadcn); angka di tab memakai badge abu-abu.

Perbaikan:
- `shadcn:install` menyisipkan `use …ShadcnPlugin;` setelah import terakhir (sebelumnya sebelum import pertama).
- Kartu konten inset tanpa topbar kini punya jarak atas (aturan `margin-top` sebelumnya kalah spesifisitas).

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
