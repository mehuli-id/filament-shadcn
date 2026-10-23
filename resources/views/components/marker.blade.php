{{--
    Marker — catatan singkat di dalam percakapan/linimasa (status, catatan sistem, pemisah tanggal).
    <x-shadcn::marker variant="separator">Hari ini</x-shadcn::marker>
    <x-shadcn::marker variant="border" role="status"><x-slot:icon>✓</x-slot:icon>Sinkronisasi selesai</x-shadcn::marker>
--}}
@props([
    'variant' => 'default', // default | border | separator
    'href' => null,
    'shimmer' => false, // efek berkilau untuk status yang sedang berjalan
])

@php
    $tag = filled($href) ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if (filled($href)) href="{{ \Mehuli\FilamentShadcn\Support\SafeUrl::filter($href) }}" @endif
    {{ $attributes->class(['sc-marker', 'sc-marker-' . $variant]) }}
>
    @isset($icon)
        <span class="sc-marker-icon" aria-hidden="true">{{ $icon }}</span>
    @endisset
    <span @class(['sc-marker-content', 'sc-shimmer' => $shimmer])>{{ $slot }}</span>
</{{ $tag }}>
