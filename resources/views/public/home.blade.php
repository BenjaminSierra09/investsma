@php
    use App\Models\Listing;
    use Illuminate\Support\Str;

    $items = $properties['data'] ?? $properties ?? [];
    $featuredListing = ($featuredListing ?? null) instanceof Listing ? $featuredListing : null;
    $fallback = [
        [
            'title' => 'Casa con patio en Guadiana',
            'price' => 'USD 895,000',
            'image' => 'https://picsum.photos/seed/investsma-guadiana/1200/900',
            'neighborhood' => 'Guadiana',
            'city' => 'San Miguel de Allende',
        ],
        [
            'title' => 'Terreno en Atotonilco',
            'price' => 'USD 215,000',
            'image' => 'https://picsum.photos/seed/investsma-atotonilco/1200/900',
            'neighborhood' => 'Atotonilco',
            'city' => 'San Miguel de Allende',
        ],
        [
            'title' => 'Loft en el centro',
            'price' => 'USD 420,000',
            'image' => 'https://picsum.photos/seed/investsma-centro/1200/900',
            'neighborhood' => 'Centro',
            'city' => 'San Miguel de Allende',
        ],
    ];

    if ($featuredListing) {
        $heroImage = $featuredListing->primaryImage() ?? $fallback[0]['image'];
        $heroTitle = $featuredListing->title;
        $heroLocation = $featuredListing->location ?? 'San Miguel de Allende';
        $heroPrice = $featuredListing->price
            ? sprintf('%s $%s', $featuredListing->currency, number_format((float) $featuredListing->price, 0))
            : $featuredListing->listingTypeLabel();
        $heroUrl = route('listings.show', $featuredListing);
    } else {
        $heroProperty = $items[0] ?? $fallback[0];
        $heroImage = $heroProperty['featured_image'] ?? $heroProperty['image'] ?? $fallback[0]['image'];
        $heroTitle = $heroProperty['name'] ?? $heroProperty['title'] ?? $fallback[0]['title'];
        $heroLocation = collect([
            $heroProperty['neighborhood'] ?? $fallback[0]['neighborhood'],
            $heroProperty['city'] ?? $fallback[0]['city'],
        ])->filter()->implode(', ');
        $heroPrice = ! empty($heroProperty['price'])
            ? sprintf('%s $%s', $heroProperty['currency'] ?? 'USD', number_format((float) $heroProperty['price'], 0))
            : ($heroProperty['price'] ?? $fallback[0]['price']);
        $heroUrl = ! empty($heroProperty['mls_id'] ?? $heroProperty['id'] ?? null)
            ? route('properties.show', [
                'mlsId' => $heroProperty['mls_id'] ?? $heroProperty['id'],
                'slug' => Str::slug($heroTitle),
            ])
            : null;
    }
@endphp

