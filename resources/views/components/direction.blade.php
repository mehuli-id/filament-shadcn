{{-- Direction — atur arah teks bagian tertentu: <x-shadcn::direction dir="rtl">…</x-shadcn::direction> --}}
@props([
    'dir' => 'ltr', // ltr | rtl
])

<div dir="{{ $dir }}" {{ $attributes->class(['sc-direction']) }}>
    {{ $slot }}
</div>
