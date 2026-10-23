<?php

return [

    'appearance' => [
        'navigation_label' => 'Tampilan',
        'title' => 'Tampilan',
        'subheading' => 'Atur gaya, warna, dan tata letak panel. Perubahan berlaku untuk semua pengguna.',
        'sections' => [
            'style' => 'Gaya',
            'style_description' => 'Bentuk, radius, dan kerapatan komponen.',
            'colors' => 'Warna',
            'colors_description' => 'Warna dasar, warna utama, dan warna status.',
            'layout' => 'Tata letak',
            'layout_description' => 'Sidebar dan halaman login.',
            'typography' => 'Tipografi',
            'typography_description' => 'Font dari Bunny Fonts (atau penyedia yang diatur di config).',
            'advanced' => 'Lanjutan',
            'advanced_description' => 'Timpa token shadcn/ui satu per satu, mis. hasil salin dari ui.shadcn.com/themes atau tweakcn.',
        ],
        'styles' => [
            'vega' => 'Vega — klasik',
            'nova' => 'Nova — padat',
            'maia' => 'Maia — lembut',
            'lyra' => 'Lyra — tajam',
            'mira' => 'Mira — rapat',
        ],
        'fields' => [
            'style' => 'Gaya komponen',
            'radius' => 'Radius',
            'radius_helper' => 'Kosongkan untuk ikut gaya. Dalam rem, 0 – 1.5.',
            'density' => 'Kerapatan',
            'density_helper' => 'Kosongkan untuk ikut gaya. 1 = normal.',
            'base_color' => 'Warna dasar',
            'primary_mode' => 'Warna utama',
            'primary_mode_default' => 'Default (hitam / putih)',
            'primary_mode_named' => 'Pilih warna',
            'primary_mode_custom' => 'Kode warna sendiri',
            'primary_named' => 'Warna',
            'primary_custom' => 'Kode warna',
            'danger' => 'Bahaya',
            'success' => 'Sukses',
            'warning' => 'Peringatan',
            'info' => 'Info',
            'sidebar' => 'Sidebar',
            'sidebar_inset' => 'Inset — konten jadi kartu',
            'sidebar_sidebar' => 'Klasik — garis pemisah',
            'sidebar_floating' => 'Mengambang — sidebar & konten jadi kartu',
            'auth_layout' => 'Halaman login',
            'auth_muted' => 'Latar muted, kartu di tengah',
            'auth_split' => 'Form + gambar',
            'auth_plain' => 'Polos',
            'auth_image' => 'Gambar login',
            'font_sans' => 'Font utama',
            'font_mono' => 'Font monospace',
            'font_helper' => 'Nama font, mis. Geist, Inter, Plus Jakarta Sans. Kosongkan untuk ikut gaya.',
            'tokens_light' => 'Token mode terang',
            'tokens_dark' => 'Token mode gelap',
            'token_key' => 'Token',
            'token_value' => 'Nilai',
        ],
        'actions' => [
            'save' => 'Simpan',
            'reset' => 'Kembalikan ke bawaan',
            'reset_confirm' => 'Semua pengaturan tampilan akan kembali ke nilai bawaan dari config.',
        ],
        'validation' => [
            'token' => 'Token :key berisi nilai yang tidak diizinkan. Pakai warna (oklch(), #hex), ukuran, calc() atau var().',
        ],

        'notifications' => [
            'saved' => 'Tampilan disimpan',
            'saved_body' => 'Muat ulang halaman jika ada bagian yang belum berubah.',
            'reset' => 'Tampilan dikembalikan ke bawaan',
        ],
    ],

    'auth' => [
        'login' => [
            'heading' => 'Selamat datang kembali',
            'subheading' => 'Masuk ke akun :brand Anda',
            'register_prompt' => 'Belum punya akun?',
            'register' => 'Daftar',
        ],
        'terms' => 'Dengan melanjutkan, Anda menyetujui :terms dan :privacy kami.',
        'terms_label' => 'Ketentuan Layanan',
        'privacy_label' => 'Kebijakan Privasi',
    ],

    'profile' => [
        'account' => 'Informasi akun',
        'account_description' => 'Nama dan alamat email untuk masuk ke panel.',
        'password' => 'Kata sandi',
        'password_description' => 'Kosongkan bila tidak ingin mengganti kata sandi.',
    ],

    'shell' => [
        'toggle_sidebar' => 'Buka/tutup sidebar',
        'breadcrumbs' => 'Jejak navigasi',
    ],

    'showcase' => [
        'dashboard_label' => 'Contoh dasbor',
        'form_label' => 'Contoh form',
        'navigation_label' => 'Showcase komponen',
        'title' => 'Showcase komponen',
        'subheading' => 'Semua komponen Filament dalam satu halaman untuk mengecek tema.',
    ],

    'components' => [
        'navigation_label' => 'Komponen shadcn',
        'title' => 'Komponen shadcn',
        'subheading' => 'Komponen Blade <x-shadcn::…> untuk kebutuhan di luar komponen bawaan Filament.',
    ],

    'ui' => [
        'carousel' => [
            'label' => 'Carousel',
            'previous' => 'Slide sebelumnya',
            'next' => 'Slide berikutnya',
        ],
        'command' => [
            'placeholder' => 'Ketik perintah atau cari…',
            'empty' => 'Tidak ada hasil.',
        ],
        'resizable' => [
            'label' => 'Ubah ukuran panel',
        ],
        'spinner' => [
            'label' => 'Memuat',
        ],
        'message_scroller' => [
            'latest' => 'Ke pesan terbaru',
        ],
        'questionnaire' => [
            'submit' => 'Kirim',
            'next' => 'Lanjut',
            'previous' => 'Kembali',
            'skip' => 'Lewati',
            'required' => 'Pertanyaan ini wajib dijawab.',
            'progress' => 'Pertanyaan :current dari :total',
        ],
    ],

];
