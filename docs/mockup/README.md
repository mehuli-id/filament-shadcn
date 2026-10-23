# Mockup desain — referensi

Mockup panel admin Filament × shadcn/ui yang menjadi acuan package ini.
Sumber aslinya ada di artifact Design "Filament UI Mockup" di claude.ai; folder ini salinannya per 23 Oktober 2026.

## Isi

- `gambar/` — tangkapan layar PNG setiap artboard (mode terang, gaya nova). Paling mudah untuk dilihat cepat.
- `sumber/` — file asli artboard (`*.dc.html`), interaktif. Buka langsung di browser
  (dobel-klik); `support.js` adalah runtime-nya. `canvas.json` mencatat posisi & judul tiap artboard.

## Daftar artboard

| File | Judul | Lebar |
|---|---|---|
| `Shadcn` | Dasbor (sidebar, pencarian, notifikasi, pilihan tema) | 1440 |
| `Form` | Form — semua komponen | 1440 |
| `LoginMuted` | Login — latar muted | 1440 |
| `LoginImage` | Login — form + gambar | 1440 |
| `DasborPonsel` / `DasborTablet` | Dasbor — ponsel / tablet | 390 / 820 |
| `FormPonsel` / `FormTablet` | Form — ponsel / tablet | 390 / 820 |
| `LoginPonsel` / `LoginTablet` | Login — ponsel / tablet | 390 / 820 |
| `Main`, `Bento`, `Dark` | Eksplorasi gaya awal (A Clean Minimal, B Soft Bento, C Dark Premium) — tidak dipakai | 1440 |

## Catatan

- Mode gelap & gaya lain (vega, maia, lyra) bisa dicoba di file `sumber/` lewat tombol tema di pojok kanan atas.
- Font Geist dimuat dari Google Fonts; tanpa internet browser memakai font sistem.
- Folder `docs/` tidak ikut terpasang lewat composer (lihat `.gitattributes`).
