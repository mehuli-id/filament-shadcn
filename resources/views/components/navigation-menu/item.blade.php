@props([
    'label',
    'href' => null,
    'active' => false,
    'columns' => 2, // kolom panel dropdown
])

@php
    $key = 'sc-nav-' . \Illuminate\Support\Str::random(8);
    $hasPanel = trim((string) $slot) !== '';
@endphp

<li class="sc-navigation-menu-item" {{ $attributes }}>
    @if ($hasPanel)
        <button
            type="button"
            class="sc-navigation-menu-trigger"
            aria-controls="{{ $key }}"
            x-bind:aria-expanded="active === @js($key)"
            x-bind:data-state="active === @js($key) ? 'open' : 'closed'"
            {{-- Hover membuka panel; klik langsung sesudah hover tidak menutupnya lagi. --}}
            x-on:click="active = (active === @js($key) && Date.now() - openedAt > 400) ? null : @js($key)"
            x-on:mouseenter="if (active !== @js($key)) { active = @js($key); openedAt = Date.now() }"
        >
            {{ $label }}
            <x-shadcn::icon name="chevron-down" size="12" class="sc-navigation-menu-chevron" />
        </button>

        <div
            id="{{ $key }}"
            class="sc-navigation-menu-panel sc-floating"
            data-side="bottom"
            data-align="start"
            style="--sc-nav-columns: {{ max(1, (int) $columns) }}"
            x-show="active === @js($key)"
            x-cloak
            x-transition.opacity.duration.150ms
            x-on:mouseleave="active = null"
        >
            {{ $slot }}
        </div>
    @else
        <a
            href="{{ \Mehuli\FilamentShadcn\Support\SafeUrl::filter($href) }}"
            @class(['sc-navigation-menu-trigger', 'sc-navigation-menu-link-active' => $active])
            @if ($active) aria-current="page" @endif
        >{{ $label }}</a>
    @endif
</li>
