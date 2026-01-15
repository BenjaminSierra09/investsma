<?php

namespace App\Models;

use App\Support\StaticPageRegistry;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu',
        'label',
        'type',
        'url',
        'static_key',
        'page_id',
        'parent_id',
        'order',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order');
    }

    public function scopeForMenu($query, string $menu = 'main')
    {
        return $query->where('menu', $menu)->orderBy('order');
    }

    public function resolvedUrl(): ?string
    {
        return match ($this->type) {
            'page' => $this->page && $this->page->status === 'published'
                ? $this->page->url()
                : null,
            'static' => StaticPageRegistry::urlForKey($this->static_key),
            default => $this->url,
        };
    }

    public static function tree(string $menu = 'main'): Collection
    {
        $items = self::forMenu($menu)
            ->with(['page', 'children.page'])
            ->get();

        $grouped = $items->groupBy('parent_id');

        $build = function ($parentId) use (&$build, $grouped) {
            return ($grouped[$parentId] ?? collect())->map(function (MenuItem $item) use (&$build) {
                $item->setRelation('children', $build($item->id));

                return $item;
            });
        };

        return new Collection($build(null)->all());
    }
}
