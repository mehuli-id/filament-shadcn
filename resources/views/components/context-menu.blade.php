{{--
    Context Menu — menu klik kanan.
    <x-shadcn::context-menu>
        <div class="area">Klik kanan di sini</div>
        <x-slot:menu>
            <x-shadcn::menu.item shortcut="⌘[">Kembali</x-shadcn::menu.item>
            <x-shadcn::menu.separator />
            <x-shadcn::menu.item destructive>Hapus</x-shadcn::menu.item>
        </x-slot:menu>
    </x-shadcn::context-menu>
--}}
@php
    $id = 'sc-context-menu-' . \Illuminate\Support\Str::random(8);
@endphp

<div
    x-data="{
        open: false,
        x: 0,
        y: 0,
        openedAt: 0,
        show(event) {
            this.open = true
            this.openedAt = Date.now()
            this.$nextTick(() => {
                const menu = document.getElementById('{{ $id }}')
                const width = menu.offsetWidth
                const height = menu.offsetHeight
                this.x = Math.min(event.clientX, window.innerWidth - width - 8)
                this.y = Math.min(event.clientY, window.innerHeight - height - 8)
                menu.querySelector('[role=menuitem]:not([disabled])')?.focus()
            })
        },
    }"
    x-on:contextmenu.prevent="show($event)"
    x-on:keydown.escape.window="open = false"
    x-on:click.window="open = false"
    x-on:scroll.window="if (Date.now() - openedAt > 200) open = false"
    x-on:resize.window="open = false"
    {{ $attributes->class(['sc-context-menu']) }}
>
    {{ $slot }}

    <template x-teleport="body">
        <div
            id="{{ $id }}"
            x-show="open"
            x-cloak
            x-transition.opacity.duration.100ms
            role="menu"
            class="sc-menu-content sc-context-menu-content"
            x-bind:style="`left: ${x}px; top: ${y}px`"
            x-on:keydown.arrow-down.prevent="$focus.wrap().next()"
            x-on:keydown.arrow-up.prevent="$focus.wrap().previous()"
            x-on:click="open = false"
        >
            {{ $menu ?? '' }}
        </div>
    </template>
</div>
