# Uji tampilan otomatis

Memeriksa aturan tampilan di setiap halaman, di **Chromium** dan **Safari asli**, ukuran 1440 / 820 / 390px, mode terang & gelap, plus sidebar diciutkan.

```bash
cd tests/ui
npm install
npm test               # Chromium + Safari (Safari dilewati bila belum diizinkan)
npm run selftest       # bukti tiap aturan menangkap bug lamanya
```

Butuh panel yang sedang berjalan (bawaan: proyek uji `../shadcn-playground` di `http://127.0.0.1:8765/admin`). Atur lewat env: `UI_BASE_URL`, `UI_EMAIL`, `UI_PASSWORD`, `UI_PAGES`, `UI_VIEWPORTS`.

Hasil: `output/<browser>/*.png` dan `output/report.json`. Perintah keluar dengan kode 1 bila ada pelanggaran.

## Safari

Sekali per komputer: Safari → Pengaturan → Lanjutan → centang *Tampilkan fitur untuk pengembang web*, lalu Pengaturan → Pengembang → centang **Izinkan otomatisasi jarak jauh** (*Allow remote automation*). Uji memakai `safaridriver` bawaan macOS.

## Aturan (checks.js)

| Aturan | Masalah yang pernah terjadi |
|---|---|
| `input-vertically-centered` | teks input lebih tinggi dari prefix/suffix |
| `avatar-square` | avatar lonjong di Safari |
| `no-native-date-input` | kalender bawaan Safari |
| `date-picker-placeholder` | kolom tanggal kosong tampak rusak |
| `field-inside-card` | form polos tanpa kartu |
| `shell-header-present`, `sidebar-present`, `profile-in-panel` | profil di layout terpisah |
| `collapsed-sidebar-centered`, `collapsed-sidebar-brand-visible` | ikon sidebar ciut miring, merek hilang |
| `auth-split-one-card` | panel gambar login tidak sejajar dengan form |
| `no-horizontal-overflow`, `page-rendered`, `no-js-or-server-error` | overflow, error 500/JS |

**Setiap bug tampilan baru:** tambahkan aturan di `checks.js` dan kasusnya di `selftest.mjs`, lalu pastikan `npm run selftest` menangkapnya.
