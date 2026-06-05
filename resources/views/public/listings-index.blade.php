@php use Illuminate\Support\Str; @endphp

<x-layouts.public title="Listados | investsma">
    @php
        $heading = match ($listingType ?? null) {
            'sale' => 'Propiedades en venta seleccionadas por investsma',
            'rent' => 'Propiedades en renta seleccionadas por investsma',
            default => 'Listados exclusivos del equipo investsma',
        };

        $description = match ($listingType ?? null) {
            'sale' => 'Casas, terrenos y oportunidades que nuestro equipo publica con seguimiento directo.',
            'rent' => 'Inventario de renta con contacto inmediato y página propia para revisar cada opción con calma.',
            default => 'Explora el inventario publicado directamente por nuestro equipo, con galería, descripción y contacto claro en cada propiedad.',
        };
    @endphp

    <section class="section-wrap pb-10 pt-10 lg:pt-14">
        <div class="grid gap-8 lg:grid-cols-[minmax(0,0.78fr)_minmax(0,1.22fr)] lg:items-end">
            <div data-reveal>
                <div class="section-label">Listados</div>
                <h1 class="section-title text-4xl sm:text-5xl">{{ $heading }}</h1>
                <p class="section-copy max-w-xl">{{ $description }}</p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end" data-reveal data-reveal-delay="70">
                <a href="{{ route('contact') }}" class="button-primary">Agenda una visita</a>
                <a href="{{ route('properties.index') }}" class="button-secondary">Ver MLS completo</a>
            </div>
        </div>

        <div class="mt-8 flex flex-wrap gap-3" data-reveal data-reveal-delay="110">
            <a href="{{ route('listings.index') }}" class="meta-pill {{ empty($listingType) ? '!border-amber-200 !bg-amber-50 !text-amber-700' : '' }}">Todos</a>
            <a href="{{ route('listings.sales') }}" class="meta-pill {{ ($listingType ?? null) === 'sale' ? '!border-amber-200 !bg-amber-50 !text-amber-700' : '' }}">Venta</a>
            <a href="{{ route('listings.rentals') }}" class="meta-pill {{ ($listingType ?? null) === 'rent' ? '!border-amber-200 !bg-amber-50 !text-amber-700' : '' }}">Renta</a>
        </div>
    </section>

    <section class="section-wrap pb-16">
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($listings as $listing)
                <article class="property-card group" data-reveal data-reveal-delay="{{ ($loop->index % 3) * 60 }}">
                    <a href="{{ route('listings.show', $listing) }}" class="block aspect-[4/3] overflow-hidden bg-zinc-100">
                        @if ($listing->primaryImage())
                            <div class="property-media h-full">
                                <img
                                    src="{{ $listing->primaryImage() }}"
                                    alt="{{ $listing->title }}"
                                    class="h-full w-full object-cover transition duration-700 group-hover:scale-[1.04]"
                                    loading="lazy"
                                >
                            </div>
                        @else
                            <div class="flex h-full items-center justify-center text-zinc-400">Sin imagen</div>
                        @endif
                    </a>

                    <div class="space-y-4 px-5 py-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="text-xl font-semibold">
                                    <a href="{{ route('listings.show', $listing) }}" class="text-zinc-950 transition hover:text-amber-700 group-hover:text-amber-700">
                                        {{ $listing->title }}
                                    </a>
                                </h2>
                                @if ($listing->location)
                                    <p class="mt-2 text-sm text-zinc-600">{{ $listing->location }}</p>
                                @endif
                            </div>

                            <div class="flex flex-wrap justify-end gap-2">
                                <span class="meta-pill">{{ $listing->listingTypeLabel() }}</span>
                                @if ($listing->featured)
                                    <span class="meta-pill !border-amber-200 !bg-amber-50 !text-amber-700">Destacado</span>
                                @endif
                            </div>
                        </div>

                        @if ($listing->price)
                            <div class="text-lg font-semibold text-amber-700">{{ $listing->currency }} ${{ number_format((float) $listing->price, 0) }}</div>
                        @endif

                        <div class="flex flex-wrap gap-2">
                            @if ($listing->bedrooms)
                                <span class="meta-pill">{{ $listing->bedrooms }} recámaras</span>
                            @endif
                            @if ($listing->bathrooms)
                                <span class="meta-pill">{{ $listing->bathrooms }} baños</span>
                            @endif
                            @if ($listing->construction_m2)
                                <span class="meta-pill">{{ $listing->construction_m2 }} m2 const.</span>
                            @endif
                            @if ($listing->lot_m2)
                                <span class="meta-pill">{{ $listing->lot_m2 }} m2 terreno</span>
                            @endif
                        </div>

                        <p class="text-sm leading-relaxed text-zinc-600">
                            {{ Str::limit($listing->summary ?: strip_tags($listing->description ?? ''), 140) }}
                        </p>

                        <a href="{{ route('listings.show', $listing) }}" class="button-ghost px-0">
                            Ver propiedad
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </article>
            @empty
                <div class="surface-panel px-6 py-12 text-center text-zinc-600 md:col-span-2 xl:col-span-3">
                    Aún no hay listados publicados.
                </div>
            @endforelse
        </div>
    </section>
</x-layouts.public>
