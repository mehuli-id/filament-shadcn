@props([
    'title',
    'description' => null,
    'status' => null, // null (selesai) | running | error | pending
    'open' => false,
    'href' => null,
])

@php
    $status ??= 'done';
    $hasDetail = trim((string) $slot) !== '';
    $defaultIcon = match ($status) {
        'running' => 'loader',
        'error' => 'alert',
        'pending' => 'circle',
        default => 'circle-check',
    };
@endphp

<div
    role="listitem"
    {{ $attributes->class(['sc-trace-item']) }}
    data-status="{{ $status }}"
    @if ($hasDetail) x-data="{ open: @js((bool) $open) }" x-bind:data-state="open ? 'open' : 'closed'" @endif
>
    @if ($hasDetail)
        <button type="button" class="sc-trace-line" x-on:click="open = ! open" x-bind:aria-expanded="open">
    @elseif (filled($href))
        <a href="{{ \Mehuli\FilamentShadcn\Support\SafeUrl::filter($href) }}" class="sc-trace-line">
    @else
        <div class="sc-trace-line">
    @endif

        <span class="sc-trace-icon" aria-hidden="true">
            @isset($icon)
                {{ $icon }}
            @else
                <x-shadcn::icon :name="$defaultIcon" size="14" />
            @endisset
        </span>

        <span @class(['sc-trace-title', 'sc-shimmer' => $status === 'running'])>{{ $title }}</span>

        @if (filled($description))
            <span class="sc-trace-description">{{ $description }}</span>
        @endif

        @if ($status === 'error')
            <span class="sc-sr-only">(gagal)</span>
        @elseif ($status === 'running')
            <span class="sc-sr-only">(sedang berjalan)</span>
        @elseif ($status === 'pending')
            <span class="sc-sr-only">(menunggu)</span>
        @endif

        @if ($hasDetail)
            <x-shadcn::icon name="chevron-right" size="14" class="sc-trace-chevron" />
        @endif

    @if ($hasDetail)
        </button>
    @elseif (filled($href))
        </a>
    @else
        </div>
    @endif

    @if ($hasDetail)
        <div class="sc-trace-content" x-show="open" x-collapse x-cloak>
            {{ $slot }}
        </div>
    @endif
</div>
