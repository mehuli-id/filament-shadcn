{{-- Scroll Area — area bergulir dengan scrollbar tipis: <x-shadcn::scroll-area height="18rem">…</x-shadcn::scroll-area> --}}
@props([
    'height' => null,
    'orientation' => 'vertical', // vertical | horizontal | both
    'label' => null,
])

<div
    tabindex="0"
    @if (filled($label)) role="region" aria-label="{{ $label }}" @endif
    {{ $attributes->class(['sc-scroll-area', 'sc-scroll-area-' . $orientation])->style([$height ? 'max-height: ' . $height : '']) }}
>
    {{ $slot }}
</div>
