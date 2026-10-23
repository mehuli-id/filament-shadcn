<x-filament-panels::page>
    @php
        $commandItems = [
            ['group' => 'Halaman', 'items' => [
                ['label' => 'Dasbor', 'url' => filament()->getUrl(), 'shortcut' => 'G D'],
                ['label' => 'Tampilan', 'url' => \Mehuli\FilamentShadcn\Pages\Appearance::getUrl(), 'keywords' => 'tema warna'],
                ['label' => 'Showcase komponen', 'url' => \Mehuli\FilamentShadcn\Pages\ComponentShowcase::getUrl()],
            ]],
            ['group' => 'Aksi', 'items' => [
                ['label' => 'Buat produk', 'description' => 'Tambah produk baru ke katalog', 'event' => 'sc-demo-action'],
                ['label' => 'Ekspor laporan', 'event' => 'sc-demo-action', 'shortcut' => '⌘E'],
            ]],
        ];

        $questions = [
            [
                'name' => 'kepuasan',
                'prompt' => 'Seberapa puas Anda dengan panel admin ini?',
                'description' => 'Pilih satu jawaban.',
                'required' => true,
                'choices' => [
                    ['value' => 'sangat', 'label' => 'Sangat puas', 'description' => 'Semua berjalan lancar'],
                    ['value' => 'puas', 'label' => 'Puas'],
                    ['value' => 'kurang', 'label' => 'Kurang puas', 'description' => 'Ada yang perlu diperbaiki'],
                ],
            ],
            [
                'name' => 'fitur',
                'prompt' => 'Fitur apa yang paling sering Anda pakai?',
                'description' => 'Boleh pilih lebih dari satu, atau lewati.',
                'multiple' => true,
                'choices' => [
                    ['value' => 'pesanan', 'label' => 'Pesanan'],
                    ['value' => 'laporan', 'label' => 'Laporan'],
                    ['value' => 'stok', 'label' => 'Stok'],
                ],
                'input' => 'Lainnya…',
            ],
        ];

        $code = <<<'PHP'
        use Mehuli\FilamentShadcn\ShadcnPlugin;

        return $panel
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->plugin(ShadcnPlugin::make()->style('nova')->primary('blue'));
        PHP;

        $diff = "  'style' => 'vega',\n- 'primary' => 'default',\n+ 'primary' => 'blue',\n  'radius' => null,";
    @endphp

    <div class="sc-demo-grid" x-data x-on:sc-demo-action.window="new FilamentNotification().title('Perintah dijalankan: ' + $event.detail.label).success().send()">

        {{-- Accordion --}}
        <x-filament::section heading="Accordion" description="Satu atau beberapa bagian terbuka.">
            <x-shadcn::accordion default="kirim">
                <x-shadcn::accordion.item value="kirim" title="Berapa lama pengiriman?">
                    Pengiriman Jabodetabek 1–2 hari kerja, luar kota 2–5 hari kerja.
                </x-shadcn::accordion.item>
                <x-shadcn::accordion.item value="retur" title="Bagaimana cara retur barang?">
                    Ajukan retur dari halaman pesanan maksimal 7 hari setelah barang diterima.
                </x-shadcn::accordion.item>
                <x-shadcn::accordion.item value="bayar" title="Metode pembayaran apa saja?">
                    Transfer bank, QRIS, kartu kredit, dan e-wallet.
                </x-shadcn::accordion.item>
            </x-shadcn::accordion>
        </x-filament::section>

        {{-- Progress, Spinner, Skeleton --}}
        <x-filament::section heading="Progress · Spinner · Skeleton">
            <div class="sc-demo-stack">
                <x-shadcn::progress :value="64" label="Unggah katalog" show-value />
                <x-shadcn::progress :value="100" color="success" label="Impor selesai" show-value />
                <x-shadcn::progress :value="18" color="danger" size="sm" label="Stok minimum" />
                <x-shadcn::progress indeterminate label="Memuat" />

                <div class="sc-demo-row">
                    <x-shadcn::spinner size="sm" />
                    <x-shadcn::spinner />
                    <x-shadcn::spinner size="lg" label="Menyimpan" />
                    <x-filament::button disabled>
                        <x-shadcn::spinner size="sm" /> Menyimpan…
                    </x-filament::button>
                </div>

                <div class="sc-demo-row">
                    <x-shadcn::skeleton shape="circle" width="3rem" />
                    <div class="sc-demo-stack" style="flex: 1; gap: 0.5rem">
                        <x-shadcn::skeleton width="70%" />
                        <x-shadcn::skeleton width="45%" />
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- Kbd, Separator, Marker --}}
        <x-filament::section heading="Kbd · Separator · Marker">
            <div class="sc-demo-stack">
                <p style="font-size: 0.875rem">
                    Tekan <x-shadcn::kbd.group><x-shadcn::kbd>⌘</x-shadcn::kbd><x-shadcn::kbd>K</x-shadcn::kbd></x-shadcn::kbd.group>
                    untuk membuka palet perintah, atau <x-shadcn::kbd>Esc</x-shadcn::kbd> untuk menutup.
                </p>
                <x-shadcn::separator />
                <div class="sc-demo-row" style="height: 1.25rem; font-size: 0.875rem">
                    <span>Blog</span><x-shadcn::separator orientation="vertical" /><span>Dokumentasi</span><x-shadcn::separator orientation="vertical" /><span>Sumber</span>
                </div>
                <x-shadcn::separator label="atau" />
                <x-shadcn::marker variant="separator">Hari ini</x-shadcn::marker>
                <x-shadcn::marker variant="border" role="status">
                    <x-slot:icon><x-shadcn::icon name="check" size="14" /></x-slot:icon>
                    Sinkronisasi selesai
                </x-shadcn::marker>
                <x-shadcn::marker shimmer>Menyiapkan laporan bulanan…</x-shadcn::marker>
            </div>
        </x-filament::section>

        {{-- Item --}}
        <x-filament::section heading="Item" description="Baris konten: media, judul, deskripsi, aksi.">
            <x-shadcn::item.group>
                <x-shadcn::item variant="outline">
                    <x-shadcn::item.media variant="icon"><x-shadcn::icon name="alert" /></x-shadcn::item.media>
                    <x-shadcn::item.content>
                        <x-shadcn::item.title>Verifikasi pembayaran</x-shadcn::item.title>
                        <x-shadcn::item.description>3 transfer menunggu dicek sejak pagi ini.</x-shadcn::item.description>
                    </x-shadcn::item.content>
                    <x-shadcn::item.actions>
                        <x-filament::button size="sm" color="gray">Cek</x-filament::button>
                    </x-shadcn::item.actions>
                </x-shadcn::item>
                <x-shadcn::item variant="muted" size="sm" href="#">
                    <x-shadcn::item.media variant="avatar"><span class="sc-avatar">RS</span></x-shadcn::item.media>
                    <x-shadcn::item.content>
                        <x-shadcn::item.title>Rina Sembiring</x-shadcn::item.title>
                        <x-shadcn::item.description>Pelanggan sejak 2024 · 12 pesanan</x-shadcn::item.description>
                    </x-shadcn::item.content>
                    <x-shadcn::icon name="chevron-right" />
                </x-shadcn::item>
            </x-shadcn::item.group>
        </x-filament::section>

        {{-- Popover, Hover card, Context menu, Menubar --}}
        <x-filament::section heading="Popover · Hover Card · Context Menu · Menubar">
            <div class="sc-demo-stack">
                <div class="sc-demo-row">
                    <x-shadcn::popover align="start" width="18rem">
                        <x-slot:trigger><x-filament::button color="gray">Buka popover</x-filament::button></x-slot:trigger>
                        <div class="sc-demo-stack" style="gap: 0.5rem">
                            <strong>Dimensi paket</strong>
                            <span style="color: var(--muted-foreground)">Atur ukuran untuk menghitung ongkir.</span>
                            <x-filament::input.wrapper><x-filament::input type="text" value="9 × 9 × 24 cm" /></x-filament::input.wrapper>
                        </div>
                    </x-shadcn::popover>

                    <x-shadcn::hover-card align="start">
                        <x-slot:trigger><a href="#" class="fi-link" style="font-size: 0.875rem">@mehuli</a></x-slot:trigger>
                        <div class="sc-demo-row" style="align-items: flex-start">
                            <span class="sc-avatar">MD</span>
                            <div class="sc-demo-stack" style="gap: 0.25rem">
                                <strong>PT. Mehuli Data Media</strong>
                                <span style="color: var(--muted-foreground)">Membangun aplikasi web & sistem informasi.</span>
                            </div>
                        </div>
                    </x-shadcn::hover-card>
                </div>

                <x-shadcn::context-menu>
                    <div class="sc-demo-dropzone">Klik kanan di area ini</div>
                    <x-slot:menu>
                        <x-shadcn::menu.label>Pesanan INV-2041</x-shadcn::menu.label>
                        <x-shadcn::menu.separator />
                        <x-shadcn::menu.item shortcut="⌘O">Buka</x-shadcn::menu.item>
                        <x-shadcn::menu.item shortcut="⌘D">Duplikat</x-shadcn::menu.item>
                        <x-shadcn::menu.item disabled>Arsipkan</x-shadcn::menu.item>
                        <x-shadcn::menu.separator />
                        <x-shadcn::menu.item destructive shortcut="⌫">Hapus</x-shadcn::menu.item>
                    </x-slot:menu>
                </x-shadcn::context-menu>

                <x-shadcn::menubar>
                    <x-shadcn::menubar.menu label="Berkas">
                        <x-shadcn::menu.item shortcut="⌘N">Pesanan baru</x-shadcn::menu.item>
                        <x-shadcn::menu.item shortcut="⌘P">Cetak</x-shadcn::menu.item>
                        <x-shadcn::menu.separator />
                        <x-shadcn::menu.item>Ekspor CSV</x-shadcn::menu.item>
                    </x-shadcn::menubar.menu>
                    <x-shadcn::menubar.menu label="Edit">
                        <x-shadcn::menu.item shortcut="⌘Z">Urungkan</x-shadcn::menu.item>
                        <x-shadcn::menu.item shortcut="⇧⌘Z">Ulangi</x-shadcn::menu.item>
                    </x-shadcn::menubar.menu>
                    <x-shadcn::menubar.menu label="Tampilan">
                        <x-shadcn::menu.item>Mode padat</x-shadcn::menu.item>
                        <x-shadcn::menu.item>Layar penuh</x-shadcn::menu.item>
                    </x-shadcn::menubar.menu>
                </x-shadcn::menubar>
            </div>
        </x-filament::section>

        {{-- Navigation menu, Command, Drawer --}}
        <x-filament::section heading="Navigation Menu · Command · Drawer">
            <div class="sc-demo-stack">
                <x-shadcn::navigation-menu>
                    <x-shadcn::navigation-menu.item label="Produk">
                        <x-shadcn::navigation-menu.link href="#" title="Semua produk" description="Katalog lengkap dengan stok terkini." />
                        <x-shadcn::navigation-menu.link href="#" title="Kategori" description="Atur pengelompokan produk." />
                        <x-shadcn::navigation-menu.link href="#" title="Promo" description="Diskon & voucher yang aktif." />
                        <x-shadcn::navigation-menu.link href="#" title="Ulasan" description="Tanggapan pelanggan." />
                    </x-shadcn::navigation-menu.item>
                    <x-shadcn::navigation-menu.item label="Laporan" href="#" />
                    <x-shadcn::navigation-menu.item label="Bantuan" href="#" />
                </x-shadcn::navigation-menu>

                <x-shadcn::command :items="$commandItems" />

                <div class="sc-demo-row">
                    <x-shadcn::command :items="$commandItems" dialog shortcut="j">
                        <x-slot:trigger>
                            <x-filament::button color="gray">
                                Palet perintah <x-shadcn::kbd.group><x-shadcn::kbd>⌘</x-shadcn::kbd><x-shadcn::kbd>J</x-shadcn::kbd></x-shadcn::kbd.group>
                            </x-filament::button>
                        </x-slot:trigger>
                    </x-shadcn::command>

                    <x-shadcn::drawer title="Target penjualan" description="Atur target harian Anda.">
                        <x-slot:trigger><x-filament::button color="gray">Buka drawer</x-filament::button></x-slot:trigger>
                        <div class="sc-demo-stack" style="align-items: center; padding-block: 1rem">
                            <span style="font-size: 3rem; font-weight: 700; letter-spacing: -0.04em">350</span>
                            <span style="color: var(--muted-foreground)">pesanan / hari</span>
                        </div>
                        <x-slot:footer>
                            <x-filament::button x-on:click="open = false">Simpan</x-filament::button>
                            <x-filament::button color="gray" x-on:click="open = false">Batal</x-filament::button>
                        </x-slot:footer>
                    </x-shadcn::drawer>
                </div>
            </div>
        </x-filament::section>

        {{-- Code block --}}
        <x-filament::section heading="Code Block" class="sc-demo-wide">
            <div class="sc-demo-stack">
                <x-shadcn::code-block title="app/Providers/Filament/AdminPanelProvider.php" language="php" :code="$code" line-numbers :highlight="[5]" />
                <x-shadcn::code-block title="config/filament-shadcn.php" :code="$diff" diff size="sm" />
                <x-shadcn::code-block variant="destructive" size="sm" code="SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'TMB-500-01'" />
            </div>
        </x-filament::section>

        {{-- Carousel & Aspect ratio --}}
        <x-filament::section heading="Carousel · Aspect Ratio">
            <x-shadcn::carousel :per-view="2" label="Banner promo">
                @foreach (['Promo akhir tahun', 'Gratis ongkir', 'Produk baru', 'Paket kantor'] as $index => $slide)
                    <x-shadcn::carousel.item>
                        <x-shadcn::aspect-ratio ratio="4/3">
                            <div class="sc-demo-slide" style="--sc-demo-hue: {{ 220 + $index * 40 }}">{{ $slide }}</div>
                        </x-shadcn::aspect-ratio>
                    </x-shadcn::carousel.item>
                @endforeach
            </x-shadcn::carousel>
        </x-filament::section>

        {{-- Resizable & Scroll area --}}
        <x-filament::section heading="Resizable · Scroll Area">
            <div class="sc-demo-stack">
                <x-shadcn::resizable :default="35" with-handle style="height: 10rem">
                    <x-slot:start><div class="sc-demo-panel">Daftar</div></x-slot:start>
                    <x-slot:end>
                        <x-shadcn::resizable direction="vertical" :default="60" style="height: 100%; border-radius: 0; box-shadow: none">
                            <x-slot:start><div class="sc-demo-panel">Detail</div></x-slot:start>
                            <x-slot:end><div class="sc-demo-panel">Catatan</div></x-slot:end>
                        </x-shadcn::resizable>
                    </x-slot:end>
                </x-shadcn::resizable>

                <x-shadcn::scroll-area height="9rem" label="Riwayat stok" style="border-radius: var(--sc-radius-card); box-shadow: var(--sc-border)">
                    <div style="padding: 0.75rem 1rem">
                        <strong style="font-size: 0.875rem">Riwayat stok</strong>
                        @foreach (range(1, 12) as $i)
                            <div style="padding-block: 0.5rem; font-size: 0.875rem; border-top: 1px solid var(--border)">v1.{{ 12 - $i }} — stok diperbarui</div>
                        @endforeach
                    </div>
                </x-shadcn::scroll-area>
            </div>
        </x-filament::section>

        {{-- Attachment --}}
        <x-filament::section heading="Attachment">
            <div class="sc-demo-stack">
                <x-shadcn::attachment.group>
                    <x-shadcn::attachment name="laporan-oktober.pdf" description="PDF · 1,2 MB" />
                    <x-shadcn::attachment name="foto-produk.zip" description="Mengunggah · 64%" state="uploading" :progress="64" size="sm">
                        <x-slot:actions>
                            <x-shadcn::attachment.action aria-label="Batalkan unggah foto-produk.zip"><x-shadcn::icon name="x" /></x-shadcn::attachment.action>
                        </x-slot:actions>
                    </x-shadcn::attachment>
                    <x-shadcn::attachment name="faktur.xlsx" description="Format tidak didukung" state="error" size="sm" />
                </x-shadcn::attachment.group>
            </div>
        </x-filament::section>

        {{-- Trace --}}
        <x-filament::section heading="Trace" description="Langkah proses, job antrean, atau agen AI.">
            <x-shadcn::trace>
                <x-shadcn::trace.line title="Mengimpor 1.284 pesanan dari marketplace" description="12 dtk" />
                <x-shadcn::trace.line title="Mencocokkan pembayaran" description="3 dtk">
                    <x-shadcn::trace>
                        <x-shadcn::trace.line title="QRIS: 412 cocok" />
                        <x-shadcn::trace.line title="Transfer: 38 cocok" />
                    </x-shadcn::trace>
                </x-shadcn::trace.line>
                <x-shadcn::trace.line status="running" title="Menghitung ulang stok gudang" />
                <x-shadcn::trace.line status="error" title="Gagal mengirim email ringkasan" description="2 dtk">
                    SMTP menolak koneksi (550). Periksa pengaturan MAIL_HOST.
                </x-shadcn::trace.line>
                <x-shadcn::trace.line status="pending" title="Membuat laporan PDF" />
            </x-shadcn::trace>
        </x-filament::section>

        {{-- Message, Bubble, Message scroller --}}
        <x-filament::section heading="Message · Bubble · Message Scroller" class="sc-demo-wide">
            <x-slot:afterHeader>
                <x-filament::button size="sm" color="gray" wire:click="addMessage">Simulasikan pesan masuk</x-filament::button>
            </x-slot:afterHeader>

            <x-shadcn::message-scroller height="20rem" style="border-radius: var(--sc-radius-card); box-shadow: var(--sc-border)">
                <x-shadcn::marker variant="separator">Hari ini</x-shadcn::marker>

                @foreach ($messages as $message)
                    <x-shadcn::message :align="$message['mine'] ? 'end' : 'start'" wire:key="message-{{ $loop->index }}">
                        <x-shadcn::message.avatar :initials="$message['initials']" :alt="$message['author']" />
                        <x-shadcn::message.content>
                            @unless ($message['mine'])
                                <x-shadcn::message.header>{{ $message['author'] }}</x-shadcn::message.header>
                            @endunless
                            <x-shadcn::bubble :variant="$message['mine'] ? 'default' : 'secondary'" :align="$message['mine'] ? 'end' : 'start'">
                                {{ $message['text'] }}
                            </x-shadcn::bubble>
                            <x-shadcn::message.footer>{{ $message['time'] }}@if ($message['mine']) · Dibaca @endif</x-shadcn::message.footer>
                        </x-shadcn::message.content>
                    </x-shadcn::message>
                @endforeach

                <x-shadcn::message>
                    <x-shadcn::message.avatar initials="AI" alt="Asisten" />
                    <x-shadcn::message.content>
                        <x-shadcn::bubble variant="tinted">
                            Ringkasan: 3 pesanan menunggu konfirmasi.
                            <x-slot:reactions>👍 2</x-slot:reactions>
                        </x-shadcn::bubble>
                    </x-shadcn::message.content>
                </x-shadcn::message>
            </x-shadcn::message-scroller>
        </x-filament::section>

        {{-- Questionnaire --}}
        <x-filament::section heading="Questionnaire" description="Survei bertahap, terhubung ke Livewire.">
            <x-shadcn::questionnaire :items="$questions" wire="saveAnswers">
                <x-slot:done>Terima kasih! Jawaban Anda sudah kami terima.</x-slot:done>
            </x-shadcn::questionnaire>
        </x-filament::section>

        {{-- Typography & Direction --}}
        <x-filament::section heading="Typography · Direction">
            <x-shadcn::typography>
                <h3 style="margin-top: 0">Kebijakan pengembalian</h3>
                <p class="sc-lead">Belanja tenang dengan jaminan retur 7 hari.</p>
                <p>Barang dapat dikembalikan bila <a href="#">rusak atau tidak sesuai</a>. Sertakan foto dan nomor pesanan seperti <code>INV-2041</code>.</p>
                <blockquote>“Pelayanan cepat, retur pun dibantu sampai selesai.”</blockquote>
                <ul>
                    <li>Ajukan dari halaman pesanan</li>
                    <li>Kurir menjemput barang</li>
                </ul>
                <p class="sc-muted">Diperbarui 23 Oktober 2026.</p>
            </x-shadcn::typography>

            <x-shadcn::separator style="margin-block: 1rem" />

            <x-shadcn::direction dir="rtl">
                <x-shadcn::progress :value="40" label="تقدم" show-value />
            </x-shadcn::direction>
        </x-filament::section>
    </div>
</x-filament-panels::page>
