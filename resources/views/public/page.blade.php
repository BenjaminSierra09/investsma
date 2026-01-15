<x-layouts.public :title="($page->meta_title ?? $page->title).' | investsma'">
    <section class="mx-auto max-w-4xl px-6 pt-16 lg:pt-20 pb-16">
        <h1 class="mt-3 text-4xl font-semibold text-zinc-900">{{ $page->title }}</h1>
        @if ($page->meta_description)
            <p class="mt-2 text-sm text-zinc-600 leading-relaxed">{{ $page->meta_description }}</p>
        @endif

        <article class="mt-8 space-y-4 text-base leading-relaxed text-zinc-700">
            {!! $html !!}
        </article>
    </section>
</x-layouts.public>
