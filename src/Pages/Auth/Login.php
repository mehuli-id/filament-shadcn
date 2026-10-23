<?php

namespace Mehuli\FilamentShadcn\Pages\Auth;

use Filament\Auth\Pages\Login as FilamentLogin;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Halaman login sesuai docs/mockup: "Selamat datang kembali", subjudul dengan nama merek,
 * dan tautan "Belum punya akun? Daftar" di bawah form.
 *
 *   $panel->login(\Mehuli\FilamentShadcn\Pages\Auth\Login::class)
 */
class Login extends FilamentLogin
{
    public function getHeading(): string | Htmlable | null
    {
        if (filled($this->userUndertakingMultiFactorAuthentication)) {
            return parent::getHeading();
        }

        return __('filament-shadcn::filament-shadcn.auth.login.heading');
    }

    public function getSubheading(): string | Htmlable | null
    {
        if (filled($this->userUndertakingMultiFactorAuthentication)) {
            return parent::getSubheading();
        }

        return __('filament-shadcn::filament-shadcn.auth.login.subheading', ['brand' => filament()->getBrandName()]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                RenderHook::make(PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE),
                $this->getFormContentComponent(),
                $this->getMultiFactorChallengeFormContentComponent(),
                View::make('filament-shadcn::auth.register-prompt')
                    ->visible(fn (): bool => filament()->hasRegistration() && blank($this->userUndertakingMultiFactorAuthentication)),
                RenderHook::make(PanelsRenderHook::AUTH_LOGIN_FORM_AFTER),
            ]);
    }
}
