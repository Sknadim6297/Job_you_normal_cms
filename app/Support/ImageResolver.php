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

        $path = static::storagePath($value);

        if ($path !== null && Storage::disk('public')->exists($path)) {
            return asset('storage/'.$path);
        }

        if (static::isExternalUrl($value)) {
            return $value;
        }

        $path = ltrim($value, '/');

        if ($path !== '' && file_exists(public_path($path))) {
            return asset($path);
        }

        return static::fallbackUrl($fallback);
    }

    protected static function storagePath(string $value): ?string
    {
        $path = $value;

        if (static::isExternalUrl($value)) {
            $urlHost = parse_url($value, PHP_URL_HOST);
            $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
            $requestHost = request()->getHost();

            if (! str_starts_with((string) parse_url($value, PHP_URL_PATH), '/storage/')) {
                return null;
            }

            if (! in_array($urlHost, array_filter([$appHost, $requestHost]), true)) {
                return null;
            }

            $path = (string) parse_url($value, PHP_URL_PATH);
        }

        $path = ltrim($path, '/');

        if (Str::startsWith($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        return $path !== '' ? $path : null;
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
