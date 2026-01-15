@php use Illuminate\Support\Str; @endphp

<section class="py-10">
        <div class="mx-auto max-w-6xl px-6">
            <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-semibold text-amber-600">San Miguel de Allende</p>
                    <h1 class="text-3xl font-bold text-zinc-900">Busca propiedades MLS</h1>
                    <p class="text-sm text-zinc-600">Filtra por colonia, precio y características. Consultamos la API de AMPI en tiempo real.</p>
                </div>
            </div>

            <form wire:submit.prevent="search" class="rounded-2xl border border-amber-100 bg-white/80 p-6 shadow-sm backdrop-blur">
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label class="text-sm font-medium text-zinc-700">Colonia</label>
                        <select wire:model.defer="neighborhood" multiple class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-400 focus:ring-amber-300">
                            @foreach ($neighborhoods as $item)
                                @php
                                    $value = is_array($item) ? ($item['slug'] ?? ($item['name'] ?? null)) : $item;
                                    $label = is_array($item) ? ($item['name'] ?? ($item['slug'] ?? '')) : $item;
                                @endphp
                                @if ($value)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endif
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-zinc-500">Selecciona una o varias colonias.</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-zinc-700">Categoría</label>
                        <input wire:model.defer="category" type="text" placeholder="Residencial, Terreno, Comercial" class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-400 focus:ring-amber-300">
                    </div>

                    <div>
                        <label class="text-sm font-medium text-zinc-700">Estatus</label>
                        <input wire:model.defer="status" type="text" placeholder="Active, Pending..." class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-400 focus:ring-amber-300">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-sm font-medium text-zinc-700">Precio mín.</label>
                            <input wire:model.defer="price_min" type="number" min="0" class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-400 focus:ring-amber-300" placeholder="100000">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-zinc-700">Precio máx.</label>
                            <input wire:model.defer="price_max" type="number" min="0" class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-400 focus:ring-amber-300" placeholder="500000">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-sm font-medium text-zinc-700">Recámaras</label>
                            <input wire:model.defer="bedrooms" type="number" min="0" class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-400 focus:ring-amber-300">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-zinc-700">Baños</label>
                            <input wire:model.defer="bathrooms" type="number" min="0" class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-400 focus:ring-amber-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-sm font-medium text-zinc-700">Construcción mín. (m²)</label>
                            <input wire:model.defer="construction_meters_min" type="number" min="0" class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-400 focus:ring-amber-300">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-zinc-700">Construcción máx. (m²)</label>
                            <input wire:model.defer="construction_meters_max" type="number" min="0" class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-400 focus:ring-amber-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-sm font-medium text-zinc-700">Terreno mín. (m²)</label>
                            <input wire:model.defer="lot_meters_min" type="number" min="0" class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-400 focus:ring-amber-300">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-zinc-700">Terreno máx. (m²)</label>
                            <input wire:model.defer="lot_meters_max" type="number" min="0" class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-400 focus:ring-amber-300">
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-zinc-700">Moneda</label>
                        <select wire:model.defer="currency" class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-400 focus:ring-amber-300">
                            <option value="">Cualquiera</option>
                            <option value="USD">USD</option>
                            <option value="MXN">MXN</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-sm font-medium text-zinc-700">Alberca</label>
                            <select wire:model.defer="pool" class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-400 focus:ring-amber-300">
                                <option value="">Indistinto</option>
                                <option value="yes">Sí</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-zinc-700">Casita</label>
                            <select wire:model.defer="casita" class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-400 focus:ring-amber-300">
                                <option value="">Indistinto</option>
                                <option value="yes">Sí</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="text-xs text-zinc-500">Se consultan {{ $perPage }} resultados por página.</div>
                    <div class="flex gap-3">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-md transition hover:-translate-y-0.5 hover:bg-amber-700">
                            Buscar
                        </button>
                        <button type="button" wire:click="resetFilters" class="inline-flex items-center gap-2 rounded-xl border border-zinc-200 px-4 py-2 text-sm font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50">
                            Limpiar cambios
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-8 space-y-4">
                @if ($errorMessage)
                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
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
                @endphp

                @if (! empty($items))
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach ($items as $property)
                            @php
                                $img = $property['featured_image']
                                    ?? ($property['photos'][0] ?? null)
                                    ?? 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1200&q=80';
                            @endphp
                            <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white/90 shadow-sm">
                                <div class="relative aspect-video bg-zinc-100">
                                    <img src="{{ $img }}" alt="{{ $property['name'] ?? 'Propiedad' }}" class="h-full w-full object-cover" loading="lazy">
                                    @if (! empty($property['status']))
                                        <span class="absolute left-3 top-3 inline-flex rounded-full bg-white/90 px-3 py-1 text-[11px] font-semibold text-amber-700 shadow">{{ $property['status'] }}</span>
                                    @endif
                                </div>

                                <div class="p-4 space-y-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <h3 class="text-lg font-semibold text-zinc-900">{{ $property['name'] ?? 'Propiedad sin título' }}</h3>
                                            <p class="text-sm text-zinc-600">{{ $property['neighborhood'] ?? 'Sin colonia' }} · {{ $property['city'] ?? 'Sin ciudad' }}</p>
                                        </div>
                                        @if (! empty($property['price']))
                                            <div class="text-right text-amber-600 font-bold">
                                                {{ $property['currency'] ?? 'USD' }} ${{ number_format($property['price'], 0) }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex flex-wrap gap-3 text-sm text-zinc-700">
                                        @if (! empty($property['bedrooms']))
                                            <span class="inline-flex items-center gap-1 rounded-full bg-zinc-100 px-3 py-1">{{ $property['bedrooms'] }} recámaras</span>
                                        @endif
                                        @if (! empty($property['bathrooms']))
                                            <span class="inline-flex items-center gap-1 rounded-full bg-zinc-100 px-3 py-1">{{ $property['bathrooms'] }} baños</span>
                                        @endif
                                        @if (! empty($property['construction_meters']))
                                            <span class="inline-flex items-center gap-1 rounded-full bg-zinc-100 px-3 py-1">{{ $property['construction_meters'] }} m² const.</span>
                                        @endif
                                        @if (! empty($property['lot_meters']))
                                            <span class="inline-flex items-center gap-1 rounded-full bg-zinc-100 px-3 py-1">{{ $property['lot_meters'] }} m² terreno</span>
                                        @endif
                                    </div>

                                    @if (! empty($property['description_short_es']) || ! empty($property['description_short_en']))
                                        <p class="text-sm text-zinc-600 line-clamp-3">{{ $property['description_short_es'] ?? $property['description_short_en'] }}</p>
                                    @endif

                                    <div class="flex justify-end">
                                        <a
                                            href="{{ route('properties.show', ['mlsId' => $property['mls_id'] ?? $property['id'] ?? null, 'slug' => Str::slug($property['name'] ?? 'propiedad')]) }}"
                                            class="inline-flex items-center gap-2 rounded-lg border border-amber-200 px-3 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-50"
                                        >
                                            Ver detalles
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-zinc-200 bg-white/70 px-4 py-10 text-center text-sm text-zinc-600">
                        No encontramos propiedades con los filtros actuales.
                    </div>
                @endif

                @if ($meta)
                    <div class="flex flex-col gap-2 rounded-xl border border-zinc-200 bg-white/80 px-4 py-3 text-sm text-zinc-700 md:flex-row md:items-center md:justify-between">
                        <div class="flex flex-col gap-1 md:flex-row md:items-center md:gap-3">
                            <span>Página {{ $meta['current_page'] ?? $page }} de {{ $meta['last_page'] ?? ($meta['total_pages'] ?? '¿?') }}</span>
                            @if (!empty($meta['from']) && !empty($meta['to']) && !empty($meta['total']))
                                <span class="text-xs text-zinc-500">Mostrando {{ $meta['from'] }}–{{ $meta['to'] }} de {{ $meta['total'] }}</span>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <button type="button" wire:click="prevPage" class="rounded-lg border border-zinc-200 px-3 py-1 disabled:opacity-50" @if(isset($meta['current_page']) && $meta['current_page'] <= 1) disabled @endif>Anterior</button>
                            <button type="button" wire:click="nextPage" class="rounded-lg border border-zinc-200 px-3 py-1 disabled:opacity-50" @if(isset($meta['last_page']) && isset($meta['current_page']) && $meta['current_page'] >= $meta['last_page']) disabled @endif>Siguiente</button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
