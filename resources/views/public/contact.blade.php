<x-layouts.public title="{{ __('Contact | investsma') }}">
    <section class="mx-auto max-w-5xl px-6 pt-16 lg:pt-20 pb-16">
        <div class="grid gap-8 lg:grid-cols-2 lg:items-start">
            <div class="rounded-[28px] border border-amber-100/70 bg-white/80 p-8 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">{{ __('Contact') }}</p>
                <h1 class="mt-3 text-3xl font-semibold text-zinc-900">{{ __('Tell us what you are looking for.') }}</h1>
                <p class="mt-3 text-sm text-zinc-700 leading-relaxed">{{ __('We prepare a shortlist with the listings and lots that best fit your goal. We include comps, risks, and rental estimates.') }}</p>

                @if (session('status'))
                    <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 shadow-sm">
                        {{ session('status') }}
                    </div>
                @endif

                <form class="mt-6 space-y-4" method="POST" action="{{ route('contact.submit') }}">
                    @csrf
                    <div>
                        <label class="text-sm font-semibold text-zinc-800">{{ __('Full name') }}</label>
                        <input name="nombre" value="{{ old('nombre') }}" required class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-3 text-sm shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-200" placeholder="{{ __('Maria Lopez') }}" />
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-zinc-800">{{ __('Email') }}</label>
                            <input name="email" type="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-3 text-sm shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-200" placeholder="{{ __('you@email.com') }}" />
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-zinc-800">{{ __('Phone') }}</label>
                            <input name="telefono" value="{{ old('telefono') }}" class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-3 text-sm shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-200" placeholder="+52" />
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-zinc-800">{{ __('Investment goal') }}</label>
                        <select name="objetivo" class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-3 text-sm shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-200">
                            <option @selected(old('objetivo') === 'Live in SMA')>{{ __('Live in SMA') }}</option>
                            <option @selected(old('objetivo') === 'Vacation rental')>{{ __('Vacation rental') }}</option>
                            <option @selected(old('objetivo') === 'Long-term rental')>{{ __('Long-term rental') }}</option>
                            <option @selected(old('objetivo') === 'Appreciation / presale')>{{ __('Appreciation / presale') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-zinc-800">{{ __('Message') }}</label>
                        <textarea name="mensaje" rows="4" class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-3 text-sm shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-200" placeholder="{{ __('Budget, areas of interest, purchase timeline...') }}">{{ old('mensaje') }}</textarea>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-amber-500 px-5 py-3 text-sm font-semibold text-white shadow-md transition hover:-translate-y-0.5">{{ __('Send') }}</button>
                </form>
            </div>
            <div class="rounded-[28px] bg-zinc-900 p-8 text-white shadow-xl">
                <div class="text-sm font-semibold uppercase tracking-[0.25em] text-amber-200">{{ __('Office') }}</div>
                <p class="mt-3 text-xl font-semibold">{{ __('San Miguel de Allende') }}</p>
                <p class="mt-2 text-sm text-zinc-200">{{ __('Historic center and Golden Corridor') }}</p>
                <div class="mt-6 space-y-3 text-sm text-zinc-200">
                    <div>{{ __('Phone (English)') }}: <a href="tel:+524151793155" class="text-amber-200">+52 415 179 3155</a></div>
                    <div>{{ __('Phone (Spanish)') }}: <a href="tel:+524151230502" class="text-amber-200">+52 415 123 0502</a></div>
                    <div>{{ __('Email') }}: <a href="mailto:info@investsma.com" class="text-amber-200">info@investsma.com</a></div>
                    <div>{{ __('Schedule a visit by appointment.') }}</div>
                </div>
                <div class="mt-10 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-white/5 p-4">
                        <div class="text-sm font-semibold">{{ __('Personalized advisory') }}</div>
                        <p class="text-xs text-zinc-200">{{ __('We listen to your plan and design a tailored search.') }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/5 p-4">
                        <div class="text-sm font-semibold">{{ __('Curated selection') }}</div>
                        <p class="text-xs text-zinc-200">{{ __('We share fact sheets with comps and clear risks.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
