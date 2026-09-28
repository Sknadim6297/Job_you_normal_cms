<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageResolver
{
    public static function resolve(?string $value, ?string $fallback = null): string
    {
        $value = static::normalizeValue($value);

        if ($value === null || $value === '') {
            return static::fallbackUrl($fallback);
        }

        if (static::isExternalUrl($value)) {
            return $value;
        }

        $path = ltrim($value, '/');

        if ($path !== '' && Str::startsWith($path, 'storage/')) {
            return asset($path);
        }

        if ($path !== '' && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        if ($path !== '' && file_exists(public_path($path))) {
            return asset($path);
        }

        return static::fallbackUrl($fallback);
    }

    public static function isExternalUrl(?string $value): bool
    {
        if ($value === null || trim($value) === '') {
            return false;
        }

        return filter_var(trim($value), FILTER_VALIDATE_URL) !== false
            && in_array(parse_url(trim($value), PHP_URL_SCHEME), ['http', 'https'], true);
    }

    public static function normalizeValue(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }

    protected static function fallbackUrl(?string $fallback): string
    {
        if (empty($fallback)) {
            return asset('assets/img/placeholder-job.svg');
        }

        return static::resolve($fallback, asset('assets/img/placeholder-job.svg'));
    }
}
