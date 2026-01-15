<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'type',
        'status',
        'content',
        'static_view',
        'meta_title',
        'meta_description',
        'published_at',
    ];

    protected $casts = [
        'content' => 'array',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Page $page) {
            if (empty($page->slug) && ! empty($page->title)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function isStatic(): bool
    {
        return $this->type === 'static' && filled($this->static_view);
    }

    public function url(): string
    {
        if ($this->isStatic()) {
            return url($this->slug === '/' ? '/' : sprintf('/%s', ltrim($this->slug, '/')));
        }

        return route('page.show', $this);
    }
}