<x-layouts.public title="Bienes raíces en San Miguel de Allende | investsma">
    <section class="section-wrap pb-10 pt-8 lg:pt-12">
        <div class="grid gap-8 lg:grid-cols-[minmax(0,0.92fr)_minmax(0,1.08fr)] lg:items-center">
            <div class="max-w-2xl" data-reveal>
                <div class="eyebrow">Bienes raíces en San Miguel</div>
                <h1 class="mt-4 text-4xl font-semibold leading-[1.04] tracking-tight text-zinc-950 sm:text-5xl lg:text-6xl">
                    Encuentra propiedad con mejor criterio local.
                </h1>
                <p class="mt-5 max-w-xl text-lg leading-relaxed text-zinc-600">
                    Filtra inventario activo y compara casas, terrenos e inversión con contexto de zona.
                </p>
                <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('properties.index') }}" class="button-primary">Explorar propiedades</a>
                    <a href="{{ route('contact') }}" class="button-secondary">Agenda una visita</a>
                </div>
            </div>

            <div class="relative" data-reveal data-reveal-delay="70">
                <div class="home-hero-media" data-spotlight>
                    @if ($heroUrl)
                        <a href="{{ $heroUrl }}" class="group block h-full focus:outline-none focus-visible:ring-4 focus-visible:ring-amber-300" aria-label="Ver detalles de {{ $heroTitle }}">
                    @endif
                        <img
                            src="{{ $heroImage }}"
                            alt="{{ $heroTitle }}"
                            class="h-full w-full object-cover transition duration-700 group-hover:scale-[1.03]"
                            loading="eager"
                        >
                        <div class="absolute inset-x-4 bottom-4 rounded-[22px] border border-white/20 bg-zinc-950/74 p-4 text-white backdrop-blur-xl sm:inset-x-5 sm:bottom-5">
                            <p class="text-sm font-medium text-white/72">Selección destacada</p>
                            <div class="mt-2 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                                <div>
                                    <h2 class="line-clamp-2 text-xl font-semibold leading-tight transition group-hover:text-amber-100">
                                        {{ $heroTitle }}
                                    </h2>
                                    <p class="mt-1 text-sm text-white/70">{{ $heroLocation }}</p>
                                </div>
                                <div class="whitespace-nowrap rounded-full bg-white/12 px-3 py-1.5 text-sm font-semibold text-amber-100">
                                    {{ $heroPrice }}
                                </div>
                            </div>
                        </div>
                    @if ($heroUrl)
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-7 home-filter-bar" data-reveal data-reveal-delay="110">
            <form action="{{ route('properties.index') }}" method="GET" class="grid gap-3 lg:grid-cols-[minmax(180px,1.4fr)_minmax(150px,1fr)_minmax(140px,0.85fr)_minmax(130px,0.75fr)_auto] lg:items-end">
                <div>
                    <label class="filter-label" for="home-keywords">Keywords</label>
                    <input
                        id="home-keywords"
                        type="text"
                        name="keywords"
                        class="filter-input"
                        placeholder="jardín, terraza, centro"
                    >
                </div>

                <div>
                    <label class="filter-label" for="home-neighborhood">Zona</label>
                    <select id="home-neighborhood" name="neighborhood" data-choices data-choices-placeholder-value="Todas las zonas" class="filter-select">
                        <option value="">Todas</option>
                        @foreach ($neighborhoods as $neighborhood)
                            <option value="{{ $neighborhood }}">{{ $neighborhood }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="filter-label" for="home-category">Tipo</label>
                    <select id="home-category" name="category" data-choices data-choices-placeholder-value="Tipo" class="filter-select">
                        <option value="">Todos</option>
                        <option value="Residential">Residencial</option>
                        <option value="Land and Lots">Terrenos</option>
                        <option value="Commercial">Comercial</option>
                        <option value="Pre Sales">Preventa</option>
                    </select>
                </div>

                <div>
                    <label class="filter-label" for="home-price-max">Precio máximo</label>
                    <input id="home-price-max" type="number" min="0" step="1000" name="price_max" class="filter-input" placeholder="500000">
                </div>

                <button type="submit" class="button-primary h-11 px-5">Buscar</button>
            </form>

            <div class="mt-4 flex flex-wrap gap-2 text-sm">
                <a href="{{ route('properties.index', ['category' => 'Residential']) }}" class="quick-filter-chip">Casas</a>
                <a href="{{ route('properties.index', ['category' => 'Land and Lots']) }}" class="quick-filter-chip">Terrenos</a>
                <a href="{{ route('properties.index', ['status' => 'Price Reduction']) }}" class="quick-filter-chip">Baja de precio</a>
            </div>
        </div>
    </section>

    <section class="section-wrap relative z-0 py-8">
        <div class="grid gap-6 lg:grid-cols-[minmax(0,0.7fr)_minmax(0,1.3fr)]">
            <div data-reveal>
                <h2 class="text-3xl font-semibold tracking-tight text-zinc-950">
                    Nuestro trabajo no es abrir puertas. Es ayudarte a filtrar mejor.
                </h2>
                <p class="mt-4 max-w-md text-sm leading-relaxed text-zinc-600">
                    Cada oportunidad pasa por una lectura de ubicación, encaje de uso y fricción operativa antes de recomendar una visita.
                </p>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div class="feature-tile" data-reveal data-reveal-delay="40">
                    <p class="text-lg font-semibold text-zinc-950">Primero entendemos tu objetivo</p>
                    <p class="mt-3 text-sm leading-relaxed text-zinc-600">
                        Vivienda patrimonial, renta o plusvalía exigen filtros distintos desde el primer día.
                    </p>
                </div>
                <div class="feature-tile" data-reveal data-reveal-delay="80">
                    <p class="text-lg font-semibold text-zinc-950">Después reducimos el ruido</p>
                    <p class="mt-3 text-sm leading-relaxed text-zinc-600">
                        Cruzamos inventario MLS y lectura local para separar opciones atractivas de opciones sólidas.
                    </p>
                </div>
                <div class="feature-tile" data-reveal data-reveal-delay="120">
                    <p class="text-lg font-semibold text-zinc-950">Y entramos a negociación con contexto</p>
                    <p class="mt-3 text-sm leading-relaxed text-zinc-600">
                        Llegas a la visita con preguntas claras, comparables listos y menos sorpresas en el proceso.
                    </p>
                </div>
            </div>
        </div>
    </section>

    @if (! empty($items))
        <section class="section-wrap py-8">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between" data-reveal>
                <div>
                    <div class="section-label">Inventario seleccionado</div>
                    <h2 class="mt-4 text-3xl font-semibold tracking-tight text-zinc-950">
                        Propiedades para empezar a comparar con calma.
                    </h2>
                </div>
                <a href="{{ route('properties.index', ['office_id' => 32]) }}" class="button-secondary">
                    Ver todo el inventario
                </a>
            </div>

            <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($items as $property)
                    @php
                        $detailUrl = route('properties.show', [
                            'mlsId' => $property['mls_id'] ?? $property['id'] ?? null,
                            'slug' => Str::slug($property['name'] ?? 'propiedad'),
                        ]);
                        $image = $property['featured_image']
                            ?? ($property['photos'][0] ?? null)
                            ?? 'https://picsum.photos/seed/'.urlencode($property['name'] ?? 'investsma-property').'/1200/900';
                    @endphp

                    <article class="property-card group" data-reveal data-reveal-delay="{{ ($loop->index % 3) * 60 }}">
                        <a href="{{ $detailUrl }}" class="block focus:outline-none" aria-label="Ver detalles de {{ $property['name'] ?? 'la propiedad' }}">
                            <div class="property-media aspect-[4/3] bg-zinc-100">
                                <img
                                    src="{{ $image }}"
                                    alt="{{ $property['name'] ?? 'Propiedad' }}"
                                    class="h-full w-full object-cover transition-transform duration-700 [transition-timing-function:var(--ease-out-strong)] group-hover:scale-[1.04]"
                                    loading="lazy"
                                >
                                @if (! empty($property['status']))
                                    <span class="absolute left-4 top-4 rounded-full bg-white/92 px-3 py-1 text-[11px] font-semibold text-amber-700 shadow-sm">
                                        {{ $property['status'] }}
                                    </span>
                                @endif
                            </div>
                        </a>

                        <div class="space-y-4 px-5 py-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <a href="{{ $detailUrl }}" class="line-clamp-2 text-xl font-semibold text-zinc-950 transition-colors hover:text-amber-700 group-hover:text-amber-700">
                                        {{ $property['name'] ?? 'Propiedad' }}
                                    </a>
                                    <p class="mt-2 text-sm text-zinc-600">
                                        {{ $property['neighborhood'] ?? 'San Miguel de Allende' }},
                                        {{ $property['city'] ?? 'Guanajuato' }}
                                    </p>
                                </div>
                                @if (! empty($property['price']))
                                    <div class="rounded-full bg-amber-50 px-3 py-2 text-sm font-semibold whitespace-nowrap text-amber-700">
                                        {{ $property['currency'] ?? 'USD' }} ${{ number_format((float) $property['price'], 0) }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-wrap gap-2">
                                @if (! empty($property['bedrooms']))
                                    <span class="meta-pill">{{ $property['bedrooms'] }} recámaras</span>
                                @endif
                                @if (! empty($property['bathrooms']))
                                    <span class="meta-pill">{{ $property['bathrooms'] }} baños</span>
                                @endif
                                @if (! empty($property['construction_meters']))
                                    <span class="meta-pill">{{ $property['construction_meters'] }} m2 const.</span>
                                @endif
                            </div>

                            <a href="{{ $detailUrl }}" class="button-ghost px-0">
                                Ver detalles
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section class="section-wrap py-8 pb-16">
        <div class="cta-band" data-reveal>
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-center">
                <div>
                    <p class="text-sm text-white/70">Siguiente paso</p>
                    <h2 class="mt-3 max-w-2xl text-3xl font-semibold tracking-tight">
                        Si ya tienes presupuesto, zonas o un tipo de activo en mente, empezamos por ahí.
                    </h2>
                    <p class="mt-4 max-w-2xl text-sm leading-relaxed text-white/72">
                        Te ayudamos a ordenar el inventario y a decidir qué conviene visitar primero.
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row lg:flex-col">
                    <a href="{{ route('contact') }}" class="button-primary">Agenda una visita</a>
                    <a href="{{ route('about') }}" class="button-secondary">Conoce el proceso</a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
