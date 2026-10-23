{{-- Kartu statistik shadcn: label + badge tren, nilai, baris keterangan + petunjuk. --}}
@php
    $url = $getUrl();
    $tag = $url ? 'a' : 'div';
    $trendIcon = $getTrendIcon();
    $descriptionIcon = $getDescriptionIcon() ?? $trendIcon;
    $trendState = match ($isTrendUp()) {
        true => 'up',
        false => 'down',
        default => 'flat',
    };
@endphp

<{!! $tag !!}
    @if ($url)
        {{ \Filament\Support\generate_href_html($url, $shouldOpenUrlInNewTab()) }}
    @endif
    {{
        $getExtraAttributeBag()
            ->class([
                'fi-wi-stats-overview-stat',
                'sc-stat',
            ])
            ->merge(['data-trend' => $trendState], escape: false)
    }}
>
    <div class="sc-stat-head">
        <span class="sc-stat-label">
            {{ \Filament\Support\generate_icon_html($getIcon()) }}
            {{ $getLabel() }}
        </span>

        @if (filled($trend = $getTrend()))
            <span class="sc-stat-trend">
                {{ \Filament\Support\generate_icon_html($trendIcon) }}
                {{ $trend }}
            </span>
        @endif
    </div>

    @if (filled($value = $getValue()))
        <div class="sc-stat-value">{{ $value }}</div>
    @else
        <div class="sc-stat-value fi-wi-stats-overview-stat-placeholder">{{ $getPlaceholder() }}</div>
    @endif

    @if (filled($description = $getDescription()) || filled($getHint()))
        <div class="sc-stat-foot">
            @if (filled($description))
                <span class="sc-stat-description">
                    {{ $description }}
                    {{ \Filament\Support\generate_icon_html($descriptionIcon) }}
                </span>
            @endif

            @if (filled($hint = $getHint()))
                <span class="sc-stat-hint">{{ $hint }}</span>
            @endif
        </div>
    @endif
</{!! $tag !!}>
