@php
    $percentage = $getPercentage();
    $color = $getColor() ?? 'primary';
    $color = is_string($color) ? $color : 'primary';
@endphp

<div {{ $getExtraAttributeBag()->class(['sc-fi-progress']) }} style="padding: 0.75rem;">
    <div
        role="progressbar"
        aria-valuemin="0"
        aria-valuemax="{{ $getMaxValue() }}"
        aria-valuenow="{{ (float) $getState() }}"
        aria-label="{{ strip_tags((string) $getLabel()) }}"
        class="sc-progress sc-progress-{{ $color }}"
    >
        <div class="sc-progress-indicator" style="width: {{ $percentage }}%"></div>
    </div>

    @if ($shouldShowValue())
        <span class="sc-fi-progress-value">{{ round($percentage) }}%</span>
    @endif
</div>
