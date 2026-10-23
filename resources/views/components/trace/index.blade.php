{{--
    Trace — daftar langkah kerja (proses, job antrean, agen AI, impor data).
    <x-shadcn::trace>
        <x-shadcn::trace.line title="Mengimpor 1.284 pesanan" description="12 dtk" />
        <x-shadcn::trace.line status="running" title="Menghitung ulang stok" />
        <x-shadcn::trace.line status="error" title="Gagal mengirim email" description="3 dtk">
            SMTP menolak koneksi (550).   ← isi slot = detail yang bisa dibuka
        </x-shadcn::trace.line>
        <x-shadcn::trace.line status="pending" title="Membuat laporan" />
    </x-shadcn::trace>
--}}
<div role="list" {{ $attributes->class(['sc-trace']) }}>
    {{ $slot }}
</div>
