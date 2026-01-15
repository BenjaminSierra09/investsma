<x-layouts.public title="{{ __('About us | investsma') }}">
    <section class="mx-auto max-w-5xl px-6 pt-16 lg:pt-20 pb-16">
        <div class="rounded-[28px] border border-amber-100/80 bg-white/80 p-8 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">{{ __('About us') }}</p>
            <h1 class="mt-3 text-3xl font-semibold text-zinc-900">{{ __('We are local allies to invest wisely in San Miguel de Allende.') }}</h1>
            <p class="mt-4 text-sm text-zinc-700 leading-relaxed">{{ __('investsma was born from working with developers, notaries, and vacation rental managers. We understand which properties preserve value, what permits are required, and how to negotiate favorable terms.') }}</p>

            <div class="mt-8 grid gap-6 md:grid-cols-3">
                @foreach ([
                    ['title' => __('Due diligence'), 'desc' => __('We review legal status, zoning, and condo restrictions.')],
                    ['title' => __('Guidance'), 'desc' => __('We coordinate signings, inspections, and the relationship with property managers.')],
                    ['title' => __('Strategy'), 'desc' => __('We recommend rental schemes, staging, and mid-term exits.')],
                ] as $item)
                    <div class="rounded-2xl bg-amber-50/60 p-5 ring-1 ring-amber-100">
                        <div class="text-sm font-semibold text-zinc-900">{{ $item['title'] }}</div>
                        <p class="mt-2 text-xs text-zinc-600 leading-relaxed">{{ $item['desc'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-10 rounded-2xl bg-zinc-900 px-6 py-6 text-white">
                <h2 class="text-xl font-semibold">{{ __('Ready to review options?') }}</h2>
                <p class="mt-2 text-sm text-zinc-200">{{ __('Let\'s schedule a call to share the portfolio that best fits your goal.') }}</p>
                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="mailto:hola@investsma.com" class="inline-flex items-center justify-center rounded-full bg-white px-4 py-2 text-sm font-semibold text-zinc-900">{{ __('Write to us') }}</a>
                    <a href="{{ route('contact') }}" class="inline-flex items-center justify-center rounded-full border border-white/40 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">{{ __('Schedule a visit') }}</a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
