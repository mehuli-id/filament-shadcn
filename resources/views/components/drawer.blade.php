{{--
    Drawer — panel yang muncul dari bawah (cocok untuk mobile).
    <x-shadcn::drawer title="Atur target" description="Target harian Anda.">
        <x-slot:trigger><x-filament::button color="gray">Buka drawer</x-filament::button></x-slot:trigger>
        …isi…
        <x-slot:footer><x-filament::button>Simpan</x-filament::button></x-slot:footer>
    </x-shadcn::drawer>

    Buka dari luar: $dispatch('open-drawer', { name: 'target' })  (beri prop name="target")
--}}
@props([
    'title' => null,
    'description' => null,
    'side' => 'bottom', // bottom | top | left | right
    'name' => null,
])

@php
    $id = 'sc-drawer-' . \Illuminate\Support\Str::random(8);
@endphp

<div
    x-data="{ open: false }"
    x-on:keydown.escape.window="open = false"
    x-on:open-drawer.window="if ($event.detail?.name === @js($name)) open = true"
    x-on:close-drawer.window="if (! $event.detail?.name || $event.detail.name === @js($name)) open = false"
    {{ $attributes->class(['sc-drawer-root']) }}
>
    @isset($trigger)
        <span class="sc-drawer-trigger" x-on:click="open = true">{{ $trigger }}</span>
    @endisset

    <template x-teleport="body">
        <div class="sc-drawer" data-side="{{ $side }}" x-show="open" x-cloak>
            <div
                class="sc-drawer-overlay"
                x-show="open"
                x-transition.opacity.duration.200ms
                x-on:click="open = false"
            ></div>

            <div
                class="sc-drawer-content"
                role="dialog"
                aria-modal="true"
                @if (filled($title)) aria-labelledby="{{ $id }}-title" @endif
                x-show="open"
                x-trap.noscroll="open"
                x-transition:enter="sc-drawer-transition"
                x-transition:enter-start="sc-drawer-hidden"
                x-transition:leave="sc-drawer-transition"
                x-transition:leave-end="sc-drawer-hidden"
            >
                @if (in_array($side, ['bottom', 'top'], true))
                    <div class="sc-drawer-handle" aria-hidden="true"></div>
                @endif

                @if (filled($title) || filled($description))
                    <div class="sc-drawer-header">
                        @if (filled($title))
                            <h2 id="{{ $id }}-title" class="sc-drawer-title">{{ $title }}</h2>
                        @endif
                        @if (filled($description))
                            <p class="sc-drawer-description">{{ $description }}</p>
                        @endif
                    </div>
                @endif

                <div class="sc-drawer-body">{{ $slot }}</div>

                @isset($footer)
                    <div class="sc-drawer-footer">{{ $footer }}</div>
                @endisset
            </div>
        </div>
    </template>
</div>
