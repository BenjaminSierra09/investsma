<?php

namespace App\Support;

use Illuminate\Support\Collection;

class StaticPageRegistry
{
    public static function all(): Collection
    {
        return collect(config('site.static_pages', []));
    }

    public static function find(?string $key): ?array
    {
        if (! $key) {
            return null;
        }

        return static::all()->firstWhere('key', $key);
    }

    public static function urlForKey(?string $key): ?string
    {
        $page = static::find($key);

        return $page ? url($page['url']) : null;
    }

    public static function viewForKey(?string $key): ?string
    {
        $page = static::find($key);

        return $page['view'] ?? null;
    }

    public static function routeForKey(?string $key): ?string
    {
        $page = static::find($key);

        return $page['route'] ?? null;
    }

    public static function options(): array
    {
        return static::all()
            ->map(fn (array $page) => [
                'key' => $page['key'],
                'label' => $page['title'],
                'route' => $page['route'] ?? null,
            ])
            ->values()
            ->all();
    }
}
