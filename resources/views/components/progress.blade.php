{{--
    Progress — <x-shadcn::progress :value="64" label="Unggah berkas" show-value />
    Tanpa nilai (memuat): <x-shadcn::progress indeterminate label="Memuat" />
--}}
@props([
    'value' => 0,
    'max' => 100,
    'label' => null,
    'size' => 'default', // sm | default | lg
    'color' => 'primary', // primary | success | warning | danger | info
    'indeterminate' => false,
    'showValue' => false,
])

@php
    $percent = $max > 0 ? max(0, min(100, ($value / $max) * 100)) : 0;
@endphp

<div {{ $attributes->class(['sc-progress-wrapper']) }}>
    @if (filled($label) || $showValue)
        <div class="sc-progress-meta">
            <span>{{ $label }}</span>
            @if ($showValue && ! $indeterminate)
                <span class="sc-progress-value">{{ round($percent) }}%</span>
            @endif
        </div>
    @endif

    <div
        role="progressbar"
        aria-valuemin="0"
        aria-valuemax="{{ $max }}"
        @unless ($indeterminate) aria-valuenow="{{ $value }}" @endunless
        @if (filled($label)) aria-label="{{ $label }}" @endif
        @class([
            'sc-progress',
            'sc-progress-size-' . $size,
            'sc-progress-' . $color,
            'sc-progress-indeterminate' => $indeterminate,
        ])
    >
        <div class="sc-progress-indicator" @unless ($indeterminate) style="width: {{ $percent }}%" @endunless></div>
    </div>
</div>
