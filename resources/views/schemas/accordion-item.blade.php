@php
    $value = $getValue();
    $id = 'sc-fi-accordion-' . $value . '-' . substr(md5($getStatePath() ?? $value), 0, 6);
@endphp

<div
    {{ $getExtraAttributeBag()->class(['sc-accordion-item']) }}
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
        >
            <span>{{ $getHeading() }}</span>
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
            {{ $getChildSchema() }}
        </div>
    </div>
</div>
