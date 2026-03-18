<x-layouts.public title="Mapa de propiedades | investsma">
    @vite(['resources/js/properties-map.js'])

    <section class="py-10">
        <div class="mx-auto flex max-w-6xl flex-col gap-6 px-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div class="space-y-2">
                    <p class="text-sm font-semibold text-amber-600">San Miguel de Allende</p>
                    <h1 class="text-3xl font-bold text-zinc-900">Mapa de propiedades MLS</h1>
                    <p class="max-w-3xl text-sm text-zinc-600">
                        Explora propiedades en el mapa, abre el pin para ver un resumen y entra al detalle completo de cada inmueble.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center rounded-full bg-white/80 px-4 py-2 text-sm font-medium text-zinc-700 shadow-sm ring-1 ring-zinc-200">
                        {{ count($properties) }} propiedades con coordenadas
                    </span>
                    <a
                        href="{{ route('properties.index', $filters) }}"
                        class="inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-white px-4 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-50"
                    >
                        Ver listado
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="overflow-hidden rounded-[2rem] border border-amber-100 bg-white/90 p-3 shadow-sm backdrop-blur">
                <div id="properties-map" class="h-[65vh] min-h-[480px] w-full rounded-[1.5rem]"></div>
            </div>

            <script>
                window.propertiesMapData = @json($properties);
            </script>

            @if (empty($properties))
                <div class="rounded-2xl border border-dashed border-zinc-200 bg-white/70 px-4 py-10 text-center text-sm text-zinc-600">
                    No encontramos propiedades con coordenadas para mostrar en el mapa con los filtros actuales.
                </div>
            @endif
        </div>
    </section>
</x-layouts.public>
