{{-- Pilihan tema terang / gelap / sistem (3 tombol). Memakai kunci localStorage & event Filament. --}}
@php
    use Filament\Support\Icons\Heroicon;
@endphp

<div
    x-data="{ theme: null }"
    x-init="
        $watch('theme', () => $dispatch('theme-changed', theme))
        theme = localStorage.getItem('filament:theme:mode') || @js(filament()->getDefaultThemeMode()->value)
    "
    role="radiogroup"
    aria-label="{{ __('filament-panels::layout.actions.theme_switcher.label') }}"
    class="sc-theme-switcher"
>
    @foreach (['light' => Heroicon::OutlinedSun, 'dark' => Heroicon::OutlinedMoon, 'system' => Heroicon::OutlinedComputerDesktop] as $mode => $icon)
        <button
            type="button"
            role="radio"
            x-on:click="theme = @js($mode)"
            x-bind:aria-checked="theme === @js($mode) ? 'true' : 'false'"
            x-bind:class="{ 'sc-active': theme === @js($mode) }"
            aria-label="{{ __("filament-panels::layout.actions.theme_switcher.{$mode}.label") }}"
            title="{{ __("filament-panels::layout.actions.theme_switcher.{$mode}.label") }}"
            class="sc-theme-switcher-btn"
        >
            {{ \Filament\Support\generate_icon_html($icon) }}
        </button>
    @endforeach
</div>
