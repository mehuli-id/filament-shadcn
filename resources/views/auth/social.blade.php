{{-- Tombol masuk dengan layanan lain (auth.social). Hanya tampilan: URL mengarah ke rute OAuth aplikasi. --}}
@php
    $knownIcons = ['google', 'microsoft', 'github'];
    $t = fn (string $key, array $replace = []): string => __("filament-shadcn::filament-shadcn.auth.social.{$key}", $replace);
@endphp

<div @class(['sc-auth-social', 'sc-auth-social-compact' => $compact, 'sc-auth-social-after' => ! $before])>
    @unless ($before)
        <div class="sc-auth-divider"><span>{{ $t('divider') }}</span></div>
    @endunless

    <div class="sc-auth-social-buttons">
        @foreach ($providers as $provider)
            <a
                href="{{ $provider['url'] }}"
                class="sc-auth-social-btn"
                @if ($compact) aria-label="{{ $t('continue_with', ['provider' => $provider['label']]) }}" title="{{ $provider['label'] }}" @endif
            >
                @if (in_array($provider['icon'], $knownIcons, true))
                    @include("filament-shadcn::partials.brand-icons.{$provider['icon']}")
                @elseif (filled($provider['icon']))
                    {{ \Filament\Support\generate_icon_html($provider['icon']) }}
                @endif

                @unless ($compact)
                    <span>{{ $t('continue_with', ['provider' => $provider['label']]) }}</span>
                @endunless
            </a>
        @endforeach
    </div>

    @if ($before)
        <div class="sc-auth-divider"><span>{{ $t('divider_email') }}</span></div>
    @endif
</div>
