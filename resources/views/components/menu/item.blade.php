{{--
    Item menu — dipakai Context Menu, Menubar, Popover, dll.
    <x-shadcn::menu.item href="/admin/orders" shortcut="⌘O">Pesanan</x-shadcn::menu.item>
    <x-shadcn::menu.item destructive wire:click="delete">Hapus</x-shadcn::menu.item>
--}}
@props([
    'href' => null,
    'shortcut' => null,
    'destructive' => false,
    'disabled' => false,
    'inset' => false,
])

@php
    $classes = [
        'sc-menu-item',
        'sc-menu-item-destructive' => $destructive,
        'sc-menu-item-inset' => $inset,
    ];
@endphp

@if (filled($href) && ! $disabled)
    <a href="{{ \Mehuli\FilamentShadcn\Support\SafeUrl::filter($href) }}" role="menuitem" tabindex="-1" {{ $attributes->class($classes) }}>
        @isset($icon) <span class="sc-menu-item-icon">{{ $icon }}</span> @endisset
        <span class="sc-menu-item-label">{{ $slot }}</span>
        @if (filled($shortcut)) <span class="sc-menu-shortcut">{{ $shortcut }}</span> @endif
    </a>
@else
    <button type="button" role="menuitem" tabindex="-1" @disabled($disabled) {{ $attributes->class($classes) }}>
        @isset($icon) <span class="sc-menu-item-icon">{{ $icon }}</span> @endisset
        <span class="sc-menu-item-label">{{ $slot }}</span>
        @if (filled($shortcut)) <span class="sc-menu-shortcut">{{ $shortcut }}</span> @endif
    </button>
@endif
