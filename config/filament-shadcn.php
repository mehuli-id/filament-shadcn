<?php

/*
|--------------------------------------------------------------------------
| Filament × shadcn/ui
|--------------------------------------------------------------------------
|
| Semua nilai di sini adalah nilai awal. Urutan prioritas (paling kuat di bawah):
|
|   1. file ini (bisa diisi lewat .env)
|   2. pengaturan lewat kode: ShadcnPlugin::make()->primary('blue')->...
|   3. halaman "Tampilan" di panel (disimpan ke file JSON, bisa diubah admin)
|
*/

return [

    /*
    | Gaya komponen, padanan "style" di shadcn/ui:
    |   vega  – klasik (dulu "New York"), radius sedang
    |   nova  – padat, cocok untuk admin panel
    |   maia  – lembut, tombol & input berbentuk pil
    |   lyra  – kotak tajam tanpa radius, font monospace
    |   mira  – paling rapat, untuk tabel data padat
    */
    'style' => env('SHADCN_STYLE', 'nova'),

    /*
    | Warna dasar (netral) untuk latar, border, teks redup, sidebar.
    | Pilihan: neutral, zinc, slate, stone, gray, mauve, olive, mist, taupe
    */
    'base_color' => env('SHADCN_BASE_COLOR', 'zinc'),

    /*
    | Warna utama (tombol utama, toggle, checkbox, link aktif).
    |   'default'  – hitam di mode terang, putih di mode gelap (khas shadcn)
    |   nama warna – red, orange, amber, yellow, lime, green, emerald, teal,
    |                cyan, sky, blue, indigo, violet, purple, fuchsia, pink, rose
    |   kode warna – '#2563eb', 'rgb(37, 99, 235)', atau 'oklch(0.55 0.22 263)'
    */
    'primary' => env('SHADCN_PRIMARY', 'default'),

    /*
    | Warna status. Nama warna atau kode warna, seperti 'primary'.
    */
    'colors' => [
        'danger' => env('SHADCN_DANGER', 'red'),
        'success' => env('SHADCN_SUCCESS', 'emerald'),
        'warning' => env('SHADCN_WARNING', 'amber'),
        'info' => env('SHADCN_INFO', 'blue'),
    ],

    /*
    | Radius dasar dalam rem. null = ikut gaya (vega 0.625, nova 0.5, maia 1,
    | lyra 0, mira 0.375).
    */
    'radius' => env('SHADCN_RADIUS'),

    /*
    | Kerapatan (skala jarak Tailwind). null = ikut gaya. 1 = normal.
    */
    'density' => env('SHADCN_DENSITY'),

    /*
    | Font. null = ikut gaya (Geist, atau Geist Mono untuk lyra).
    | provider: null = Bunny Fonts (bawaan Filament). Bisa diisi class
    | \Filament\FontProviders\GoogleFontProvider::class atau LocalFontProvider.
    */
    'font' => [
        'sans' => env('SHADCN_FONT'),
        'mono' => env('SHADCN_FONT_MONO'),
        'provider' => null,
    ],

    /*
    | Tata letak panel.
    |   sidebar: 'inset'   – konten utama jadi kartu di atas latar sidebar
    |            'sidebar' – sidebar polos dengan garis pemisah (klasik)
    |            'floating'– sidebar & konten sama-sama kartu mengambang
    |   shell:   true  – header shadcn di dalam kartu konten (lihat docs/mockup): judul/breadcrumb,
    |                    pencarian, pilihan tema, notifikasi, tautan. Topbar bawaan Filament
    |                    dimatikan dan menu pengguna pindah ke kaki sidebar.
    |            false – topbar bawaan Filament
    |   sidebar_width:  lebar sidebar desktop (shadcn: 16rem). Angka + rem/px/em.
    |   brand_icon:     ikon di kotak merek (sidebar & login) bila panel tanpa ->brandLogo().
    |   brand_subtitle: teks kecil di bawah nama merek, mis. nama toko/cabang.
    |   header_links:   tautan di ujung header, mis. [['label' => 'Dokumentasi', 'url' => 'https://…']].
    |                   Label boleh kunci terjemahan.
    */
    'layout' => [
        'sidebar' => env('SHADCN_SIDEBAR', 'inset'),
        'shell' => env('SHADCN_SHELL', true),
        'sidebar_width' => env('SHADCN_SIDEBAR_WIDTH', '16rem'),
        'brand_icon' => env('SHADCN_BRAND_ICON', 'heroicon-o-home'),
        'brand_subtitle' => env('SHADCN_BRAND_SUBTITLE'),
        'header_links' => [],
    ],

    /*
    | Form.
    |   native_date_pickers: false – DatePicker/DateTimePicker/TimePicker memakai kalender Filament
    |                                (bergaya shadcn, sama di semua browser) kecuali ->native() ditulis sendiri
    |                        true  – kontrol tanggal bawaan browser
    */
    'forms' => [
        'native_date_pickers' => env('SHADCN_NATIVE_DATE_PICKERS', false),
    ],

    /*
    | Halaman login / daftar / lupa sandi.
    |   layout: 'muted' – kartu di tengah, latar abu-abu (block login-03)
    |           'split' – form + gambar berdampingan (block login-02/04)
    |           'plain' – latar putih polos
    |   image:  URL atau path asset gambar untuk layout 'split'
    */
    'auth' => [
        'layout' => env('SHADCN_AUTH_LAYOUT', 'muted'),
        'image' => env('SHADCN_AUTH_IMAGE'),
        'image_position' => 'end', // 'start' | 'end'
        'tagline' => null,
        'tagline_author' => null,

        // Teks persetujuan di bawah kartu ("Dengan melanjutkan, Anda menyetujui …").
        // Tampil bila salah satu URL diisi.
        'terms_url' => env('SHADCN_TERMS_URL'),
        'privacy_url' => env('SHADCN_PRIVACY_URL'),

        // Tombol "Masuk dengan …" di halaman login (hanya tampilan). Alur OAuth-nya
        // (mis. Laravel Socialite) dibuat di aplikasi; 'url' mengarah ke rute redirect-nya.
        // 'icon': google | microsoft | github, atau nama Heroicon.
        // Contoh: ['label' => 'Google', 'url' => '/auth/google/redirect', 'icon' => 'google'],
        'social' => [],
    ],

    /*
    | Warna grafik (chart-1 … chart-5). null = bawaan shadcn.
    | Contoh: ['light' => ['#e8590c', ...5], 'dark' => [...5]]
    */
    'charts' => null,

    /*
    | Timpa token shadcn mana pun. Kunci = nama variabel tanpa "--".
    | Cocok untuk menempel tema dari ui.shadcn.com/themes atau tweakcn.
    | Contoh: 'light' => ['primary' => 'oklch(0.55 0.22 263)', 'radius' => '0.75rem']
    */
    'tokens' => [
        'light' => [],
        'dark' => [],
    ],

    /*
    | Halaman "Tampilan" di panel untuk mengubah gaya & warna tanpa deploy.
    */
    'appearance_page' => [
        'enabled' => env('SHADCN_APPEARANCE_PAGE', true),
        'storage_disk' => env('SHADCN_APPEARANCE_DISK', 'local'),
        'storage_path' => 'filament-shadcn/appearance.json',
        'navigation_group' => null,
        'navigation_sort' => 90,

        // Gambar login yang boleh diunggah (SVG sengaja tidak termasuk) dan batas ukurannya.
        'image_types' => ['image/png', 'image/jpeg', 'image/webp', 'image/avif'],
        'image_max_kb' => 4096,
        'image_directory' => 'filament-shadcn', // folder di disk public
    ],

    /*
    | Halaman "Showcase" berisi semua komponen Filament untuk mengecek tema.
    | Sebaiknya hanya aktif di lingkungan lokal / staging.
    */
    'showcase' => [
        'enabled' => env('SHADCN_SHOWCASE', false),
        'navigation_group' => null,
        'navigation_sort' => 91,
    ],

    /*
    | Skema URL yang ditolak di atribut href komponen <x-shadcn::…>.
    */
    'blocked_url_schemes' => ['javascript', 'vbscript', 'data', 'file'],

];
