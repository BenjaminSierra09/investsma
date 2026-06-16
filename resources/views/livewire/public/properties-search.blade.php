@php
    use Illuminate\Support\Str;

    $selectedNeighborhoods = is_array($neighborhood)
        ? array_filter($neighborhood)
        : (filled(trim((string) $neighborhood)) ? [trim((string) $neighborhood)] : []);
@endphp

<section class="section-wrap py-8 lg:py-10">
    <div class="grid gap-5 lg:grid-cols-[minmax(0,0.82fr)_minmax(0,1.18fr)] lg:items-end">
        <div data-reveal>
            <div class="eyebrow">Buscador de propiedades</div>
            <h1 class="mt-4 max-w-3xl text-4xl font-semibold tracking-tight text-zinc-950 sm:text-5xl">
                Inventario activo, filtros más ligeros.
            </h1>
            <p class="mt-4 max-w-xl text-base leading-relaxed text-zinc-600">
                Busca por zona, rango y características clave sin perder de vista los resultados.
            </p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end" data-reveal data-reveal-delay="60">
            <a
                href="{{ route('properties.map', array_filter([
                    'keywords' => $keywords,
                    'office_id' => $office_id,
                    'neighborhood' => $neighborhood,
                    'category' => $category,
                    'status' => $status,
                    'currency' => $currency,
                    'price_min' => $price_min,
                    'price_max' => $price_max,
                    'floors' => $floors,
                    'construction_meters_min' => $construction_meters_min,
                    'construction_meters_max' => $construction_meters_max,
                    'lot_meters_min' => $lot_meters_min,
                    'lot_meters_max' => $lot_meters_max,
                    'bathrooms' => $bathrooms,
                    'bedrooms' => $bedrooms,
                    'furnished' => $furnished,
                    'parking_type' => $parking_type,
                    'with_yard' => $with_yard,
                    'pool' => $pool,
                    'casita' => $casita,
                    'gated_comm' => $gated_comm,
                    'sort' => $sort,
                    'per_page' => $perPage,
                ], fn ($value) => filled($value))) }}"
                class="button-secondary"
            >
                Ver mapa
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            <button type="button" wire:click="resetFilters" class="button-ghost hidden xl:inline-flex">
                Limpiar filtros
            </button>
        </div>
    </div>

    <form wire:submit.prevent="search" class="compact-filter-panel mt-6" data-reveal data-reveal-delay="100">
        <div class="grid gap-3 xl:grid-cols-[minmax(180px,1.5fr)_minmax(160px,1.1fr)_minmax(140px,0.85fr)_minmax(120px,0.7fr)_minmax(120px,0.7fr)_auto] xl:items-end">
            <div>
                <label class="filter-label" for="search-keywords">Keywords</label>
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.85-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                    </svg>
                    <input
                        id="search-keywords"
                        wire:model.defer="keywords"
                        type="text"
                        class="filter-input pl-9"
                        placeholder="jardín, terraza, centro"
                    >
                </div>
            </div>

            <div>
                <label class="filter-label" for="search-neighborhood">Zona</label>
                <div wire:ignore>
                    <select
                        id="search-neighborhood"
                        multiple
                        data-choices
                        data-choices-remove-item-button="true"
                        data-choices-placeholder-value="Colonias"
                        data-livewire-model="neighborhood"
                        class="filter-select"
                    >
                        @foreach ($neighborhoods as $item)
                            @php
                                $value = is_array($item) ? ($item['slug'] ?? ($item['name'] ?? null)) : $item;
                                $label = is_array($item) ? ($item['name'] ?? ($item['slug'] ?? '')) : $item;
                            @endphp
                            @if ($value)
                                <option value="{{ $value }}" @selected(in_array($value, $selectedNeighborhoods, true))>{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="filter-label" for="search-category">Tipo</label>
                <div wire:ignore>
                    <select
                        id="search-category"
                        data-choices
                        data-choices-placeholder-value="Tipo"
                        data-livewire-model="category"
                        class="filter-select"
                    >
                        <option value="">Todas</option>
                        <option value="Residential" @selected($category === 'Residential')>Residencial</option>
                        <option value="Land and Lots" @selected($category === 'Land and Lots')>Terrenos</option>
                        <option value="Commercial" @selected($category === 'Commercial')>Comercial</option>
                        <option value="Pre Sales" @selected($category === 'Pre Sales')>Preventa</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="filter-label" for="search-price-min">Mínimo</label>
                <input id="search-price-min" wire:model.defer="price_min" type="number" min="0" class="filter-input" placeholder="100000">
            </div>

            <div>
                <label class="filter-label" for="search-price-max">Máximo</label>
                <input id="search-price-max" wire:model.defer="price_max" type="number" min="0" class="filter-input" placeholder="500000">
            </div>

            <div class="grid grid-cols-2 gap-2 xl:flex xl:flex-col xl:justify-end">
                <button type="submit" class="button-primary h-11 w-full px-5 data-loading:pointer-events-none data-loading:opacity-90">
                    <span class="in-data-loading:hidden">Buscar</span>
                    <span class="hidden in-data-loading:inline">Buscando</span>
                </button>
                <button type="button" wire:click="resetFilters" class="button-secondary h-11 w-full px-4 xl:hidden">
                    Limpiar
                </button>
            </div>
        </div>

        <details class="advanced-filter-drawer mt-4">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 text-sm font-semibold text-zinc-900">
                <span>Refinar búsqueda</span>
                <span class="text-xs font-semibold text-amber-700">Más filtros</span>
            </summary>

            <div class="mt-4 grid gap-3 md:grid-cols-3 xl:grid-cols-6">
                <div>
                    <label class="filter-label" for="search-status">Estatus</label>
                    <div wire:ignore>
                        <select
                            id="search-status"
                            data-choices
                            data-choices-placeholder-value="Estatus"
                            data-livewire-model="status"
                            class="filter-select"
                        >
                            <option value="">Todos</option>
                            <option value="For Sale" @selected($status === 'For Sale')>En venta</option>
                            <option value="Price Reduction" @selected($status === 'Price Reduction')>Baja de precio</option>
                            <option value="For Rent" @selected($status === 'For Rent')>En renta</option>
                            <option value="Contract Pending" @selected($status === 'Contract Pending')>Contrato pendiente</option>
                            <option value="Under Contract" @selected($status === 'Under Contract')>Bajo contrato</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="filter-label" for="search-bedrooms">Recámaras</label>
                    <input id="search-bedrooms" wire:model.defer="bedrooms" type="number" min="0" class="filter-input" placeholder="2">
                </div>

                <div>
                    <label class="filter-label" for="search-bathrooms">Baños</label>
                    <input id="search-bathrooms" wire:model.defer="bathrooms" type="number" min="0" class="filter-input" placeholder="2">
                </div>

                <div>
                    <label class="filter-label" for="search-currency">Moneda</label>
                    <div wire:ignore>
                        <select
                            id="search-currency"
                            data-choices
                            data-choices-placeholder-value="Moneda"
                            data-livewire-model="currency"
                            class="filter-select"
                        >
                            <option value="">Cualquiera</option>
                            <option value="USD" @selected($currency === 'USD')>USD</option>
                            <option value="MXN" @selected($currency === 'MXN')>MXN</option>
                            <option value="CAD" @selected($currency === 'CAD')>CAD</option>
                            <option value="EUR" @selected($currency === 'EUR')>EUR</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="filter-label" for="search-per-page">Por página</label>
                    <select id="search-per-page" wire:model.defer="perPage" class="filter-input">
                        @foreach ([12, 24, 36] as $count)
                            <option value="{{ $count }}">{{ $count }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="filter-label" for="search-sort">Orden</label>
                    <select id="search-sort" wire:model.defer="sort" class="filter-input">
                        <option value="mls_desc">Más recientes MLS</option>
                        <option value="price_desc">Mayor precio</option>
                        <option value="price_asc">Menor precio</option>
                    </select>
                </div>

                <div>
                    <label class="filter-label" for="search-construction-min">Construcción mín.</label>
                    <input id="search-construction-min" wire:model.defer="construction_meters_min" type="number" min="0" class="filter-input">
                </div>

                <div>
                    <label class="filter-label" for="search-construction-max">Construcción máx.</label>
                    <input id="search-construction-max" wire:model.defer="construction_meters_max" type="number" min="0" class="filter-input">
                </div>

                <div>
                    <label class="filter-label" for="search-lot-min">Terreno mín.</label>
                    <input id="search-lot-min" wire:model.defer="lot_meters_min" type="number" min="0" class="filter-input">
                </div>

                <div>
                    <label class="filter-label" for="search-lot-max">Terreno máx.</label>
                    <input id="search-lot-max" wire:model.defer="lot_meters_max" type="number" min="0" class="filter-input">
                </div>

                <div>
                    <label class="filter-label" for="search-pool">Alberca</label>
                    <div wire:ignore>
                        <select
                            id="search-pool"
                            data-choices
                            data-choices-placeholder-value="Indistinto"
                            data-livewire-model="pool"
                            class="filter-select"
                        >
                            <option value="">Indistinto</option>
                            <option value="Yes" @selected($pool === 'Yes')>Sí</option>
                            <option value="No" @selected($pool === 'No')>No</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="filter-label" for="search-casita">Casita</label>
                    <div wire:ignore>
                        <select
                            id="search-casita"
                            data-choices
                            data-choices-placeholder-value="Indistinto"
                            data-livewire-model="casita"
                            class="filter-select"
                        >
                            <option value="">Indistinto</option>
                            <option value="Yes" @selected($casita === 'Yes')>Sí</option>
                            <option value="No" @selected($casita === 'No')>No</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="filter-label" for="search-floors">Pisos</label>
                    <input id="search-floors" wire:model.defer="floors" type="number" min="0" class="filter-input" placeholder="1">
                </div>
            </div>
        </details>

        <div class="mt-4 flex flex-col gap-3 border-t border-zinc-200/70 pt-4 text-sm text-zinc-500 sm:flex-row sm:items-center sm:justify-between">
            <p>Usa keywords para estilo o intención, y filtros para cerrar el rango real de búsqueda.</p>
            <div class="flex gap-3">
                <p>{{ $perPage }} resultados por página</p>
                <button type="button" wire:click="resetFilters" class="hidden font-semibold text-amber-700 transition hover:text-amber-800 xl:inline">
                    Limpiar filtros
                </button>
            </div>
        </div>
    </form>

    <div class="mt-10 space-y-6" wire:loading.class="opacity-80">
        @if ($errorMessage)
            <div class="rounded-[20px] border border-amber-200 bg-amber-50/90 px-4 py-3 text-sm text-amber-800 shadow-sm">
                {{ $errorMessage }}
            </div>
        @endif

        @php
            $items = $results['data'] ?? $results;
            $meta = $results['meta'] ?? (isset($results['current_page']) ? [
                'current_page' => $results['current_page'] ?? null,
                'last_page' => $results['last_page'] ?? null,
                'per_page' => $results['per_page'] ?? null,
                'total' => $results['total'] ?? null,
                'from' => $results['from'] ?? null,
                'to' => $results['to'] ?? null,
            ] : null);
            $totalResults = is_countable($items) ? count($items) : 0;
        @endphp

        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between" data-reveal>
            <div>
                <p class="section-label">Resultados</p>
                <h2 class="mt-4 text-3xl font-semibold tracking-tight text-zinc-950">
                    {{ $totalResults > 0 ? 'Propiedades que vale la pena revisar con más detalle.' : 'Ajusta los filtros para abrir mejores opciones.' }}
                </h2>
            </div>

            @if ($meta)
                <div class="meta-pill">
                    Página {{ $meta['current_page'] ?? $page }} de {{ $meta['last_page'] ?? ($meta['total_pages'] ?? '1') }}
                </div>
            @endif
        </div>

        @if (! empty($items))
            <div class="grid gap-6 md:grid-cols-2">
                @foreach ($items as $property)
                    @php
                        $image = $property['featured_image']
                            ?? ($property['photos'][0] ?? null)
                            ?? 'https://picsum.photos/seed/'.urlencode($property['name'] ?? 'investsma-result').'/1200/900';
                        $detailUrl = route('properties.show', [
                            'mlsId' => $property['mls_id'] ?? $property['id'] ?? null,
                            'slug' => Str::slug($property['name'] ?? 'propiedad'),
                        ]);
                    @endphp

                    <article class="property-card group" data-reveal data-reveal-delay="{{ ($loop->index % 2) * 60 }}">
                        <a href="{{ $detailUrl }}" class="block focus:outline-none" aria-label="Ver detalles de {{ $property['name'] ?? 'la propiedad' }}">
                            <div class="property-media aspect-[4/3] bg-zinc-100">
                                <img src="{{ $image }}" alt="{{ $property['name'] ?? 'Propiedad' }}" class="h-full w-full object-cover transition-transform duration-700 [transition-timing-function:var(--ease-out-strong)] group-hover:scale-[1.04]" loading="lazy">
                                @if (! empty($property['status']))
                                    <span class="absolute left-4 top-4 rounded-full bg-white/92 px-3 py-1 text-[11px] font-semibold text-amber-700 shadow-sm">
                                        {{ $property['status'] }}
                                    </span>
                                @endif
                            </div>
                        </a>

                        <div class="space-y-4 p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <a href="{{ $detailUrl }}" class="text-xl font-semibold text-zinc-950 transition-colors hover:text-amber-700 group-hover:text-amber-700">
                                        {{ $property['name'] ?? 'Propiedad sin título' }}
                                    </a>
                                    <p class="mt-2 text-sm text-zinc-600">
                                        {{ $property['neighborhood'] ?? 'Sin colonia' }},
                                        {{ $property['city'] ?? 'San Miguel de Allende' }}
                                    </p>
                                </div>
                                @if (! empty($property['price']))
                                    <div class="rounded-full bg-amber-50 px-3 py-2 text-right text-sm font-semibold text-amber-700">
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
                                @if (! empty($property['lot_meters']))
                                    <span class="meta-pill">{{ $property['lot_meters'] }} m2 terreno</span>
                                @endif
                            </div>

                            @php
                                $rawDescription = $property['description_short_es'] ?? $property['description_short_en'] ?? null;
                                $cleanDescription = $rawDescription ? strip_tags($rawDescription, '<br><br/>') : null;
                            @endphp

                            @if (! empty($cleanDescription))
                                <p class="line-clamp-3 text-sm leading-relaxed text-zinc-600">{!! $cleanDescription !!}</p>
                            @endif

                            <div class="flex items-center justify-between border-t border-zinc-100 pt-3">
                                <p class="text-sm text-zinc-500">MLS activo</p>
                                <a href="{{ $detailUrl }}" class="button-ghost">
                                    Ver detalles
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="surface-panel px-6 py-12 text-center text-sm text-zinc-600">
                No encontramos propiedades con los filtros actuales.
            </div>
        @endif

        @if ($meta)
            <div class="surface-panel px-4 py-4 text-sm text-zinc-700">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div class="flex flex-col gap-1 md:flex-row md:items-center md:gap-3">
                        <span>Página {{ $meta['current_page'] ?? $page }} de {{ $meta['last_page'] ?? ($meta['total_pages'] ?? '1') }}</span>
                        @if (! empty($meta['from']) && ! empty($meta['to']) && ! empty($meta['total']))
                            <span class="text-xs text-zinc-500">Mostrando {{ $meta['from'] }} a {{ $meta['to'] }} de {{ $meta['total'] }}</span>
                        @endif
                    </div>

                    <div class="flex gap-2">
                        <button
                            type="button"
                            wire:click="prevPage"
                            class="button-secondary rounded-full px-4 py-2 disabled:opacity-50"
                            @if(isset($meta['current_page']) && $meta['current_page'] <= 1) disabled @endif
                        >
                            Anterior
                        </button>
                        <button
                            type="button"
                            wire:click="nextPage"
                            class="button-secondary rounded-full px-4 py-2 disabled:opacity-50"
                            @if(isset($meta['last_page']) && isset($meta['current_page']) && $meta['current_page'] >= $meta['last_page']) disabled @endif
                        >
                            Siguiente
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
