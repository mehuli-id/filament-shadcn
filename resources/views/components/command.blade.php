{{--
    Command — palet perintah dengan pencarian & navigasi keyboard.

    $items = [
        ['group' => 'Halaman', 'items' => [
            ['label' => 'Dasbor', 'url' => '/admin', 'shortcut' => 'G D'],
            ['label' => 'Pesanan', 'url' => '/admin/orders', 'keywords' => 'order invoice'],
        ]],
        ['group' => 'Aksi', 'items' => [
            ['label' => 'Buat produk', 'event' => 'create-product'],  // dispatch event Alpine/Livewire
        ]],
    ];

    Sebaris:  <x-shadcn::command :items="$items" />
    Dialog ⌘K: <x-shadcn::command :items="$items" dialog shortcut="k">
                   <x-slot:trigger><x-filament::button>Cari…</x-filament::button></x-slot:trigger>
               </x-shadcn::command>
--}}
@props([
    'items' => [],
    'placeholder' => null, // bawaan: terjemahan ui.command.placeholder
    'empty' => null,
    'dialog' => false,
    'shortcut' => 'k', // ⌘/Ctrl + tombol ini membuka dialog
    'name' => null, // buka dari luar: $dispatch('open-command', { name: '…' })
])

@php
    $placeholder ??= __('filament-shadcn::filament-shadcn.ui.command.placeholder');
    $empty ??= __('filament-shadcn::filament-shadcn.ui.command.empty');
    // URL item disaring di server (tolak javascript:, data:, …).
    $items = array_map(fn (array $group): array => [
        ...$group,
        'items' => array_map(fn (array $item): array => array_key_exists('url', $item)
            ? [...$item, 'url' => \Mehuli\FilamentShadcn\Support\SafeUrl::filter($item['url'])]
            : $item, $group['items'] ?? []),
    ], $items);

    $id = 'sc-command-' . \Illuminate\Support\Str::random(8);
@endphp

<div
    x-data="{
        query: '',
        open: @js(! $dialog),
        active: 0,
        groups: @js(array_values($items)),
        get filtered() {
            const query = this.query.toLowerCase().trim()

            return this.groups
                .map((group) => ({
                    ...group,
                    items: group.items.filter((item) => ! query || (item.label + ' ' + (item.keywords ?? '') + ' ' + (item.description ?? '')).toLowerCase().includes(query)),
                }))
                .filter((group) => group.items.length)
        },
        get flat() {
            return this.filtered.flatMap((group) => group.items)
        },
        indexOf(item) {
            return this.flat.indexOf(item)
        },
        move(step) {
            const total = this.flat.length
            if (! total) return
            this.active = (this.active + step + total) % total
            this.$nextTick(() => document.querySelector('#{{ $id }}-list [data-active=true]')?.scrollIntoView({ block: 'nearest' }))
        },
        select(item) {
            if (! item) return
            if (item.url) window.location.href = item.url
            if (item.event) this.$dispatch(item.event, item)
            if (@js((bool) $dialog)) this.close()
        },
        show() {
            this.open = true
            this.query = ''
            this.active = 0
            this.$nextTick(() => document.getElementById('{{ $id }}-input')?.focus())
        },
        close() {
            this.open = false
        },
    }"
    @if ($dialog)
        x-on:keydown.window="if ((($event.metaKey || $event.ctrlKey) && $event.key.toLowerCase() === @js(strtolower($shortcut)))) { $event.preventDefault(); open ? close() : show() }"
        x-on:open-command.window="if (! $event.detail?.name || $event.detail.name === @js($name)) show()"
    @endif
    {{ $attributes->class(['sc-command-root']) }}
>
    @if ($dialog && isset($trigger))
        <span class="sc-command-trigger" x-on:click="show()">{{ $trigger }}</span>
    @endif

    @if ($dialog)
        <template x-teleport="body">
            <div class="sc-command-dialog" x-show="open" x-cloak x-on:keydown.escape.window="close()">
                <div class="sc-command-overlay" x-on:click="close()" x-show="open" x-transition.opacity></div>
                <div class="sc-command-dialog-panel" role="dialog" aria-modal="true" aria-label="{{ $placeholder }}" x-show="open" x-transition x-trap="open">
    @endif

    <div class="sc-command">
        <div class="sc-command-input-wrapper">
            <x-shadcn::icon name="search" />
            <input
                id="{{ $id }}-input"
                type="text"
                class="sc-command-input"
                placeholder="{{ $placeholder }}"
                autocomplete="off"
                role="combobox"
                aria-expanded="true"
                aria-controls="{{ $id }}-list"
                x-bind:aria-activedescendant="flat.length ? '{{ $id }}-option-' + active : null"
                x-model="query"
                x-on:input="active = 0"
                x-on:keydown.arrow-down.prevent="move(1)"
                x-on:keydown.arrow-up.prevent="move(-1)"
                x-on:keydown.enter.prevent="select(flat[active])"
            />
        </div>

        <div id="{{ $id }}-list" class="sc-command-list" role="listbox">
            <template x-if="! flat.length">
                <div class="sc-command-empty">{{ $empty }}</div>
            </template>

            <template x-for="group in filtered" x-bind:key="group.group">
                <div class="sc-command-group" role="group" x-bind:aria-label="group.group">
                    <div class="sc-command-group-heading" x-text="group.group"></div>

                    <template x-for="item in group.items" x-bind:key="group.group + item.label">
                        <div
                            role="option"
                            class="sc-command-item"
                            x-bind:id="'{{ $id }}-option-' + indexOf(item)"
                            x-bind:data-active="indexOf(item) === active"
                            x-bind:aria-selected="indexOf(item) === active"
                            x-on:mousemove="active = indexOf(item)"
                            x-on:click="select(item)"
                        >
                            <span class="sc-command-item-label">
                                <span x-text="item.label"></span>
                                <template x-if="item.description">
                                    <span class="sc-command-item-description" x-text="item.description"></span>
                                </template>
                            </span>
                            <template x-if="item.shortcut">
                                <kbd class="sc-kbd" x-text="item.shortcut"></kbd>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        @isset($footer)
            <div class="sc-command-footer">{{ $footer }}</div>
        @endisset
    </div>

    @if ($dialog)
                </div>
            </div>
        </template>
    @endif
</div>
