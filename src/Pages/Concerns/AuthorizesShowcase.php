<?php

namespace Mehuli\FilamentShadcn\Pages\Concerns;

use Mehuli\FilamentShadcn\ShadcnPlugin;

/**
 * Halaman Showcase hanya untuk yang boleh mengatur tampilan (canCustomizeAppearance;
 * bawaan: hanya APP_ENV=local), dan hanya bila showcase diaktifkan.
 */
trait AuthorizesShowcase
{
    public static function canAccess(): bool
    {
        $plugin = ShadcnPlugin::get();

        return $plugin->hasShowcase() && $plugin->canAccessAppearance();
    }
}
