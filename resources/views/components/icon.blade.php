{{-- Ikon garis sederhana (gaya Lucide) yang dipakai komponen shadcn. --}}
@props([
    'name',
    'size' => 16,
])

@php
    $paths = [
        'alert' => 'M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20zM12 8v4M12 16h.01',
        'arrow-down' => 'M12 5v14M19 12l-7 7-7-7',
        'check' => 'M20 6 9 17l-5-5',
        'chevron-down' => 'm6 9 6 6 6-6',
        'chevron-left' => 'm15 18-6-6 6-6',
        'chevron-right' => 'm9 18 6-6-6-6',
        'chevron-up' => 'm18 15-6-6-6 6',
        'circle' => 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18z',
        'circle-check' => 'M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20zM9 12l2 2 4-4',
        'copy' => 'M9 9h11v11H9zM5 15H4V4h11v1',
        'file' => 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6',
        'grip-horizontal' => 'M5 9h.01M12 9h.01M19 9h.01M5 15h.01M12 15h.01M19 15h.01',
        'grip-vertical' => 'M9 5h.01M9 12h.01M9 19h.01M15 5h.01M15 12h.01M15 19h.01',
        'loader' => 'M21 12a9 9 0 1 1-6.22-8.56',
        'search' => 'M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16zM21 21l-4.35-4.35',
        'x' => 'M18 6 6 18M6 6l12 12',
    ];
@endphp

<svg
    {{ $attributes->class(['sc-icon']) }}
    width="{{ $size }}"
    height="{{ $size }}"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="2"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
    focusable="false"
><path d="{{ $paths[$name] ?? '' }}" /></svg>
