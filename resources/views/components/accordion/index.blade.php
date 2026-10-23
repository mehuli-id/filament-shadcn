{{--
    Accordion
    <x-shadcn::accordion type="single" default="pengiriman">
        <x-shadcn::accordion.item value="pengiriman" title="Berapa lama pengiriman?">…</x-shadcn::accordion.item>
    </x-shadcn::accordion>
--}}
@props([
    'type' => 'single', // single | multiple
    'collapsible' => true, // single: boleh menutup item yang sedang terbuka
    'default' => null, // value item yang terbuka di awal (string atau array)
])

<div
    x-data="{
        open: @js(array_values((array) ($default ?? []))),
        multiple: @js($type === 'multiple'),
        collapsible: @js((bool) $collapsible),
        isOpen(value) {
            return this.open.includes(value)
        },
        toggle(value) {
            if (this.isOpen(value)) {
                if (this.multiple || this.collapsible) {
                    this.open = this.open.filter((item) => item !== value)
                }

                return
            }

            this.open = this.multiple ? [...this.open, value] : [value]
        },
    }"
    {{ $attributes->class(['sc-accordion']) }}
>
    {{ $slot }}
</div>
