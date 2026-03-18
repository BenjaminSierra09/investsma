<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Listing extends Model
{
    /** @use HasFactory<\Database\Factories\ListingFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'status',
        'featured',
        'currency',
        'price',
        'bedrooms',
        'bathrooms',
        'construction_m2',
        'lot_m2',
        'location',
        'cover_image',
        'gallery',
        'summary',
        'description',
        'contact_email',
        'contact_phone',
        'contact_whatsapp',
        'meta_title',
        'meta_description',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
            'gallery' => 'array',
            'published_at' => 'datetime',
            'price' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Listing $listing): void {
            if (blank($listing->slug) && filled($listing->title)) {
                $listing->slug = Str::slug($listing->title);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function primaryImage(): ?string
    {
        return $this->cover_image ?: collect($this->gallery)->first();
    }
}
