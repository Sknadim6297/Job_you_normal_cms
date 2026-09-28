<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class JobCategory extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean'];

    public function jobs(): HasMany
    {
        return $this->hasMany(JobPosting::class);
    }

    protected static function booted(): void
    {
        static::creating(function (JobCategory $category): void {
            $category->slug ??= Str::slug($category->name);
        });
    }
}
