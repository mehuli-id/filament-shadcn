<?php

namespace Mehuli\FilamentShadcn\Support;

/**
 * Menyaring URL untuk atribut href (komponen <x-shadcn::…>, tautan header, tombol masuk sosial,
 * teks persetujuan login). Blade sudah meng-escape karakter HTML, tetapi tidak menolak skema
 * berbahaya seperti `javascript:` yang bisa datang dari data user.
 *
 * Daftar izin: hanya skema di `allowed_url_schemes` (bawaan http, https, mailto, tel) dan URL
 * relatif (`/…`, `#…`, `?…`, `halaman`). Skema lain apa pun ditolak.
 */
class SafeUrl
{
    protected const ALLOWED = ['http', 'https', 'mailto', 'tel'];

    public static function filter(mixed $url): ?string
    {
        if (! is_string($url) || ($url = trim($url)) === '') {
            return null;
        }

        // Buang karakter kontrol/spasi yang dipakai untuk menyamarkan skema (mis. "java\tscript:").
        $normalized = strtolower(preg_replace('/[\x00-\x20\x7f]+/', '', $url) ?? '');

        if (preg_match('/^([a-z][a-z0-9+.\-]*):/', $normalized, $match)) {
            $allowed = array_map(
                fn (mixed $scheme): string => strtolower(trim((string) $scheme)),
                (array) (function_exists('config') ? config('filament-shadcn.allowed_url_schemes', static::ALLOWED) : static::ALLOWED),
            );

            return in_array($match[1], $allowed, true) ? $url : null;
        }

        // Tanpa skema: hanya bentuk relatif yang jelas. Karakter ':' sebelum '/', '?', '#' berarti skema tersamar.
        if (preg_match('/^[^\/?#]*:/', $normalized)) {
            return null;
        }

        return $url;
    }
}
