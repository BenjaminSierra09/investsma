<?php

namespace App\Support;

use Illuminate\Support\Str;

class EditorJsRenderer
{
    public static function render(?array $content): string
    {
        $blocks = $content['blocks'] ?? $content ?? [];

        return collect($blocks)
            ->map(fn ($block) => static::renderBlock($block))
            ->implode("\n");
    }

    protected static function renderBlock(array $block): string
    {
        return match ($block['type'] ?? null) {
            'header' => static::renderHeader($block['data'] ?? []),
            'paragraph' => static::renderParagraph($block['data'] ?? []),
            'list' => static::renderList($block['data'] ?? []),
            'quote' => static::renderQuote($block['data'] ?? []),
            'embed' => static::renderEmbed($block['data'] ?? []),
            'image' => static::renderImage($block['data'] ?? []),
            default => '',
        };
    }

    protected static function renderHeader(array $data): string
    {
        $level = (int) ($data['level'] ?? 2);
        $text = static::cleanText($data['text'] ?? '');

        return sprintf('<h%d class="mb-3 font-semibold text-zinc-800 dark:text-zinc-50">%s</h%d>', $level, $text, $level);
    }

    protected static function renderParagraph(array $data): string
    {
        $text = static::cleanText($data['text'] ?? '');

        return sprintf('<p class="mb-4 text-zinc-700 leading-relaxed dark:text-zinc-200">%s</p>', $text);
    }

    protected static function renderList(array $data): string
    {
        $style = $data['style'] ?? 'unordered';
        $tag = $style === 'ordered' ? 'ol' : 'ul';
        $listClass = $style === 'ordered' ? 'list-decimal' : 'list-disc';
        $items = collect($data['items'] ?? [])->map(function ($item) {
            $content = static::extractText($item);

            return sprintf('<li class="mb-2">%s</li>', static::cleanText($content));
        })->implode('');

        return sprintf('<%1$s class="mb-4 ms-5 %3$s text-zinc-700 dark:text-zinc-200">%2$s</%1$s>', $tag, $items, $listClass);
    }

    protected static function renderQuote(array $data): string
    {
        $text = static::cleanText($data['text'] ?? '');
        $caption = static::cleanText($data['caption'] ?? '');

        return sprintf(
            '<blockquote class="mb-4 border-l-4 border-amber-500 bg-amber-50/60 px-4 py-3 text-zinc-800"><p class="mb-2">%s</p><span class="text-sm text-zinc-500">%s</span></blockquote>',
            $text,
            $caption,
        );
    }

    protected static function renderEmbed(array $data): string
    {
        $src = $data['embed'] ?? $data['source'] ?? null;
        if (empty($src)) {
            return '';
        }

        $caption = static::cleanText($data['caption'] ?? '');

        return sprintf(
            '<div class="mb-6 overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm"><div class="aspect-video bg-black/5"><iframe class="h-full w-full" src="%s" allowfullscreen loading="lazy"></iframe></div>%s</div>',
            e($src),
            $caption !== '' ? sprintf('<p class="px-4 py-3 text-sm text-zinc-600">%s</p>', $caption) : ''
        );
    }

    protected static function renderImage(array $data): string
    {
        $url = $data['file']['url'] ?? null;
        if (empty($url)) {
            return '';
        }

        $caption = static::cleanText($data['caption'] ?? '');

        return sprintf(
            '<figure class="mb-6 overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm"><img src="%s" alt="%s" class="h-auto w-full object-cover" loading="lazy">%s</figure>',
            e($url),
            $caption,
            $caption !== '' ? sprintf('<figcaption class="px-4 py-3 text-sm text-zinc-600">%s</figcaption>', $caption) : ''
        );
    }

    protected static function cleanText(mixed $text): string
    {
        if (! is_string($text)) {
            $text = static::extractText($text);
        }

        $allowed = '<a><b><i><strong><em><u><br>';

        return Str::of(strip_tags($text, $allowed))->trim()->toString();
    }

    protected static function extractText(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_array($value)) {
            if (array_key_exists('text', $value)) {
                return (string) $value['text'];
            }

            if (array_key_exists('content', $value)) {
                return (string) $value['content'];
            }

            if (array_is_list($value)) {
                return collect($value)->map(fn ($v) => static::extractText($v))->implode(' ');
            }

            return collect($value)->map(fn ($v) => static::extractText($v))->implode(' ');
        }

        return '';
    }
}
