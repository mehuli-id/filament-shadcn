{{--
    Popover — konten mengambang yang dibuka dengan klik.
    <x-shadcn::popover align="start" width="18rem">
        <x-slot:trigger><x-filament::button color="gray">Dimensi</x-filament::button></x-slot:trigger>
        …form kecil…
    </x-shadcn::popover>
--}}
@props([
    'side' => 'bottom', // bottom | top
    'align' => 'center', // start | center | end
    'width' => '18rem',
])

@php
    $id = 'sc-popover-' . \Illuminate\Support\Str::random(8);
@endphp

<span
    x-data="{ open: false }"
    x-on:click.outside="open = false"
    x-on:keydown.escape="open = false; $refs.trigger.querySelector('button, a, [tabindex]')?.focus()"
    {{ $attributes->class(['sc-popover']) }}
>
    <span
        x-ref="trigger"
        class="sc-popover-trigger"
        x-on:click="open = ! open"
        aria-haspopup="dialog"
        aria-controls="{{ $id }}"
        x-bind:aria-expanded="open"
    >{{ $trigger ?? '' }}</span>

    <div
        id="{{ $id }}"
        role="dialog"
        class="sc-floating sc-popover-content"
        data-side="{{ $side }}"
        data-align="{{ $align }}"
        style="width: {{ $width }}"
        x-show="open"
        {{-- Geser panel agar tetap di dalam layar (ponsel/tablet). --}}
        x-effect="if (open) $nextTick(() => { $el.style.marginLeft = ''; const r = $el.getBoundingClientRect(), pad = 16; let dx = 0; if (r.right > document.documentElement.clientWidth - pad) dx = document.documentElement.clientWidth - pad - r.right; if (r.left + dx < pad) dx = pad - r.left; if (dx) $el.style.marginLeft = dx + 'px' })"
        x-cloak
        x-transition.opacity.duration.150ms
    >
        {{ $slot }}
    </div>
</span>
