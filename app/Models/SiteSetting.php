<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function values(): array
    {
        if (! Schema::hasTable((new static)->getTable())) {
            return [];
        }

        return static::query()->pluck('value', 'key')->all();
    }
}
