{{--
    Navigation Menu — navigasi situs dengan panel dropdown.
    <x-shadcn::navigation-menu>
        <x-shadcn::navigation-menu.item label="Produk">
            <x-shadcn::navigation-menu.link href="/produk" title="Semua produk" description="Katalog lengkap." />
        </x-shadcn::navigation-menu.item>
        <x-shadcn::navigation-menu.item label="Dokumentasi" href="/docs" />
    </x-shadcn::navigation-menu>
--}}
<nav
    x-data="{ active: null, openedAt: 0 }"
    x-on:click.outside="active = null"
    x-on:keydown.escape="active = null"
    {{ $attributes->class(['sc-navigation-menu']) }}
>
    <ul class="sc-navigation-menu-list">
        {{ $slot }}
    </ul>
</nav>
