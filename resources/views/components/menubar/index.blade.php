{{--
    Menubar — bilah menu ala aplikasi desktop.
    <x-shadcn::menubar>
        <x-shadcn::menubar.menu label="Berkas">
            <x-shadcn::menu.item shortcut="⌘N">Baru</x-shadcn::menu.item>
            <x-shadcn::menu.separator />
            <x-shadcn::menu.item>Ekspor</x-shadcn::menu.item>
        </x-shadcn::menubar.menu>
    </x-shadcn::menubar>
--}}
<div
    role="menubar"
    x-data="{ active: null }"
    x-on:click.outside="active = null"
    x-on:keydown.escape="active = null"
    {{ $attributes->class(['sc-menubar']) }}
>
    {{ $slot }}
</div>
