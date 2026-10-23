@props([
    'label',
])

@php
    $key = 'sc-menubar-' . \Illuminate\Support\Str::random(8);
@endphp

<div class="sc-menubar-menu" {{ $attributes }}>
    <button
        type="button"
        role="menuitem"
        aria-haspopup="menu"
        class="sc-menubar-trigger"
        x-bind:data-state="active === @js($key) ? 'open' : 'closed'"
        x-bind:aria-expanded="active === @js($key)"
        x-on:click="active = active === @js($key) ? null : @js($key)"
        x-on:mouseenter="if (active !== null) active = @js($key)"
        x-on:keydown.arrow-down.prevent="active = @js($key); $nextTick(() => $el.nextElementSibling.querySelector('[role=menuitem]')?.focus())"
    >{{ $label }}</button>

    <div
        role="menu"
        class="sc-menu-content sc-floating"
        data-side="bottom"
        data-align="start"
        x-show="active === @js($key)"
        {{-- Geser panel agar tetap di dalam layar (ponsel/tablet). --}}
        x-effect="if (active === @js($key)) $nextTick(() => { $el.style.marginLeft = ''; const r = $el.getBoundingClientRect(), pad = 16; let dx = 0; if (r.right > document.documentElement.clientWidth - pad) dx = document.documentElement.clientWidth - pad - r.right; if (r.left + dx < pad) dx = pad - r.left; if (dx) $el.style.marginLeft = dx + 'px' })"
        x-cloak
        x-transition.opacity.duration.100ms
        x-on:click="if ($event.target.closest('[role=menuitem]')) active = null"
        x-on:keydown.arrow-down.prevent="$focus.wrap().next()"
        x-on:keydown.arrow-up.prevent="$focus.wrap().previous()"
    >
        {{ $slot }}
    </div>
</div>
