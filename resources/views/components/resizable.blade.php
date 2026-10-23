{{--
    Resizable — dua panel yang ukurannya bisa diseret (mouse, sentuh, atau panah keyboard).
    <x-shadcn::resizable :default="30" direction="horizontal" with-handle style="height: 20rem">
        <x-slot:start>Panel kiri</x-slot:start>
        <x-slot:end>Panel kanan</x-slot:end>
    </x-shadcn::resizable>
--}}
@props([
    'direction' => 'horizontal', // horizontal | vertical
    'default' => 50, // ukuran panel pertama (%)
    'min' => 10,
    'max' => 90,
    'withHandle' => false,
    'label' => null, // bawaan: terjemahan ui.resizable.label
])

<div
    x-data="{
        size: {{ (float) $default }},
        dragging: false,
        horizontal: @js($direction === 'horizontal'),
        clamp(value) {
            return Math.min({{ (float) $max }}, Math.max({{ (float) $min }}, Math.round(value * 10) / 10))
        },
        start(event) {
            this.dragging = true
            event.target.setPointerCapture(event.pointerId)
        },
        move(event) {
            if (! this.dragging) return
            const rect = this.$root.getBoundingClientRect()
            let ratio = this.horizontal ? (event.clientX - rect.left) / rect.width : (event.clientY - rect.top) / rect.height
            if (this.horizontal && document.dir === 'rtl') ratio = 1 - ratio
            this.size = this.clamp(ratio * 100)
        },
        stop() {
            this.dragging = false
        },
    }"
    data-direction="{{ $direction }}"
    x-bind:data-dragging="dragging"
    {{ $attributes->class(['sc-resizable']) }}
>
    <div class="sc-resizable-panel" x-bind:style="`flex-basis: ${size}%`">
        {{ $start ?? '' }}
    </div>

    <div
        role="separator"
        tabindex="0"
        aria-label="{{ $label ?? __('filament-shadcn::filament-shadcn.ui.resizable.label') }}"
        aria-orientation="{{ $direction === 'horizontal' ? 'vertical' : 'horizontal' }}"
        aria-valuemin="{{ $min }}"
        aria-valuemax="{{ $max }}"
        x-bind:aria-valuenow="size"
        class="sc-resizable-handle"
        x-on:pointerdown="start($event)"
        x-on:pointermove="move($event)"
        x-on:pointerup="stop()"
        x-on:pointercancel="stop()"
        x-on:keydown.arrow-left.prevent="size = clamp(size - 2)"
        x-on:keydown.arrow-up.prevent="size = clamp(size - 2)"
        x-on:keydown.arrow-right.prevent="size = clamp(size + 2)"
        x-on:keydown.arrow-down.prevent="size = clamp(size + 2)"
        x-on:keydown.home.prevent="size = {{ (float) $min }}"
        x-on:keydown.end.prevent="size = {{ (float) $max }}"
    >
        @if ($withHandle)
            <span class="sc-resizable-grip">
                <x-shadcn::icon :name="$direction === 'horizontal' ? 'grip-vertical' : 'grip-horizontal'" size="12" />
            </span>
        @endif
    </div>

    <div class="sc-resizable-panel sc-resizable-panel-end">
        {{ $end ?? '' }}
    </div>
</div>
