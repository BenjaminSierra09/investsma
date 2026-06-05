<x-layouts.public :title="($listing->meta_title ?: $listing->title).' | investsma'">
    @vite(['resources/js/listing-detail.js'])

    <section class="mx-auto max-w-6xl px-6 pb-16 pt-12 lg:pt-16">
        <div class="grid gap-10 lg:grid-cols-[1.4fr_0.8fr]">
            <div class="space-y-6">
                <div>
                    <a href="{{ route('listings.index') }}" class="text-sm font-semibold text-amber-700 hover:text-amber-800">← Volver a listados</a>
                    <div class="mt-4 flex flex-wrap items-start justify-between gap-4">
                        <div class="max-w-2xl">
                            <div class="mb-3 inline-flex rounded-full bg-zinc-900 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-white">{{ $listing->listingTypeLabel() }}</div>
                            <h1 class="text-4xl font-semibold text-zinc-900">{{ $listing->title }}</h1>
                            @if ($listing->location)
                                <p class="mt-2 text-base text-zinc-600">{{ $listing->location }}</p>
                            @endif
                        </div>
                        @if ($listing->price)
                            <div class="rounded-2xl bg-white/90 px-5 py-4 text-right shadow-sm ring-1 ring-amber-100">
                                <div class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500">Precio</div>
                                <div class="mt-1 text-2xl font-semibold text-amber-700">{{ $listing->currency }} ${{ number_format((float) $listing->price, 0) }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="space-y-4" data-listing-gallery>
                    <div class="relative overflow-hidden rounded-[30px] bg-zinc-950">
                        @php
                            $initialImage = $gallery->first();
                        @endphp
                        @if ($initialImage)
                            <img
                                src="{{ $initialImage }}"
                                alt="{{ $listing->title }}"
                                class="aspect-[16/10] w-full object-cover"
                                data-gallery-main
                            >
                            @if ($gallery->count() > 1)
                                <button type="button" class="absolute left-4 top-1/2 -translate-y-1/2 rounded-full bg-white/90 p-3 text-zinc-900 shadow" data-gallery-prev aria-label="Anterior">
                                    ‹
                                </button>
                                <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 rounded-full bg-white/90 p-3 text-zinc-900 shadow" data-gallery-next aria-label="Siguiente">
                                    ›
                                </button>
                            @endif
                        @else
                            <div class="flex aspect-[16/10] items-center justify-center text-zinc-400">Sin imágenes</div>
                        @endif
                    </div>

                    @if ($gallery->isNotEmpty())
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-5">
                            @foreach ($gallery as $image)
                                <button
                                    type="button"
                                    class="overflow-hidden rounded-2xl border border-transparent transition data-[active=true]:border-amber-400"
                                    data-gallery-thumb
                                    data-gallery-src="{{ $image }}"
                                    data-active="{{ $loop->first ? 'true' : 'false' }}"
                                >
                                    <img src="{{ $image }}" alt="{{ $listing->title }} foto {{ $loop->iteration }}" class="aspect-[4/3] w-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    @if ($listing->bedrooms)
                        <div class="rounded-2xl bg-white/85 px-4 py-4 shadow-sm ring-1 ring-amber-100">
                            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500">Recámaras</div>
                            <div class="mt-1 text-xl font-semibold text-zinc-900">{{ $listing->bedrooms }}</div>
                        </div>
                    @endif
                    @if ($listing->bathrooms)
                        <div class="rounded-2xl bg-white/85 px-4 py-4 shadow-sm ring-1 ring-amber-100">
                            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500">Baños</div>
                            <div class="mt-1 text-xl font-semibold text-zinc-900">{{ $listing->bathrooms }}</div>
                        </div>
                    @endif
                    @if ($listing->construction_m2)
                        <div class="rounded-2xl bg-white/85 px-4 py-4 shadow-sm ring-1 ring-amber-100">
                            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500">Construcción</div>
                            <div class="mt-1 text-xl font-semibold text-zinc-900">{{ $listing->construction_m2 }} m2</div>
                        </div>
                    @endif
                    @if ($listing->lot_m2)
                        <div class="rounded-2xl bg-white/85 px-4 py-4 shadow-sm ring-1 ring-amber-100">
                            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500">Terreno</div>
                            <div class="mt-1 text-xl font-semibold text-zinc-900">{{ $listing->lot_m2 }} m2</div>
                        </div>
                    @endif
                </div>

                @if ($listing->summary)
                    <div class="rounded-[28px] border border-amber-100/70 bg-white/80 p-6 shadow-sm">
                        <p class="text-lg leading-relaxed text-zinc-700">{{ $listing->summary }}</p>
                    </div>
                @endif

                <div class="rounded-[28px] border border-amber-100/70 bg-white/85 p-6 shadow-sm">
                    <h2 class="text-2xl font-semibold text-zinc-900">Descripción</h2>
                    <div class="prose mt-4 max-w-none text-zinc-700">
                        {!! nl2br(e($listing->description ?: 'Próximamente agregaremos más información de esta propiedad.')) !!}
                    </div>
                </div>
            </div>

            <aside class="lg:sticky lg:top-28 lg:self-start">
                @if ($listing->agent)
                    <div class="mb-6 rounded-[30px] border border-amber-100/80 bg-white/95 p-7 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-600">Asesor asignado</p>

                        <div class="mt-4 flex items-center gap-4">
                            @if ($listing->agent->photo_url)
                                <img src="{{ $listing->agent->photo_url }}" alt="{{ $listing->agent->name }}" class="h-20 w-20 rounded-full object-cover ring-2 ring-amber-100">
                            @else
                                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-amber-100 text-xl font-semibold text-amber-700">
                                    {{ str($listing->agent->name)->trim()->explode(' ')->filter()->take(2)->map(fn ($part) => str($part)->substr(0, 1))->join('') }}
                                </div>
                            @endif

                            <div>
                                <h2 class="text-2xl font-semibold text-zinc-900">{{ $listing->agent->name }}</h2>
                                @if ($listing->agent->title)
                                    <p class="mt-1 text-sm font-medium text-amber-700">{{ $listing->agent->title }}</p>
                                @endif
                            </div>
                        </div>

                        @if ($listing->agent->bio)
                            <p class="mt-4 text-sm leading-relaxed text-zinc-600">{{ $listing->agent->bio }}</p>
                        @endif

                        <div class="mt-4 space-y-2 text-sm text-zinc-600">
                            @if ($listing->agent->phone)
                                <div>Tel: <a href="tel:{{ preg_replace('/[^0-9+]/', '', $listing->agent->phone) }}" class="font-medium text-amber-700">{{ $listing->agent->phone }}</a></div>
                            @endif
                            @if ($listing->agent->email)
                                <div>Email: <a href="mailto:{{ $listing->agent->email }}" class="font-medium text-amber-700">{{ $listing->agent->email }}</a></div>
                            @endif
                            @if ($listing->agent->whatsapp)
                                <div>WhatsApp: <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $listing->agent->whatsapp) }}" class="font-medium text-amber-700" target="_blank" rel="noreferrer">Enviar mensaje</a></div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="rounded-[30px] bg-zinc-900 p-7 text-white shadow-xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-500">Contacto</p>
                    <h2 class="mt-3 text-2xl font-semibold">Solicita más información</h2>
                    <p class="mt-2 text-sm leading-relaxed text-zinc-300">Escríbenos y te compartimos precio, disponibilidad, visita o documentación adicional.</p>

                    @if (session('listing_inquiry_status'))
                        <div class="mt-5 rounded-2xl border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
                            {{ session('listing_inquiry_status') }}
                        </div>
                    @endif

                    <form action="{{ route('listings.inquire', $listing) }}" method="POST" class="mt-6 space-y-4">
                        @csrf
                        <div>
                            <label for="nombre" class="text-sm font-semibold text-white">Nombre</label>
                            <input id="nombre" name="nombre" value="{{ old('nombre') }}" class="mt-1 w-full rounded-xl border border-white/10 bg-white/5 px-3 py-3 text-sm text-white placeholder:text-zinc-400 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-300/30">
                            @error('nombre')
                                <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="text-sm font-semibold text-white">Correo</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" class="mt-1 w-full rounded-xl border border-white/10 bg-white/5 px-3 py-3 text-sm text-white placeholder:text-zinc-400 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-300/30">
                            @error('email')
                                <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="telefono" class="text-sm font-semibold text-white">Teléfono</label>
                            <input id="telefono" name="telefono" value="{{ old('telefono') }}" class="mt-1 w-full rounded-xl border border-white/10 bg-white/5 px-3 py-3 text-sm text-white placeholder:text-zinc-400 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-300/30">
                        </div>
                        <div>
                            <label for="mensaje" class="text-sm font-semibold text-white">Mensaje</label>
                            <textarea id="mensaje" name="mensaje" rows="5" class="mt-1 w-full rounded-xl border border-white/10 bg-white/5 px-3 py-3 text-sm text-white placeholder:text-zinc-400 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-300/30">{{ old('mensaje', 'Me interesa esta propiedad. ¿Podrían compartir más información y disponibilidad?') }}</textarea>
                            @error('mensaje')
                                <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-amber-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-amber-500/30 transition hover:-translate-y-0.5">
                            Enviar mensaje
                        </button>
                    </form>

                    <div class="mt-6 space-y-2 text-sm text-zinc-300">
                        @if ($listing->contact_phone)
                            <div>Tel: <a href="tel:{{ preg_replace('/[^0-9+]/', '', $listing->contact_phone) }}" class="text-amber-400">{{ $listing->contact_phone }}</a></div>
                        @endif
                        @if ($listing->contact_email)
                            <div>Email: <a href="mailto:{{ $listing->contact_email }}" class="text-amber-400">{{ $listing->contact_email }}</a></div>
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    </section>
</x-layouts.public>
