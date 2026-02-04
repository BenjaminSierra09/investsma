<?php

namespace App\Support;

use Illuminate\Support\Str;

class EditorJsRenderer
{
    public static function render(?array $content): string
    {
        $blocks = static::normalizeBlocks($content);

        return collect($blocks)
            ->map(fn ($block) => static::renderBlock($block))
            ->implode("\n");
    }

    protected static function normalizeBlocks(mixed $content): array
    {
        if (is_string($content)) {
            $decoded = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded['blocks'] ?? ($decoded ?? []);
            }
        }

        if (is_array($content)) {
            return $content['blocks'] ?? $content;
        }

        return [];
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
            'table' => static::renderTable($block['data'] ?? []),
            'raw' => static::renderRaw($block['data'] ?? []),
            'youtubeEmbed' => static::renderYoutube($block['data'] ?? []),
            'warning' => static::renderWarning($block['data'] ?? []),
            'delimiter' => static::renderDelimiter(),
            'columns' => static::renderColumns($block['data'] ?? []),
            default => '',
        };
    }

    protected static function renderHeader(array $data): string
    {
        $level = (int) ($data['level'] ?? 2);
        $text = static::cleanText($data['text'] ?? '');

        $alignClass = static::alignmentClass($data);

        return sprintf('<h%d class="mb-3 font-semibold text-zinc-800 dark:text-zinc-50 %s">%s</h%d>', $level, $alignClass, $text, $level);
    }

    protected static function alignmentClass(array $data): string
    {
        $alignment = $data['alignment']
            ?? ($data['tunes']['alignmentTune']['alignment'] ?? null)
            ?? 'start';

        return match ($alignment) {
            'center' => 'text-center',
            'right', 'end' => 'text-right',
            default => 'text-left',
        };
    }

    protected static function renderParagraph(array $data): string
    {
        $text = static::cleanText($data['text'] ?? '');
        $alignClass = static::alignmentClass($data);

        return sprintf('<p class="mb-4 text-zinc-700 leading-relaxed dark:text-zinc-200 %s">%s</p>', $alignClass, $text);
    }

    protected static function renderList(array $data): string
    {
        $style = $data['style'] ?? 'unordered';
        if ($style === 'checklist') {
            return static::renderChecklist($data);
        }

        $tag = $style === 'ordered' ? 'ol' : 'ul';
        $listClass = $style === 'ordered' ? 'list-decimal' : 'list-disc';
        $items = collect($data['items'] ?? [])->map(function ($item) {
            $content = static::extractText($item);

            return sprintf('<li class="mb-2">%s</li>', static::cleanText($content));
        })->implode('');

        return sprintf('<%1$s class="mb-4 ms-5 %3$s text-zinc-700 dark:text-zinc-200">%2$s</%1$s>', $tag, $items, $listClass);
    }

    protected static function renderChecklist(array $data): string
    {
        $items = collect($data['items'] ?? [])->map(function ($item) {
            $content = static::cleanText(static::extractText($item));
            $checked = (bool) ($item['meta']['checked'] ?? false);
            $stateClasses = $checked ? 'bg-emerald-500 border-emerald-500' : 'bg-white border-zinc-300';

            return sprintf(
                '<li class="flex items-start gap-3 rounded-lg border border-zinc-200 bg-white px-3 py-2 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">'
                .'<span class="mt-1 inline-flex h-4 w-4 shrink-0 items-center justify-center rounded %s"></span>'
                .'<span class="text-zinc-700 dark:text-zinc-200">%s</span>'
                .'</li>',
                $stateClasses,
                $content,
            );
        })->implode('');

        return sprintf('<ul class="mb-4 space-y-2">%s</ul>', $items);
    }

    protected static function renderQuote(array $data): string
    {
        $text = static::cleanText($data['text'] ?? '');
        $caption = static::cleanText($data['caption'] ?? '');
        $alignClass = static::alignmentClass($data);

        return sprintf(
            '<blockquote class="mb-4 border-l-4 border-amber-500 bg-amber-50/60 px-4 py-3 text-zinc-800 %s"><p class="mb-2">%s</p><span class="text-sm text-zinc-500">%s</span></blockquote>',
            $alignClass,
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
        $link = $data['link'] ?? null;

        $imgTag = sprintf(
            '<img src="%s" alt="%s" class="h-auto w-full object-cover" loading="lazy">',
            e($url),
            $caption,
        );

        if ($link) {
            $imgTag = sprintf('<a href="%s" target="_blank" rel="noopener">%s</a>', e($link), $imgTag);
        }

        return sprintf(
            '<figure class="mb-6 overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm">%s%s</figure>',
            $imgTag,
            $caption !== '' ? sprintf('<figcaption class="px-4 py-3 text-sm text-zinc-600">%s</figcaption>', $caption) : ''
        );
    }

    protected static function renderTable(array $data): string
    {
        $rows = $data['content'] ?? [];
        if (! is_array($rows) || count($rows) === 0) {
            return '';
        }

        $withHeadings = (bool) ($data['withHeadings'] ?? false);
        $head = '';
        $bodyRows = $rows;

        if ($withHeadings && count($rows) > 0) {
            $headings = array_shift($bodyRows);
            $headCells = collect($headings)->map(fn ($cell) => sprintf('<th class="px-3 py-2 text-left text-sm font-semibold text-zinc-700 dark:text-zinc-200">%s</th>', e($cell)))->implode('');
            $head = sprintf('<thead class="bg-zinc-50 dark:bg-zinc-800"><tr>%s</tr></thead>', $headCells);
        }

        $body = collect($bodyRows)->map(function ($row) {
            $cells = collect($row)->map(fn ($cell) => sprintf('<td class="px-3 py-2 text-sm text-zinc-700 dark:text-zinc-200">%s</td>', e($cell)))->implode('');

            return sprintf('<tr class="odd:bg-white even:bg-zinc-50 dark:odd:bg-zinc-900 dark:even:bg-zinc-800">%s</tr>', $cells);
        })->implode('');

        return sprintf(
            '<div class="mb-6 overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">'
            .'<div class="overflow-x-auto">'
            .'<table class="w-full min-w-[320px] border-collapse text-left">%s<tbody>%s</tbody></table>'
            .'</div>'
            .'</div>',
            $head,
            $body,
        );
    }

    protected static function renderRaw(array $data): string
    {
        $html = $data['html'] ?? '';
        if ($html === '') {
            return '';
        }

        // Allow common embed tags like iframe while stripping unknown tags.
        $allowed = '<iframe><div><span><p><br><b><strong><i><em><u><a>';
        $clean = strip_tags($html, $allowed);

        return sprintf('<div class="mb-6 overflow-hidden rounded-xl border border-zinc-200 bg-white p-3 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">%s</div>', $clean);
    }

    protected static function renderYoutube(array $data): string
    {
        $url = $data['url'] ?? $data['source'] ?? null;
        if (! $url) {
            return '';
        }

        $embedUrl = static::youtubeEmbedUrl((string) $url);

        return sprintf(
            '<div class="mb-6 overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">'
            .'<div class="aspect-video bg-black/5"><iframe class="h-full w-full" src="%s" allowfullscreen loading="lazy"></iframe></div>'
            .'</div>',
            e($embedUrl),
        );
    }

    protected static function renderWarning(array $data): string
    {
        $title = static::cleanText($data['title'] ?? '');
        $message = static::cleanText($data['message'] ?? '');

        return sprintf(
            '<div class="mb-4 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-amber-900 shadow-sm dark:border-amber-400/60 dark:bg-amber-500/10">'
            .'<p class="font-semibold">%s</p>'
            .'<p class="text-sm">%s</p>'
            .'</div>',
            $title,
            $message,
        );
    }

    protected static function renderDelimiter(): string
    {
        return '<div class="my-6 flex justify-center"><span class="h-1 w-16 rounded-full bg-zinc-200 dark:bg-zinc-700"></span></div>';
    }

    protected static function renderColumns(array $data): string
    {
        $cols = $data['cols'] ?? [];
        if (! is_array($cols) || count($cols) === 0) {
            return '';
        }

        $colHtml = collect($cols)->map(function ($col) {
            $blocks = $col['blocks'] ?? [];
            $content = collect($blocks)->map(fn ($block) => static::renderBlock($block))->implode('');

            return sprintf('<div class="flex-1 space-y-3">%s</div>', $content);
        })->implode('<div class="w-4"></div>');

        return sprintf('<div class="mb-6 flex flex-col gap-4 md:flex-row">%s</div>', $colHtml);
    }

    protected static function youtubeEmbedUrl(string $url): string
    {
        if (str_contains($url, 'youtube.com/watch')) {
            parse_str(parse_url($url, PHP_URL_QUERY) ?? '', $query);
            $videoId = $query['v'] ?? null;
            if ($videoId) {
                return sprintf('https://www.youtube.com/embed/%s', $videoId);
            }
        }

        if (str_contains($url, 'youtu.be/')) {
            $id = trim(parse_url($url, PHP_URL_PATH) ?? '', '/');
            if ($id !== '') {
                return sprintf('https://www.youtube.com/embed/%s', $id);
            }
        }

        return $url;
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
