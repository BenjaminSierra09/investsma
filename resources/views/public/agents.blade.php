<x-layouts.public title="Agentes | investsma">
    <section class="section-wrap py-10 lg:py-14">
        <div class="max-w-3xl" data-reveal>
            <div class="eyebrow">Equipo InvestSMA</div>
            <h1 class="mt-4 text-4xl font-semibold tracking-tight text-zinc-950 sm:text-5xl">
                Agentes con contexto local para cada decisión.
            </h1>
            <p class="mt-5 text-base leading-relaxed text-zinc-600">
                Trabaja con asesores que cruzan inventario, zona, negociación y objetivo de inversión antes de recomendar una visita.
            </p>
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($agents as $agent)
                <article class="property-card overflow-hidden" data-reveal data-reveal-delay="{{ ($loop->index % 3) * 50 }}">
                    <div class="aspect-[4/3] bg-zinc-100">
                        @if ($agent->photo_url)
                            <img src="{{ $agent->photo_url }}" alt="{{ $agent->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-zinc-900 text-4xl font-semibold text-white">
                                {{ str($agent->name)->substr(0, 1)->upper() }}
                            </div>
                        @endif
                    </div>

                    <div class="space-y-4 p-5">
                        <div>
                            <h2 class="text-xl font-semibold text-zinc-950">{{ $agent->name }}</h2>
                            @if ($agent->title)
                                <p class="mt-1 text-sm font-medium text-amber-700">{{ $agent->title }}</p>
                            @endif
                        </div>

                        @if ($agent->bio)
                            <p class="line-clamp-4 text-sm leading-relaxed text-zinc-600">{{ $agent->bio }}</p>
                        @endif

                        <div class="flex flex-wrap gap-2 border-t border-zinc-100 pt-4 text-sm">
                            @if ($agent->email)
                                <a href="mailto:{{ $agent->email }}" class="button-secondary">Email</a>
                            @endif
                            @if ($agent->whatsapp)
                                <a href="https://wa.me/{{ preg_replace('/\D+/', '', $agent->whatsapp) }}" target="_blank" rel="noopener noreferrer" class="button-primary">WhatsApp</a>
                            @elseif ($agent->phone)
                                <a href="tel:{{ preg_replace('/[^\d+]+/', '', $agent->phone) }}" class="button-primary">Llamar</a>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="surface-panel px-6 py-12 text-center text-sm text-zinc-600 md:col-span-2 xl:col-span-3">
                    Pronto publicaremos los perfiles del equipo.
                </div>
            @endforelse
        </div>
    </section>
</x-layouts.public>
