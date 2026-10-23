<?php

namespace Mehuli\FilamentShadcn\Support;

/**
 * Menyaring URL untuk atribut href di komponen <x-shadcn::…>.
 * Blade sudah meng-escape karakter HTML, tetapi tidak menolak skema
 * berbahaya seperti `javascript:` yang bisa datang dari data user.
 */
class SafeUrl
{
    /**
     * Skema yang ditolak. Bisa ditambah lewat config `filament-shadcn.blocked_url_schemes`.
     */
    protected const BLOCKED = ['javascript', 'vbscript', 'data', 'file'];

    public static function filter(mixed $url): ?string
    {
        if (! is_string($url) || ($url = trim($url)) === '') {
            return null;
        }

        // Buang karakter kontrol/spasi yang dipakai untuk menyamarkan skema (mis. "java\tscript:").
        $normalized = strtolower(preg_replace('/[\x00-\x20\x7f]+/', '', $url) ?? '');

        if (preg_match('/^([a-z][a-z0-9+.\-]*):/', $normalized, $match)) {
            $blocked = (array) (function_exists('config') ? config('filament-shadcn.blocked_url_schemes', static::BLOCKED) : static::BLOCKED);

            if (in_array($match[1], $blocked, true)) {
                return null;
            }
        }

        return $url;
    }
}
