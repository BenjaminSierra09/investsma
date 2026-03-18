@php use Illuminate\Support\Str; @endphp

<x-layouts.public title="{{ __('Listados | investsma') }}">
    @php
        $heading = match ($listingType ?? null) {
            'sale' => 'Propiedades en venta',
            'rent' => 'Propiedades en renta',
            default => 'Propiedades exclusivas de investsma',
        };

        $description = match ($listingType ?? null) {
            'sale' => 'Explora casas, terrenos y oportunidades de inversión disponibles para compra directa.',
            'rent' => 'Descubre propiedades disponibles para renta con atención directa de nuestro equipo.',
            default => 'Explora propiedades publicadas directamente por nuestro equipo, con su propia página, galería y contacto inmediato.',
        };
    @endphp

    <section class="mx-auto max-w-6xl px-6 pb-16 pt-16 lg:pt-20">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">Listados</p>
                <h1 class="mt-3 text-4xl font-semibold text-zinc-900">{{ $heading }}</h1>
                <p class="mt-3 text-base leading-relaxed text-zinc-700">{{ $description }}</p>
            </div>
            <a href="{{ route('contact') }}" class="inline-flex items-center justify-center rounded-full bg-amber-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-amber-200 transition hover:-translate-y-0.5">Publicar o pedir más opciones</a>
        </div>

        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('listings.index') }}" class="rounded-full px-4 py-2 text-sm font-semibold {{ empty($listingType) ? 'bg-zinc-900 text-white' : 'bg-white text-zinc-700 ring-1 ring-amber-100' }}">Todos</a>
            <a href="{{ route('listings.sales') }}" class="rounded-full px-4 py-2 text-sm font-semibold {{ ($listingType ?? null) === 'sale' ? 'bg-zinc-900 text-white' : 'bg-white text-zinc-700 ring-1 ring-amber-100' }}">Venta</a>
            <a href="{{ route('listings.rentals') }}" class="rounded-full px-4 py-2 text-sm font-semibold {{ ($listingType ?? null) === 'rent' ? 'bg-zinc-900 text-white' : 'bg-white text-zinc-700 ring-1 ring-amber-100' }}">Renta</a>
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($listings as $listing)
                <article class="overflow-hidden rounded-[28px] border border-amber-100/70 bg-white/90 shadow-sm ring-1 ring-white/60">
                    <a href="{{ route('listings.show', $listing) }}" class="block aspect-[4/3] overflow-hidden bg-zinc-100">
                        @if ($listing->primaryImage())
                            <img
                                src="{{ $listing->primaryImage() }}"
                                alt="{{ $listing->title }}"
                                class="h-full w-full object-cover transition duration-700 hover:scale-105"
                                loading="lazy"
                            >
                        @else
                            <div class="flex h-full items-center justify-center text-zinc-400">Sin imagen</div>
                        @endif
                    </a>
                    <div class="space-y-4 px-5 py-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="text-xl font-semibold text-zinc-900">{{ $listing->title }}</h2>
                                @if ($listing->location)
                                    <p class="mt-1 text-sm text-zinc-600">{{ $listing->location }}</p>
                                @endif
                            </div>
                            <div class="flex flex-wrap justify-end gap-2">
                                <span class="rounded-full bg-zinc-900 px-3 py-1 text-[11px] font-semibold text-white">{{ $listing->listingTypeLabel() }}</span>
                                @if ($listing->featured)
                                    <span class="rounded-full bg-amber-50 px-3 py-1 text-[11px] font-semibold text-amber-700">Destacado</span>
                                @endif
                            </div>
                        </div>

                        @if ($listing->price)
                            <div class="text-lg font-semibold text-amber-700">{{ $listing->currency }} ${{ number_format((float) $listing->price, 0) }}</div>
                        @endif

                        <div class="flex flex-wrap gap-2 text-xs text-zinc-700">
                            @if ($listing->bedrooms)
                                <span class="rounded-full bg-amber-50 px-2 py-1">{{ $listing->bedrooms }} rec</span>
                            @endif
                            @if ($listing->bathrooms)
                                <span class="rounded-full bg-amber-50 px-2 py-1">{{ $listing->bathrooms }} baños</span>
                            @endif
                            @if ($listing->construction_m2)
                                <span class="rounded-full bg-amber-50 px-2 py-1">{{ $listing->construction_m2 }} m2 const.</span>
                            @endif
                            @if ($listing->lot_m2)
                                <span class="rounded-full bg-amber-50 px-2 py-1">{{ $listing->lot_m2 }} m2 terreno</span>
                            @endif
                        </div>

                        <p class="text-sm leading-relaxed text-zinc-600">{{ Str::limit($listing->summary ?: strip_tags($listing->description ?? ''), 140) }}</p>

                        <a href="{{ route('listings.show', $listing) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-amber-700 hover:text-amber-800">
                            Ver propiedad
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </article>
            @empty
                <div class="rounded-[28px] border border-dashed border-amber-200 bg-white/70 px-6 py-12 text-center text-zinc-600 md:col-span-2 xl:col-span-3">
                    Aún no hay listados publicados.
                </div>
            @endforelse
        </div>
    </section>
</x-layouts.public>
