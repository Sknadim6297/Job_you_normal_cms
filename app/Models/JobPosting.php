<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class JobPosting extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;

    protected $fillable = [
        'job_category_id', 'title', 'slug', 'qualification', 'image_url',
        'excerpt', 'content', 'author', 'published_at', 'is_published',
        'is_featured', 'sort_order',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class, 'job_category_id');
    }

    protected static function booted(): void
    {
        static::creating(function (JobPosting $job): void {
            $job->slug ??= Str::slug($job->title);
        });
    }
}
