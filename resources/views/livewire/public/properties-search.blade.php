@php
    use Illuminate\Support\Str;

    $filterClass = 'field-select';
    $inputClass = 'field-input';
    $selectedNeighborhoods = is_array($neighborhood)
        ? array_filter($neighborhood)
        : (filled(trim((string) $neighborhood)) ? [trim((string) $neighborhood)] : []);
@endphp

<section class="section-wrap py-10 lg:py-14">
    <div class="grid gap-8 lg:grid-cols-[minmax(0,0.76fr)_minmax(0,1.24fr)] lg:items-end">
        <div data-reveal>
            <div class="section-label">Buscador de propiedades</div>
            <h1 class="section-title text-4xl sm:text-5xl">
                Filtra el inventario con una vista más clara.
            </h1>
            <p class="section-copy max-w-xl">
                Busca por zona, rango, tamaño o características clave para abrir un shortlist más útil antes de visitar.
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
                    'per_page' => $perPage,
                ], fn ($value) => filled($value))) }}"
                class="button-secondary"
            >
                Ver mapa
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            <button type="button" wire:click="resetFilters" class="button-ghost">
                Limpiar filtros
            </button>
        </div>
    </div>

    <form wire:submit.prevent="search" class="mt-8 surface-panel p-5 sm:p-7" data-reveal data-reveal-delay="100" data-spotlight>
        <div class="grid gap-4 xl:grid-cols-[minmax(0,2fr)_minmax(0,1.15fr)_minmax(0,0.9fr)_minmax(0,0.9fr)_auto]">
            <div>
                <label class="field-label">Keywords</label>
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.85-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                    </svg>
                    <input
                        wire:model.defer="keywords"
                        type="text"
                        class="{{ $inputClass }} pl-12"
                        placeholder="jardín, terraza, centro, inversión, vista"
                    >
                </div>
            </div>

            <div>
                <label class="field-label">Zona o colonia</label>
                <div wire:ignore>
                    <select
                        multiple
                        data-choices
                        data-choices-remove-item-button="true"
                        data-choices-placeholder-value="Busca una o varias colonias"
                        data-livewire-model="neighborhood"
                        class="{{ $filterClass }}"
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
                <label class="field-label">Tipo</label>
                <div wire:ignore>
                    <select
                        data-choices
                        data-choices-placeholder-value="Selecciona una categoría"
                        data-livewire-model="category"
                        class="{{ $filterClass }}"
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
                <label class="field-label">Estatus</label>
                <div wire:ignore>
                    <select
                        data-choices
                        data-choices-placeholder-value="Selecciona un estatus"
                        data-livewire-model="status"
                        class="{{ $filterClass }}"
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

            <div class="flex flex-col justify-end">
                <button type="submit" class="button-primary h-[52px] w-full data-loading:pointer-events-none data-loading:opacity-90">
                    <span class="in-data-loading:hidden">Buscar</span>
                    <span class="hidden in-data-loading:inline">Buscando</span>
                </button>
            </div>
        </div>

        <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-6">
            <div>
                <label class="field-label">Precio mínimo</label>
                <input wire:model.defer="price_min" type="number" min="0" class="{{ $inputClass }}" placeholder="100000">
            </div>

            <div>
                <label class="field-label">Precio máximo</label>
                <input wire:model.defer="price_max" type="number" min="0" class="{{ $inputClass }}" placeholder="500000">
            </div>

            <div>
                <label class="field-label">Recámaras mínimas</label>
                <input wire:model.defer="bedrooms" type="number" min="0" class="{{ $inputClass }}" placeholder="2">
            </div>

            <div>
                <label class="field-label">Baños mínimos</label>
                <input wire:model.defer="bathrooms" type="number" min="0" class="{{ $inputClass }}" placeholder="2">
            </div>

            <div>
                <label class="field-label">Moneda</label>
                <div wire:ignore>
                    <select
                        data-choices
                        data-choices-placeholder-value="Selecciona una moneda"
                        data-livewire-model="currency"
                        class="{{ $filterClass }}"
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
                <label class="field-label">Resultados por página</label>
                <select wire:model.defer="perPage" class="{{ $inputClass }}">
                    @foreach ([12, 24, 36] as $count)
                        <option value="{{ $count }}">{{ $count }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <details class="mt-5 rounded-[24px] border border-zinc-200/80 bg-white/72 p-4">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 text-sm font-semibold text-zinc-900">
                <span>Más filtros</span>
                <span class="rounded-full bg-amber-50 px-3 py-1 text-[11px] uppercase tracking-[0.12em] text-amber-700">Opcional</span>
            </summary>

            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div>
                    <label class="field-label">Construcción mínima (m2)</label>
                    <input wire:model.defer="construction_meters_min" type="number" min="0" class="{{ $inputClass }}">
                </div>

                <div>
                    <label class="field-label">Construcción máxima (m2)</label>
                    <input wire:model.defer="construction_meters_max" type="number" min="0" class="{{ $inputClass }}">
                </div>

                <div>
                    <label class="field-label">Terreno mínimo (m2)</label>
                    <input wire:model.defer="lot_meters_min" type="number" min="0" class="{{ $inputClass }}">
                </div>

                <div>
                    <label class="field-label">Terreno máximo (m2)</label>
                    <input wire:model.defer="lot_meters_max" type="number" min="0" class="{{ $inputClass }}">
                </div>

                <div>
                    <label class="field-label">Alberca</label>
                    <div wire:ignore>
                        <select
                            data-choices
                            data-choices-placeholder-value="Indistinto"
                            data-livewire-model="pool"
                            class="{{ $filterClass }}"
                        >
                            <option value="">Indistinto</option>
                            <option value="Yes" @selected($pool === 'Yes')>Sí</option>
                            <option value="No" @selected($pool === 'No')>No</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="field-label">Casita</label>
                    <div wire:ignore>
                        <select
                            data-choices
                            data-choices-placeholder-value="Indistinto"
                            data-livewire-model="casita"
                            class="{{ $filterClass }}"
                        >
                            <option value="">Indistinto</option>
                            <option value="Yes" @selected($casita === 'Yes')>Sí</option>
                            <option value="No" @selected($casita === 'No')>No</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="field-label">Pisos mínimos</label>
                    <input wire:model.defer="floors" type="number" min="0" class="{{ $inputClass }}" placeholder="1">
                </div>
            </div>
        </details>

        <div class="mt-5 flex flex-col gap-3 border-t border-zinc-200/70 pt-5 text-sm text-zinc-500 sm:flex-row sm:items-center sm:justify-between">
            <p>Usa keywords para estilo o intención, y filtros para cerrar el rango real de búsqueda.</p>
            <p>{{ $perPage }} resultados por página</p>
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
