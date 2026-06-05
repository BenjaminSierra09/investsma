<x-layouts.public title="Nosotros | investsma">
    <section class="section-wrap pb-10 pt-10 lg:pt-14">
        <div class="grid gap-8 lg:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)] lg:items-center">
            <div data-reveal>
                <div class="section-label">Nosotros</div>
                <h1 class="section-title text-4xl sm:text-5xl">
                    Somos un equipo local que ayuda a invertir con más contexto.
                </h1>
                <p class="section-copy max-w-xl">
                    Trabajamos con compradores que quieren entender bien una propiedad antes de comprometer tiempo, capital y energía en el proceso.
                </p>
            </div>

            <div class="surface-panel p-3" data-reveal data-reveal-delay="70" data-spotlight>
                <div class="overflow-hidden rounded-[24px]">
                    <img
                        src="https://picsum.photos/seed/investsma-about/1400/960"
                        alt="Arquitectura y calles de San Miguel de Allende"
                        class="aspect-[4/3] h-full w-full object-cover"
                        loading="lazy"
                    >
                </div>
            </div>
        </div>
    </section>

    <section class="section-wrap py-8">
        <div class="grid gap-6 lg:grid-cols-[minmax(0,0.7fr)_minmax(0,1.3fr)]">
            <div data-reveal>
                <h2 class="text-3xl font-semibold tracking-tight text-zinc-950">
                    Lo que hacemos bien está entre el tour y la decisión.
                </h2>
                <p class="mt-4 max-w-md text-sm leading-relaxed text-zinc-600">
                    No sólo mostramos inventario. Ponemos cada opción en un contexto más útil para tomar decisiones tranquilas.
                </p>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div class="feature-tile" data-reveal data-reveal-delay="40">
                    <p class="text-lg font-semibold text-zinc-950">Debida diligencia práctica</p>
                    <p class="mt-3 text-sm leading-relaxed text-zinc-600">
                        Revisamos ubicación, restricciones, uso y señales que pueden cambiar el valor real de una compra.
                    </p>
                </div>

                <div class="feature-tile" data-reveal data-reveal-delay="80">
                    <p class="text-lg font-semibold text-zinc-950">Visitas con mejor contexto</p>
                    <p class="mt-3 text-sm leading-relaxed text-zinc-600">
                        Llegas a cada recorrido con preguntas más claras y menos tiempo perdido en opciones que no encajan.
                    </p>
                </div>

                <div class="feature-tile" data-reveal data-reveal-delay="120">
                    <p class="text-lg font-semibold text-zinc-950">Seguimiento hasta el cierre</p>
                    <p class="mt-3 text-sm leading-relaxed text-zinc-600">
                        Coordinamos negociación, comunicación y los siguientes pasos para que el proceso avance con orden.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="section-wrap py-8 pb-16">
        <div class="cta-band" data-reveal>
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-center">
                <div>
                    <p class="text-sm text-white/70">Si ya estás comparando opciones</p>
                    <h2 class="mt-3 max-w-2xl text-3xl font-semibold tracking-tight">
                        Podemos ayudarte a ordenar prioridades antes de agendar visitas.
                    </h2>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row lg:flex-col">
                    <a href="{{ route('contact') }}" class="button-primary">Agenda una visita</a>
                    <a href="{{ route('properties.index') }}" class="button-secondary">Explorar propiedades</a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
