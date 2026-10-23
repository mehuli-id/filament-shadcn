{{--
    Message Scroller — area transkrip percakapan yang ikut menggulir saat
    pesan baru masuk (mis. dari Livewire / streaming), dengan tombol
    "ke pesan terbaru" ketika pembaca menggulir ke atas.

    <x-shadcn::message-scroller height="28rem">
        @foreach ($messages as $message) <x-shadcn::message …/> @endforeach
    </x-shadcn::message-scroller>
--}}
@props([
    'height' => '24rem',
    'autoScroll' => true,
    'defaultPosition' => 'end', // start | end
    'threshold' => 32, // px dari bawah yang masih dianggap "di ujung"
])

<div
    x-data="{
        atEnd: true,
        follow: @js((bool) $autoScroll),
        check() {
            const viewport = this.$refs.viewport
            this.atEnd = viewport.scrollHeight - viewport.scrollTop - viewport.clientHeight <= {{ (int) $threshold }}
        },
        toEnd(smooth = true) {
            const viewport = this.$refs.viewport
            viewport.scrollTo({ top: viewport.scrollHeight, behavior: smooth ? 'smooth' : 'auto' })
            this.follow = @js((bool) $autoScroll)
        },
        init() {
            this.$nextTick(() => {
                if (@js($defaultPosition) === 'end') this.toEnd(false)
                this.check()
            })

            new MutationObserver(() => {
                if (this.follow && this.atEnd) {
                    this.$nextTick(() => this.toEnd(false))
                } else {
                    this.check()
                }
            }).observe(this.$refs.content, { childList: true, subtree: true, characterData: true })
        },
    }"
    {{ $attributes->class(['sc-message-scroller'])->style(['height: ' . $height]) }}
>
    <div class="sc-message-scroller-viewport" x-ref="viewport" x-on:scroll.passive="check()" tabindex="0">
        <div class="sc-message-scroller-content" x-ref="content" role="log" aria-live="polite" aria-relevant="additions">
            {{ $slot }}
        </div>
    </div>

    <button
        type="button"
        class="sc-message-scroller-button"
        aria-label="{{ __('filament-shadcn::filament-shadcn.ui.message_scroller.latest') }}"
        x-bind:data-active="! atEnd"
        x-bind:tabindex="atEnd ? -1 : 0"
        x-bind:aria-hidden="atEnd"
        x-on:click="toEnd()"
    >
        <x-shadcn::icon name="arrow-down" />
    </button>
</div>
