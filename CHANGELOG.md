# Changelog

## v1.1.7 — 2026-10-24

Keamanan (hasil tinjauan keamanan):
- Halaman Showcase (4 halaman) hanya untuk yang boleh mengatur tampilan (`canCustomizeAppearance`) dan hanya bila showcase aktif. Sebelumnya semua pengguna panel bisa membukanya.
- Upload di Showcase menolak SVG (hanya `appearance_page.image_types`) dan dibatasi ukurannya. Sebelumnya `->image()` menerima SVG, yang bisa dipakai untuk XSS tersimpan.
- Gambar login di halaman Tampilan: path dari browser tidak dipercaya (`preventFilePathTampering`) dan hanya path di folder milik package yang disimpan.
- `SafeUrl` kini daftar izin (`allowed_url_schemes`: http, https, mailto, tel, plus URL relatif). Sebelumnya daftar larangan, sehingga skema seperti `blob:` lolos.
- Token CSS menolak komentar `/*` `*/`; radius dari config/JSON dicek ulang saat render; validasi warna memakai pola lengkap.
- `shadcn:install` menolak ID panel selain huruf kecil, angka, dan tanda hubung.

Perbaikan:
- Menyimpan halaman Tampilan dengan layout selain `split` tidak lagi menghapus gambar login.

Catatan peningkatan versi: config `blocked_url_schemes` diganti `allowed_url_schemes`.

## v1.1.6 — 2026-10-24

- Kolom tanggal kosong menampilkan petunjuk ("Pilih tanggal & jam"); format tampilan mengikuti bahasa ("23 Okt 2026 09:30"). `->placeholder()` / `->displayFormat()` di field tetap menang.
- Uji tampilan otomatis `tests/ui/` (Chromium + Safari lewat safaridriver, 1440/820/390, terang/gelap, sidebar ciut) dengan uji-diri yang memunculkan kembali bug lama. Tidak ikut paket composer.

## v1.1.5 — 2026-10-24

- DatePicker/DateTimePicker/TimePicker memakai kalender Filament (bergaya shadcn, sama di semua browser) sebagai bawaan; `->native()` di field tetap menang. Kontrol bawaan browser: `SHADCN_NATIVE_DATE_PICKERS=true` (`forms.native_date_pickers`).
- Form tanpa Section/Tabs/Wizard di halaman panel (mis. hasil `make:filament-resource --generate`) dibungkus kartu.

## v1.1.4 — 2026-10-24

Sisa selisih dengan `docs/mockup/`:
- Kartu pengguna di kaki sidebar menampilkan email di bawah nama (override view `user-menu` Filament di `resources/views/overrides/`; override milik aplikasi tetap diutamakan).
- Tabel `->stackedOnMobile()` di ponsel tampil sebagai daftar ringkas: tanpa label, kolom rata kanan (total), aksi, dan kotak pilih di kanan. Dipakai di tabel Showcase.
- Desktop: pencarian, pengatur kolom, dan aksi header tabel pindah ke baris tab (ListRecords dengan `getTabs()` atau Tabs berisi tabel) bila tabel tanpa judul.
- Tombol "Masuk dengan Google/Microsoft/GitHub" lewat config `auth.social` (hanya tampilan; alur OAuth di aplikasi, URL lewat SafeUrl): tombol lebar di atas form (muted/plain), tombol ikon di bawah form (split).

Perbaikan:
- Input & select mengisi tinggi kontrol, sehingga teks sejajar dengan prefix/suffix (sebelumnya 30px di dalam kontrol 36px).

## v1.1.3 — 2026-10-24

- `Pages\Auth\EditProfile`: profil di dalam layout panel dengan dua kartu (Informasi akun, Kata sandi), label di atas field, lebar 56rem. `shadcn:install` memasangnya (`->profile(EditProfile::class, isSimple: false)`).
- Sidebar ciut: kelompok menu dipusatkan dengan `margin-inline: auto` tanpa bergantung pada `scrollbar-gutter` (Safari).

## v1.1.2 — 2026-10-23

- Avatar di kaki sidebar & header dikunci 32/36px dari semua sisi (Safari bisa meregangkan avatar SVG jadi lonjong); tombol pengguna saat sidebar ciut bulat 40×40.
- `shadcn:install` mengganti `->profile()` dengan `->profile(isSimple: false)` supaya halaman profil tampil di dalam layout panel (sidebar + header). Stub ikut diperbarui.
- Badge jumlah notifikasi selalu merah, termasuk di topbar Filament dan layout sederhana.

## v1.1.1 — 2026-10-23

Perbaikan sidebar yang diciutkan (`->sidebarCollapsibleOnDesktop()`):
- Kotak ikon merek tetap tampil (nama & subjudul disembunyikan).
- Ikon navigasi dipusatkan (margin negatif Filament dan ruang scrollbar tidak lagi menggeser).
- Avatar di kaki sidebar tidak lagi gepeng/lonjong (ukuran dikunci 32×32).

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
