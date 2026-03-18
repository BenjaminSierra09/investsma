<?php

namespace App\Support;

use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SeoData
{
    public static function apply(
        string $title,
        ?string $description = null,
        ?string $canonical = null,
        array $keywords = [],
        ?string $image = null,
        string $type = 'website',
        string $schemaType = 'WebPage',
        string $robots = 'index,follow'
    ): void {
        $siteName = static::siteName();
        $canonicalUrl = $canonical ?? url()->current();
        $normalizedDescription = static::normalizeDescription($description);
        $imageUrl = static::normalizeImageUrl($image);

        SEOMeta::setTitle($title, false);
        SEOMeta::setCanonical($canonicalUrl);
        SEOMeta::addMeta('robots', $robots);

        if (filled($normalizedDescription)) {
            SEOMeta::setDescription($normalizedDescription);
        }

        $normalizedKeywords = static::normalizeKeywords($keywords);

        if ($normalizedKeywords !== []) {
            SEOMeta::addKeyword($normalizedKeywords);
        }

        OpenGraph::setTitle($title);
        OpenGraph::setUrl($canonicalUrl);
        OpenGraph::setSiteName($siteName);
        OpenGraph::setType($type);
        OpenGraph::addProperty('locale', str_replace('_', '-', app()->getLocale()));

        TwitterCard::setType($imageUrl ? 'summary_large_image' : 'summary');
        TwitterCard::setTitle($title);
        TwitterCard::setUrl($canonicalUrl);

        JsonLd::setType($schemaType);
        JsonLd::setTitle($title);
        JsonLd::setUrl($canonicalUrl);

        if (filled($normalizedDescription)) {
            OpenGraph::setDescription($normalizedDescription);
            TwitterCard::setDescription($normalizedDescription);
            JsonLd::setDescription($normalizedDescription);
        }

        if (filled($imageUrl)) {
            OpenGraph::addImage($imageUrl, [
                'alt' => $title,
            ]);
            TwitterCard::setImage($imageUrl);
            JsonLd::addImage($imageUrl);
        }
    }

    public static function applyIfMissing(
        string $title,
        ?string $description = null,
        ?string $canonical = null,
        array $keywords = [],
        ?string $image = null,
        string $type = 'website',
        string $schemaType = 'WebPage',
        string $robots = 'index,follow'
    ): void {
        if (filled(SEOMeta::getTitle())) {
            return;
        }

        static::apply(
            title: $title,
            description: $description,
            canonical: $canonical,
            keywords: $keywords,
            image: $image,
            type: $type,
            schemaType: $schemaType,
            robots: $robots,
        );
    }

    private static function normalizeDescription(?string $description): ?string
    {
        if (blank($description)) {
            return null;
        }

        return Str::limit(trim(strip_tags($description)), 160, '...');
    }

    private static function normalizeImageUrl(?string $image): ?string
    {
        if (blank($image)) {
            return null;
        }

        if (Str::startsWith($image, ['http://', 'https://'])) {
            return $image;
        }

        return asset(ltrim($image, '/'));
    }

    private static function normalizeKeywords(array $keywords): array
    {
        return Collection::make($keywords)
            ->filter(fn (mixed $keyword): bool => filled($keyword))
            ->map(fn (mixed $keyword): string => trim((string) $keyword))
            ->unique()
            ->values()
            ->all();
    }

    private static function siteName(): string
    {
        $appName = (string) config('app.name', 'investsma');

        return $appName !== 'Laravel' ? $appName : 'investsma';
    }
}
