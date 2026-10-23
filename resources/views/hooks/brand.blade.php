{{-- Blok merek di kepala sidebar: kotak ikon + nama + subjudul. Dengan ->brandLogo(), logo Filament dipakai apa adanya. --}}
@if (blank(filament()->getBrandLogo()))
    <a
        href="{{ filament()->getHomeUrl() ?? '#' }}"
        @if (filament()->isSidebarCollapsibleOnDesktop() || filament()->isSidebarFullyCollapsibleOnDesktop())
            x-show="$store.sidebar.isOpen"
        @endif
        class="sc-brand"
    >
        @if (filled($icon))
            <span class="sc-brand-icon" aria-hidden="true">{{ \Filament\Support\generate_icon_html($icon) }}</span>
        @endif

        <span class="sc-brand-text">
            <span class="sc-brand-name">{{ filament()->getBrandName() }}</span>
            @if (filled($subtitle))
                <span class="sc-brand-subtitle">{{ $subtitle }}</span>
            @endif
        </span>
    </a>
@endif
