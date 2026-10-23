@props([
    'value' => null,
    'title' => null,
    'disabled' => false,
])

@php
    $value ??= 'item-' . \Illuminate\Support\Str::random(8);
    $id = 'sc-accordion-' . \Illuminate\Support\Str::random(8);
@endphp

<div
    {{ $attributes->class(['sc-accordion-item']) }}
    x-bind:data-state="isOpen(@js($value)) ? 'open' : 'closed'"
>
    <h3 class="sc-accordion-heading">
        <button
            type="button"
            id="{{ $id }}-trigger"
            class="sc-accordion-trigger"
            aria-controls="{{ $id }}-content"
            x-bind:aria-expanded="isOpen(@js($value))"
            x-on:click="toggle(@js($value))"
            @disabled($disabled)
        >
            <span>{{ $title ?? ($trigger ?? '') }}</span>
            <x-shadcn::icon name="chevron-down" class="sc-accordion-chevron" />
        </button>
    </h3>

    <div
        id="{{ $id }}-content"
        role="region"
        aria-labelledby="{{ $id }}-trigger"
        class="sc-accordion-content"
        x-show="isOpen(@js($value))"
        x-collapse
        x-cloak
    >
        <div class="sc-accordion-content-inner">
            {{ $slot }}
        </div>
    </div>
</div>
