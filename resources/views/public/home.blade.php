@php
    use Illuminate\Support\Str;

    $items = $properties['data'] ?? $properties ?? [];
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
@endphp

<x-layouts.public title="Bienes raíces en San Miguel de Allende | investsma">
    <section class="section-wrap pb-12 pt-10 lg:pt-14">
        <div class="grid gap-10 lg:grid-cols-[minmax(0,1.05fr)_minmax(0,0.95fr)] lg:items-center">
            <div class="max-w-2xl" data-reveal>
                <div class="eyebrow">Bienes raíces con lectura local</div>
                <h1 class="section-title text-5xl leading-[1.02] sm:text-6xl">
                    Propiedades en San Miguel de Allende con criterio patrimonial.
                </h1>
                <p class="section-copy max-w-xl text-lg">
                    Seleccionamos casas, terrenos y oportunidades con mejor contexto legal, urbano y comercial para que compres con más claridad.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('properties.index') }}" class="button-primary">Explorar propiedades</a>
                    <a href="{{ route('contact') }}" class="button-secondary">Agenda una visita</a>
                </div>
            </div>

            <div class="relative lg:pl-8" data-reveal data-reveal-delay="70">
                <div class="surface-panel p-3" data-spotlight>
                    <div class="relative overflow-hidden rounded-[24px]">
                        <div class="property-media aspect-[4/5] bg-zinc-200">
                            <img src="{{ $heroImage }}" alt="{{ $heroTitle }}" class="h-full w-full object-cover" loading="eager">
                        </div>

                        <div class="absolute inset-x-0 bottom-0 p-5">
                            <div class="rounded-[22px] border border-white/15 bg-zinc-950/70 p-5 text-white backdrop-blur-xl">
                                <p class="text-sm text-white/70">Selección destacada</p>
                                <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <h2 class="text-2xl font-semibold leading-tight">{{ $heroTitle }}</h2>
                                        <p class="mt-2 text-sm text-white/72">{{ $heroLocation }}</p>
                                    </div>
                                    <div class="rounded-full bg-white/10 px-3 py-2 text-sm font-semibold text-amber-200">
                                        {{ $heroPrice }}
                                    </div>
                                </div>
                                <div class="mt-5 grid gap-3 sm:grid-cols-3">
                                    <div class="rounded-[18px] bg-white/8 px-4 py-4">
                                        <p class="text-xs uppercase tracking-[0.12em] text-white/55">Filtro</p>
                                        <p class="mt-2 text-sm font-medium text-white">Ubicación y liquidez</p>
                                    </div>
                                    <div class="rounded-[18px] bg-white/8 px-4 py-4">
                                        <p class="text-xs uppercase tracking-[0.12em] text-white/55">Lectura</p>
                                        <p class="mt-2 text-sm font-medium text-white">Comparables y riesgos</p>
                                    </div>
                                    <div class="rounded-[18px] bg-white/8 px-4 py-4">
                                        <p class="text-xs uppercase tracking-[0.12em] text-white/55">Acompañamiento</p>
                                        <p class="mt-2 text-sm font-medium text-white">Visita y cierre</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-wrap pb-8">
        <div class="grid gap-4 lg:grid-cols-3">
            <div class="metric-card" data-reveal>
                <p class="text-lg font-semibold text-zinc-950">Compra con un shortlist más claro</p>
                <p class="mt-3 text-sm leading-relaxed text-zinc-600">
                    Partimos del inventario real y lo reducimos a opciones que sí vale la pena visitar.
                </p>
            </div>
            <div class="metric-card" data-reveal data-reveal-delay="50">
                <p class="text-lg font-semibold text-zinc-950">Contexto local antes de negociar</p>
                <p class="mt-3 text-sm leading-relaxed text-zinc-600">
                    Revisamos zona, comparables, permisos y fricción operativa antes de dar un siguiente paso.
                </p>
            </div>
            <div class="metric-card" data-reveal data-reveal-delay="100">
                <p class="text-lg font-semibold text-zinc-950">Un solo equipo del tour al cierre</p>
                <p class="mt-3 text-sm leading-relaxed text-zinc-600">
                    Coordinamos visitas, seguimiento comercial y el proceso documental sin perder ritmo.
                </p>
            </div>
        </div>
    </section>

    <section class="section-wrap relative z-20 py-8">
        <div class="surface-panel z-20 overflow-visible p-6 sm:p-8" data-reveal data-spotlight>
            <div class="grid gap-8 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)] lg:items-end">
                <div>
                    <div class="section-label">Búsqueda rápida</div>
                    <h2 class="mt-4 text-3xl font-semibold tracking-tight text-zinc-950">
                        Empieza por zona, rango y tipo de propiedad.
                    </h2>
                    <p class="mt-4 max-w-md text-sm leading-relaxed text-zinc-600">
                        Si ya tienes una idea del ticket o del barrio, este filtro te deja entrar directo al inventario activo.
                    </p>
                </div>

                <form action="{{ route('properties.index') }}" method="GET" class="grid gap-4">
                    <div class="grid gap-4 xl:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)]">
                        <div>
                            <label class="field-label">Keywords</label>
                            <input
                                type="text"
                                name="keywords"
                                class="field-input"
                                placeholder="jardín, terraza, centro, inversión, vista"
                            >
                        </div>

                        <div>
                            <label class="field-label">Zona o colonia</label>
                            <select name="neighborhood" data-choices data-choices-placeholder-value="Selecciona una colonia" class="field-select">
                                <option value="">Todas</option>
                                @foreach ($neighborhoods as $neighborhood)
                                    <option value="{{ $neighborhood }}">{{ $neighborhood }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-[minmax(0,1fr)_minmax(0,0.8fr)_minmax(0,0.8fr)_auto]">
                        <div>
                            <label class="field-label">Tipo</label>
                            <select name="category" data-choices data-choices-placeholder-value="Selecciona una categoría" class="field-select">
                                <option value="">Todos</option>
                                <option value="Residential">Residencial</option>
                                <option value="Land and Lots">Terrenos</option>
                                <option value="Commercial">Comercial</option>
                                <option value="Pre Sales">Preventa</option>
                            </select>
                        </div>

                        <div>
                            <label class="field-label">Precio mínimo</label>
                            <input type="number" min="0" step="1000" name="price_min" class="field-input" placeholder="100000">
                        </div>

                        <div>
                            <label class="field-label">Precio máximo</label>
                            <input type="number" min="0" step="1000" name="price_max" class="field-input" placeholder="500000">
                        </div>

                        <div class="flex flex-col justify-end">
                            <button type="submit" class="button-primary h-[52px] w-full">Ver propiedades</button>
                        </div>
                    </div>
                </form>
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
