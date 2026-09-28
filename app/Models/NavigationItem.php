<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavigationItem extends Model
{
    protected $fillable = ['label', 'url', 'route_name', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean'];

    public function destination(): string
    {
        return $this->route_name && \Route::has($this->route_name)
            ? route($this->route_name)
            : ($this->url ?: '#');
    }
}
