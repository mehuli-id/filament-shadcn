{{-- Awal halaman login/daftar/lupa sandi (docs/mockup/gambar/LoginMuted.png & LoginImage.png):
     pilihan tema di kanan atas, merek di atas kartu (muted/plain), panel gambar (split). --}}
@if ($isGuest)
    <div class="sc-auth-top">
        @if (filament()->hasDarkMode() && (! filament()->hasDarkModeForced()))
            @include('filament-shadcn::partials.theme-switcher')
        @endif
    </div>

    @if ($showBrand && $layout !== 'split')
        <a href="{{ filament()->getHomeUrl() ?? url('/') }}" class="sc-auth-brand">
            <span class="sc-brand-icon" aria-hidden="true">{{ \Filament\Support\generate_icon_html($icon) }}</span>
            {{ filament()->getBrandName() }}
        </a>
    @endif

    @if ($layout === 'split')
        @include('filament-shadcn::hooks.auth-aside')
    @endif
@endif
