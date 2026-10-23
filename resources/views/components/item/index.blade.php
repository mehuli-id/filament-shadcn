{{--
    Item — baris konten: media, judul, deskripsi, aksi.
    <x-shadcn::item variant="outline" href="/admin/orders/1">
        <x-shadcn::item.media variant="icon">…svg…</x-shadcn::item.media>
        <x-shadcn::item.content>
            <x-shadcn::item.title>Verifikasi pembayaran</x-shadcn::item.title>
            <x-shadcn::item.description>3 transfer menunggu dicek.</x-shadcn::item.description>
        </x-shadcn::item.content>
        <x-shadcn::item.actions>…tombol…</x-shadcn::item.actions>
    </x-shadcn::item>
--}}
@props([
    'variant' => 'default', // default | outline | muted
    'size' => 'default', // default | sm | xs
    'href' => null,
])

@php
    $classes = ['sc-item', 'sc-item-' . $variant, 'sc-item-size-' . $size];
@endphp

@if (filled($href))
    <a href="{{ \Mehuli\FilamentShadcn\Support\SafeUrl::filter($href) }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <div {{ $attributes->class($classes) }}>{{ $slot }}</div>
@endif
