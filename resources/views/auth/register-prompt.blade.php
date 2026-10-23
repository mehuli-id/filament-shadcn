{{-- "Belum punya akun? Daftar" di bawah form login. --}}
<p class="sc-auth-register">
    {{ __('filament-shadcn::filament-shadcn.auth.login.register_prompt') }}
    <a href="{{ filament()->getRegistrationUrl() }}">{{ __('filament-shadcn::filament-shadcn.auth.login.register') }}</a>
</p>
