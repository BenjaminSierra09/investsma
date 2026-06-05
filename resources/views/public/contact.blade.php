<x-layouts.public title="Contacto | investsma">
    <section class="section-wrap pb-10 pt-10 lg:pt-14">
        <div class="grid gap-8 lg:grid-cols-[minmax(0,0.76fr)_minmax(0,1.24fr)] lg:items-end">
            <div data-reveal>
                <div class="section-label">Contacto</div>
                <h1 class="section-title text-4xl sm:text-5xl">
                    Cuéntanos qué estás buscando y armamos el primer filtro contigo.
                </h1>
                <p class="section-copy max-w-xl">
                    Comparte presupuesto, zonas de interés y horizonte de compra. Te respondemos con una lectura inicial más útil que una lista genérica.
                </p>
            </div>

            <div class="surface-panel p-3" data-reveal data-reveal-delay="70" data-spotlight>
                <div class="overflow-hidden rounded-[24px]">
                    <img
                        src="https://picsum.photos/seed/investsma-contact/1400/960"
                        alt="Casa en San Miguel de Allende"
                        class="aspect-[4/3] h-full w-full object-cover"
                        loading="lazy"
                    >
                </div>
            </div>
        </div>
    </section>

    <section class="section-wrap pb-16">
        <div class="grid gap-8 lg:grid-cols-[minmax(0,1.05fr)_minmax(0,0.95fr)] lg:items-start">
            <div class="surface-panel p-6 sm:p-8" data-reveal>
                @if (session('status'))
                    <div class="rounded-[20px] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 shadow-sm">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-[20px] border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 shadow-sm">
                        <p class="font-semibold">Revisa los campos marcados.</p>
                        <ul class="mt-2 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label for="nombre" class="field-label">Nombre completo</label>
                        <input id="nombre" name="nombre" value="{{ old('nombre') }}" required class="field-input" placeholder="María López">
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="email" class="field-label">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required class="field-input" placeholder="tu@email.com">
                        </div>

                        <div>
                            <label for="telefono" class="field-label">Teléfono</label>
                            <input id="telefono" name="telefono" value="{{ old('telefono') }}" class="field-input" placeholder="+52 415 ...">
                        </div>
                    </div>

                    <div>
                        <label for="objetivo" class="field-label">Objetivo</label>
                        <select id="objetivo" name="objetivo" class="field-select">
                            <option value="Vivir en San Miguel" @selected(old('objetivo') === 'Vivir en San Miguel')>Vivir en San Miguel</option>
                            <option value="Renta vacacional" @selected(old('objetivo') === 'Renta vacacional')>Renta vacacional</option>
                            <option value="Renta de largo plazo" @selected(old('objetivo') === 'Renta de largo plazo')>Renta de largo plazo</option>
                            <option value="Plusvalía o preventa" @selected(old('objetivo') === 'Plusvalía o preventa')>Plusvalía o preventa</option>
                        </select>
                    </div>

                    <div>
                        <label for="mensaje" class="field-label">Mensaje</label>
                        <textarea id="mensaje" name="mensaje" class="field-textarea" placeholder="Presupuesto, zonas de interés, plazo de compra, tipo de propiedad...">{{ old('mensaje') }}</textarea>
                    </div>

                    <button type="submit" class="button-primary">Enviar mensaje</button>
                </form>
            </div>

            <div class="space-y-4" data-reveal data-reveal-delay="90">
                <div class="feature-tile">
                    <p class="text-lg font-semibold text-zinc-950">Qué incluimos en la primera respuesta</p>
                    <div class="mt-4 space-y-3 text-sm leading-relaxed text-zinc-600">
                        <p>Una lectura inicial de encaje entre tu objetivo y el inventario actual.</p>
                        <p>Zonas o tipos de propiedad que vale la pena mirar primero.</p>
                        <p>Señales de precio, fricción y próximos pasos para avanzar con orden.</p>
                    </div>
                </div>

                <div class="surface-panel-dark p-6">
                    <p class="text-sm text-white/65">San Miguel de Allende</p>
                    <h2 class="mt-3 text-2xl font-semibold">Hablemos de tu búsqueda.</h2>
                    <div class="mt-6 space-y-3 text-sm text-white/72">
                        <div><a href="tel:+524151255042" class="text-amber-300">+52 415 125 5042</a></div>
                        <div><a href="mailto:info@investsma.com" class="text-amber-300">info@investsma.com</a></div>
                        <div>Atención con cita y seguimiento personalizado.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
