<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavigationItem extends Model
{
    protected $fillable = ['label', 'url', 'route_name', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean'];

    public function destination(): string
    {
        $namedRoute = $this->route_name
            ? \Route::getRoutes()->getByName($this->route_name)
            : null;

        if ($namedRoute && ! str_contains($namedRoute->uri, '{')) {
            return route($this->route_name);
        }

        $url = trim((string) $this->url);
        if ($url === '' || preg_match('/[\x00-\x1F\\\\]/', $url)) {
            return '#';
        }

        $parsedUrl = parse_url($url);
        if ($parsedUrl === false) {
            return '#';
        }

        $scheme = $parsedUrl['scheme'] ?? null;
        if ($scheme !== null) {
            return in_array(strtolower($scheme), ['http', 'https'], true)
                && filter_var($url, FILTER_VALIDATE_URL) !== false
                ? $url
                : '#';
        }

        return str_starts_with($url, '//') ? '#' : $url;
    }
}
