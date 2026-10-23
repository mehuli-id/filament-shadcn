{{--
    Carousel (scroll-snap + Alpine).
    <x-shadcn::carousel :per-view="3" label="Produk unggulan">
        <x-shadcn::carousel.item>…</x-shadcn::carousel.item>
    </x-shadcn::carousel>
--}}
@props([
    'perView' => 1, // jumlah slide yang terlihat sekaligus
    'loop' => true,
    'dots' => true,
    'label' => null, // bawaan: terjemahan ui.carousel.label
])

<div
    x-data="{
        index: 0,
        count: 0,
        loop: @js((bool) $loop),
        init() {
            this.count = this.$refs.track.children.length
            this.$refs.track.addEventListener('scroll', () => {
                const items = [...this.$refs.track.children]
                const left = this.$refs.track.scrollLeft
                let closest = 0
                items.forEach((item, i) => {
                    if (Math.abs(item.offsetLeft - left) < Math.abs(items[closest].offsetLeft - left)) closest = i
                })
                this.index = closest
            }, { passive: true })
        },
        go(target) {
            if (! this.count) return
            if (this.loop) target = (target + this.count) % this.count
            target = Math.max(0, Math.min(this.count - 1, target))
            const item = this.$refs.track.children[target]
            this.$refs.track.scrollTo({ left: item.offsetLeft - this.$refs.track.offsetLeft, behavior: 'smooth' })
        },
    }"
    x-on:keydown.arrow-left.prevent="go(index - 1)"
    x-on:keydown.arrow-right.prevent="go(index + 1)"
    role="region"
    aria-roledescription="carousel"
    aria-label="{{ $label ?? __('filament-shadcn::filament-shadcn.ui.carousel.label') }}"
    tabindex="0"
    {{ $attributes->class(['sc-carousel'])->style(['--sc-carousel-per-view: ' . max(1, (int) $perView)]) }}
>
    <div class="sc-carousel-viewport">
        <div class="sc-carousel-track" x-ref="track">
            {{ $slot }}
        </div>
    </div>

    <button
        type="button"
        class="sc-carousel-button sc-carousel-previous"
        aria-label="{{ __('filament-shadcn::filament-shadcn.ui.carousel.previous') }}"
        x-on:click="go(index - 1)"
        x-bind:disabled="! loop && index === 0"
    >
        <x-shadcn::icon name="chevron-left" />
    </button>

    <button
        type="button"
        class="sc-carousel-button sc-carousel-next"
        aria-label="{{ __('filament-shadcn::filament-shadcn.ui.carousel.next') }}"
        x-on:click="go(index + 1)"
        x-bind:disabled="! loop && index >= count - 1"
    >
        <x-shadcn::icon name="chevron-right" />
    </button>

    @if ($dots)
        <div class="sc-carousel-dots">
            <template x-for="i in count" x-bind:key="i">
                <button
                    type="button"
                    class="sc-carousel-dot"
                    x-bind:aria-label="'Ke slide ' + i"
                    x-bind:aria-current="index === i - 1"
                    x-on:click="go(i - 1)"
                ></button>
            </template>
        </div>
    @endif
</div>
