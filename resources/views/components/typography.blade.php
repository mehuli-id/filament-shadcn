{{--
    Typography — gaya teks shadcn untuk konten bebas (artikel, halaman bantuan, deskripsi).
    <x-shadcn::typography>
        <h1>Judul</h1>
        <p class="sc-lead">Paragraf pembuka.</p>
        <p>…</p>
        <blockquote>…</blockquote>
    </x-shadcn::typography>

    Kelas tambahan: sc-lead, sc-large, sc-small, sc-muted.
--}}
<div {{ $attributes->class(['sc-typography']) }}>
    {{ $slot }}
</div>
