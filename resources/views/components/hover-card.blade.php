{{--
    Hover Card — kartu pratinjau saat kursor/fokus berada di pemicu.
    <x-shadcn::hover-card>
        <x-slot:trigger><a href="#">@budi</a></x-slot:trigger>
        …isi kartu…
    </x-shadcn::hover-card>
--}}
@props([
    'side' => 'bottom', // bottom | top
    'align' => 'center', // start | center | end
    'openDelay' => 400,
    'closeDelay' => 200,
    'width' => '20rem',
])

<span
    x-data="{
        open: false,
        timer: null,
        enter() {
            clearTimeout(this.timer)
            this.timer = setTimeout(() => (this.open = true), {{ (int) $openDelay }})
        },
        leave() {
            clearTimeout(this.timer)
            this.timer = setTimeout(() => (this.open = false), {{ (int) $closeDelay }})
        },
    }"
    x-on:mouseenter="enter()"
    x-on:mouseleave="leave()"
    x-on:focusin="enter()"
    x-on:focusout="leave()"
    x-on:keydown.escape="open = false"
    {{-- Layar sentuh tidak punya hover: ketuk pertama membuka kartu, ketuk di luar menutupnya. --}}
    x-on:click="if (window.matchMedia('(hover: none)').matches && ! open) { $event.preventDefault(); open = true }"
    x-on:click.outside="open = false"
    {{ $attributes->class(['sc-hover-card']) }}
>
    {{ $trigger ?? '' }}

    <div
        class="sc-floating sc-hover-card-content"
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
