<?php

namespace Mehuli\FilamentShadcn\Pages\Auth;

use Filament\Auth\Pages\EditProfile as FilamentEditProfile;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

/**
 * Halaman profil di dalam layout panel: form dibagi ke dua kartu (akun & kata sandi),
 * label di atas field, lebar dibatasi.
 *
 *   $panel->profile(\Mehuli\FilamentShadcn\Pages\Auth\EditProfile::class, isSimple: false)
 */
class EditProfile extends FilamentEditProfile
{
    public function defaultForm(Schema $schema): Schema
    {
        return parent::defaultForm($schema)->inlineLabel(false);
    }

    public function form(Schema $schema): Schema
    {
        $t = fn (string $key): string => __("filament-shadcn::filament-shadcn.profile.{$key}");

        return $schema
            ->components([
                Section::make($t('account'))
                    ->description($t('account_description'))
                    ->aside()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                    ]),
                Section::make($t('password'))
                    ->description($t('password_description'))
                    ->aside()
                    ->schema([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getCurrentPasswordFormComponent(),
                    ]),
            ]);
    }

    public function getMaxContentWidth(): Width | string | null
    {
        return static::isSimple() ? parent::getMaxContentWidth() : Width::FourExtraLarge;
    }
}
