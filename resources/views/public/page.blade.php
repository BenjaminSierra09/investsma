<x-layouts.public :title="($page->meta_title ?? $page->title).' | investsma'">
    <section class="section-wrap max-w-4xl pb-16 pt-10 lg:pt-14">
        <div class="section-label">Página</div>
        <h1 class="mt-4 text-4xl font-semibold tracking-tight text-zinc-950">{{ $page->title }}</h1>
        @if ($page->meta_description)
            <p class="mt-4 max-w-2xl text-sm leading-relaxed text-zinc-600">{{ $page->meta_description }}</p>
        @endif

        <article class="surface-panel mt-8 space-y-4 px-6 py-8 text-base leading-relaxed text-zinc-700">
            {!! $html !!}
        </article>
    </section>
</x-layouts.public>
