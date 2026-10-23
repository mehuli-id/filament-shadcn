{{-- Teks persetujuan di bawah kartu login (aktif bila auth.terms_url / auth.privacy_url diisi). --}}
@php
    $link = fn (?string $url, string $label): string => filled($url)
        ? '<a href="' . e($url) . '" target="_blank" rel="noopener noreferrer">' . e($label) . '</a>'
        : e($label);
@endphp

<p class="sc-auth-terms">
    {!! __('filament-shadcn::filament-shadcn.auth.terms', [
        'terms' => $link($termsUrl, __('filament-shadcn::filament-shadcn.auth.terms_label')),
        'privacy' => $link($privacyUrl, __('filament-shadcn::filament-shadcn.auth.privacy_label')),
    ]) !!}
</p>
