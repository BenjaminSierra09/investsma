@php
    use Illuminate\Support\Str;

    $items = $properties['data'] ?? $properties ?? [];
    $featured = array_slice($items, 0, 3);
    $fallback = [
        ['title' => __('Garden home in Guadiana'), 'price' => 'USD 895,000', 'tag' => __('Turnkey'), 'image' => 'https://images.unsplash.com/photo-1505692069463-5e3405e3e7ee?auto=format&fit=crop&w=900&q=80'],
        ['title' => __('Lot in Atotonilco'), 'price' => 'USD 215,000', 'tag' => __('Capital gain'), 'image' => 'https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?auto=format&fit=crop&w=900&q=80'],
        ['title' => __('Loft Historic Center'), 'price' => 'USD 420,000', 'tag' => __('Vacation rental'), 'image' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=900&q=80'],
    ];
@endphp
<x-layouts.public title="{{ __('Home | investsma') }}">
    <section class="mx-auto max-w-6xl px-6 pt-16 lg:pt-20">
        <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
            <div class="space-y-6">
                <div class="inline-flex items-center gap-2 rounded-full bg-white/80 px-3 py-1 text-xs font-semibold text-amber-800 ring-1 ring-amber-200 shadow-sm">
                    {{ __('Real estate investment in San Miguel de Allende') }}
                </div>
                <h1 class="text-4xl font-semibold leading-tight text-zinc-900 lg:text-5xl">
                    {{ __('Curated properties to live in and grow your equity.') }}
                </h1>
                <p class="text-lg text-zinc-700 leading-relaxed">
                    {{ __('We source homes, lots, and developments with proven value, negotiate on your behalf, and coordinate the legal process so you can invest confidently.') }}
                </p>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="{{ route('contact') }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-amber-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-amber-200 transition hover:-translate-y-0.5 hover:shadow-xl">{{ __('Book a call') }}</a>
                    <a href="{{ route('about') }}" class="inline-flex items-center justify-center gap-2 rounded-full px-5 py-3 text-sm font-semibold text-amber-800 ring-1 ring-amber-200 bg-white/70 hover:bg-white">{{ __('See our methodology') }}</a>
                </div>
                <div class="grid grid-cols-3 gap-4 pt-4">
                    <div class="rounded-2xl bg-white/80 p-4 shadow-sm ring-1 ring-zinc-100">
                        <div class="text-3xl font-semibold text-amber-600">+140</div>
                        <div class="text-xs text-zinc-500">{{ __('Properties analyzed') }}</div>
                    </div>
                    <div class="rounded-2xl bg-white/80 p-4 shadow-sm ring-1 ring-zinc-100">
                        <div class="text-3xl font-semibold text-amber-600">8.5%</div>
                        <div class="text-xs text-zinc-500">{{ __('Target yield') }}</div>
                    </div>
                    <div class="rounded-2xl bg-white/80 p-4 shadow-sm ring-1 ring-zinc-100">
                        <div class="text-3xl font-semibold text-amber-600">24h</div>
                        <div class="text-xs text-zinc-500">{{ __('Response to new listings') }}</div>
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="absolute inset-0 -left-12 -top-6 rounded-[32px] bg-gradient-to-br from-amber-200/60 via-white to-emerald-100/60 blur-2xl"></div>
                <div class="relative mt-6 rounded-2xl border border-amber-100/70 bg-white/80 p-4 shadow-sm ring-1 ring-white/60">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">{{ __('Search properties') }}</p>
                            <p class="text-sm text-zinc-600">{{ __('Filter the office inventory in real time.') }}</p>
                        </div>
                        <a href="{{ route('properties.index') }}" class="hidden text-xs font-semibold text-amber-700 hover:text-amber-800 sm:inline-flex">{{ __('Advanced view') }} →</a>
                    </div>
                    <form action="{{ route('properties.index') }}" method="GET" class="mt-4 space-y-3">
                        <div class="grid gap-3 md:grid-cols-3">
                            <div>
                                <label class="text-xs font-semibold text-zinc-800">{{ __('Area / neighborhood') }}</label>
                                <input list="home-neighborhoods" name="neighborhood" value="{{ request('neighborhood') }}" class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-200" placeholder="{{ __('Centro, Guadiana') }}" />
                                @if (!empty($neighborhoods))
                                    <datalist id="home-neighborhoods">
                                        @foreach ($neighborhoods as $n)
                                            <option value="{{ $n }}"></option>
                                        @endforeach
                                    </datalist>
                                @endif
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-zinc-800">{{ __('Type') }}</label>
                                <select name="category" class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-200">
                                    <option value="">{{ __('All') }}</option>
                                    <option value="Residential" @selected(request('category') === 'Residential')>Residential</option>
                                    <option value="Land and Lots" @selected(request('category') === 'Land and Lots')>Land and Lots</option>
                                    <option value="Commercial" @selected(request('category') === 'Commercial')>Commercial</option>
                                    <option value="Pre Sales" @selected(request('category') === 'Pre Sales')>Pre Sales</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-zinc-800">{{ __('Status') }}</label>
                                <select name="status" class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-200">
                                    <option value="">{{ __('All') }}</option>
                                    <option value="For Sale" @selected(request('status') === 'For Sale')>For Sale</option>
                                    <option value="Price Reduction" @selected(request('status') === 'Price Reduction')>Price Reduction</option>
                                    <option value="For Rent" @selected(request('status') === 'For Rent')>For Rent</option>
                                    <option value="Contract Pending" @selected(request('status') === 'Contract Pending')>Contract Pending</option>
                                    <option value="Under Contract" @selected(request('status') === 'Under Contract')>Under Contract</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid gap-3 md:grid-cols-4">
                            <div>
                                <label class="text-xs font-semibold text-zinc-800">{{ __('Minimum price') }}</label>
                                <input type="number" min="0" step="1000" name="price_min" value="{{ request('price_min') }}" class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-200" placeholder="100000" />
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-zinc-800">{{ __('Maximum price') }}</label>
                                <input type="number" min="0" step="1000" name="price_max" value="{{ request('price_max') }}" class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-200" placeholder="500000" />
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-zinc-800">{{ __('Min. bedrooms') }}</label>
                                <select name="bedrooms" class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-200">
                                    <option value="">{{ __('Any') }}</option>
                                    @foreach ([1,2,3,4,5] as $beds)
                                        <option value="{{ $beds }}" @selected(request('bedrooms') == $beds)>{{ $beds }}+</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-zinc-800">{{ __('Min. bathrooms') }}</label>
                                <select name="bathrooms" class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-200">
                                    <option value="">{{ __('Any') }}</option>
                                    @foreach ([1,2,3,4,5] as $baths)
                                        <option value="{{ $baths }}" @selected(request('bathrooms') == $baths)>{{ $baths }}+</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <div class="w-48 max-w-full">
                                <label class="text-xs font-semibold text-zinc-800">{{ __('Currency') }}</label>
                                <select name="currency" class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-200">
                                    <option value="">{{ __('All') }}</option>
                                    <option value="USD" @selected(request('currency') === 'USD')>USD</option>
                                    <option value="MXN" @selected(request('currency') === 'MXN')>MXN</option>
                                    <option value="CAD" @selected(request('currency') === 'CAD')>CAD</option>
                                    <option value="EUR" @selected(request('currency') === 'EUR')>EUR</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-amber-500 px-4 py-2 text-sm font-semibold text-white shadow-md transition hover:-translate-y-0.5">{{ __('Search') }}</button>
                            <a href="{{ route('home') }}" class="text-sm font-semibold text-zinc-600 hover:text-amber-700">{{ __('Clear filters') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    @if (! empty($items))
        <section class="mx-auto mt-16 max-w-6xl px-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-600">{{ __('Properties') }}</p>
                    <h2 class="text-2xl font-semibold text-zinc-900">{{ __('Curated inventory') }}</h2>
                    <p class="text-sm text-zinc-600">{{ __('Browse the office portfolio in real time.') }}</p>
                </div>
                <a href="{{ route('properties.index') }}" class="hidden text-sm font-semibold text-amber-700 hover:text-amber-800 md:inline-flex">{{ __('See all') }} →</a>
            </div>

            @php
                $meta = isset($properties['current_page']) ? $properties : ($properties['meta'] ?? null);
                $query = request()->query();
            @endphp

            <div class="mt-6 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($items as $property)
                    <div class="group overflow-hidden rounded-2xl border border-amber-100/70 bg-white/80 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="relative aspect-video overflow-hidden bg-zinc-100">
                            <img
                                src="{{ $property['featured_image'] ?? 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1200&q=80' }}"
                                alt="{{ $property['name'] ?? 'Propiedad' }}"
                                class="h-full w-full object-cover transition duration-700 group-hover:scale-105"
                                loading="lazy"
                            >
                            @if (! empty($property['status']))
                                <span class="absolute left-3 top-3 inline-flex rounded-full bg-white/90 px-3 py-1 text-[11px] font-semibold text-amber-700 shadow">{{ $property['status'] }}</span>
                            @endif
                        </div>
                        <div class="space-y-2 px-4 py-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="text-base font-semibold text-zinc-900 line-clamp-2">{{ $property['name'] ?? __('Property') }}</div>
                                @if (! empty($property['price']))
                                    <div class="text-sm font-semibold text-amber-700 whitespace-nowrap">{{ $property['currency'] ?? 'USD' }} ${{ number_format($property['price'], 0) }}</div>
                                @endif
                            </div>
                            <div class="text-xs text-zinc-600">{{ $property['neighborhood'] ?? 'San Miguel de Allende' }} · {{ $property['city'] ?? 'SMA' }}</div>
                            <div class="flex flex-wrap gap-2 text-xs text-zinc-700">
                                @if (! empty($property['bedrooms']))
                                    <span class="rounded-full bg-amber-50 px-2 py-1">{{ $property['bedrooms'] }} {{ __('bed') }}</span>
                                @endif
                                @if (! empty($property['bathrooms']))
                                    <span class="rounded-full bg-amber-50 px-2 py-1">{{ $property['bathrooms'] }} {{ __('bath') }}</span>
                                @endif
                                @if (! empty($property['construction_meters']))
                                    <span class="rounded-full bg-amber-50 px-2 py-1">{{ $property['construction_meters'] }} {{ __('sqm') }}</span>
                                @endif
                            </div>
                            <p class="text-sm text-zinc-600 line-clamp-2">{{ $property['description_short_es'] ?? $property['description_short_en'] ?? __('Check more details in the listing.') }}</p>
                            <div class="pt-1">
                                <a
                                    href="{{ route('properties.show', ['mlsId' => $property['mls_id'] ?? $property['id'] ?? null, 'slug' => Str::slug($property['name'] ?? __('property'))]) }}"
                                    class="inline-flex items-center gap-2 text-sm font-semibold text-amber-700 hover:text-amber-800"
                                >
                                    {{ __('See details') }}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 md:hidden">
                <a href="{{ route('properties.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-amber-700 hover:text-amber-800">{{ __('See all') }} →</a>
            </div>

            @if ($meta && ($meta['last_page'] ?? 1) > 1)
                <div class="mt-6 flex flex-col gap-2 rounded-xl border border-amber-100/70 bg-white/80 px-4 py-3 text-sm text-zinc-700 md:flex-row md:items-center md:justify-between">
                    <div>
                        {{ __('Page') }} {{ $meta['current_page'] ?? 1 }} {{ __('of') }} {{ $meta['last_page'] ?? 1 }}
                        @if (!empty($meta['from']) && !empty($meta['to']) && !empty($meta['total']))
                            <span class="text-xs text-zinc-500">· {{ __('Showing') }} {{ $meta['from'] }}–{{ $meta['to'] }} {{ __('of') }} {{ $meta['total'] }}</span>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        @php $prevPage = max(1, ($meta['current_page'] ?? 1) - 1); @endphp
                        @php $nextPage = min(($meta['last_page'] ?? 1), ($meta['current_page'] ?? 1) + 1); @endphp
                        <a
                            href="{{ route('home', array_merge($query, ['page' => $prevPage])) }}"
                            class="rounded-lg border border-amber-100 px-3 py-1 {{ ($meta['current_page'] ?? 1) <= 1 ? 'pointer-events-none opacity-50' : 'hover:bg-amber-50' }}"
                        >{{ __('Previous') }}</a>
                        <a
                            href="{{ route('home', array_merge($query, ['page' => $nextPage])) }}"
                            class="rounded-lg border border-amber-100 px-3 py-1 {{ ($meta['current_page'] ?? 1) >= ($meta['last_page'] ?? 1) ? 'pointer-events-none opacity-50' : 'hover:bg-amber-50' }}"
                        >{{ __('Next') }}</a>
                    </div>
                </div>
            @endif
        </section>
    @endif

    <section class="mx-auto mt-16 max-w-6xl px-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-600">{{ __('Collections') }}</p>
                <h2 class="text-2xl font-semibold text-zinc-900">{{ __('Portfolios we curate closely') }}</h2>
            </div>
            <a href="{{ route('contact') }}" class="hidden text-sm font-semibold text-amber-700 hover:text-amber-800 md:inline-flex">{{ __('Request more options') }} →</a>
        </div>
        <div class="mt-6 grid gap-6 md:grid-cols-3">
            @foreach ([
                ['title' => __('Colonial Luxe'), 'desc' => __('Homes with courtyards, terraces, and artisan finishes ready to rent.'), 'badge' => __('Downtown')],
                ['title' => __('Terraces & Views'), 'desc' => __('Lots and residences on hillsides with Parroquia and valley views.'), 'badge' => __('View')],
                ['title' => __('Boutique Projects'), 'desc' => __('New condos with amenities and rental management.'), 'badge' => __('Presale')],
            ] as $collection)
                <div class="rounded-2xl border border-amber-100/70 bg-white/80 p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-[11px] font-semibold text-amber-700 ring-1 ring-amber-100">{{ $collection['badge'] }}</div>
                    <h3 class="mt-3 text-xl font-semibold text-zinc-900">{{ $collection['title'] }}</h3>
                    <p class="mt-2 text-sm text-zinc-600 leading-relaxed">{{ $collection['desc'] }}</p>
                    <a href="{{ route('contact') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-amber-700 hover:text-amber-800">{{ __('See selections') }} →</a>
                </div>
            @endforeach
        </div>
    </section>

    <section class="mx-auto mt-16 max-w-6xl px-6">
        <div class="rounded-[28px] border border-emerald-100 bg-gradient-to-br from-emerald-50 via-white to-amber-50 p-8 shadow-lg">
            <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
                <div class="space-y-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-emerald-700">{{ __('Method') }}</p>
                    <h2 class="text-2xl font-semibold text-zinc-900">{{ __('Due diligence, negotiation, and guided operations.') }}</h2>
                    <p class="text-sm text-zinc-700 leading-relaxed">{{ __('We review documents, value with local comps, and design the tax and rental strategy that works best for you. We also coordinate notary, architects, and property managers.') }}</p>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ([
                        ['title' => __('Analysis'), 'desc' => __('Valuation, legal risks, and rental projection')],
                        ['title' => __('Negotiation'), 'desc' => __('Data-backed offers and clear closings')],
                        ['title' => __('Oversight'), 'desc' => __('Support through inspections and delivery')],
                        ['title' => __('Operations'), 'desc' => __('We coordinate management, rentals, or resale')],
                    ] as $step)
                        <div class="rounded-2xl bg-white/80 p-4 ring-1 ring-emerald-100 shadow-sm">
                            <div class="text-sm font-semibold text-zinc-900">{{ $step['title'] }}</div>
                            <p class="mt-1 text-xs text-zinc-600 leading-relaxed">{{ $step['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto mt-16 max-w-6xl px-6 pb-20">
        <div class="rounded-[28px] bg-zinc-900 px-8 py-10 text-white shadow-xl">
            <div class="grid gap-8 lg:grid-cols-2 lg:items-center">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-200">{{ __('Let\'s talk') }}</p>
                    <h2 class="mt-3 text-3xl font-semibold">{{ __('Tell us what you are looking for and we will prepare a shortlist in 48h.') }}</h2>
                    <p class="mt-3 text-sm text-zinc-200">{{ __('We share the best options based on your goal: living, vacation rental, or medium-term appreciation.') }}</p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end sm:items-center">
                    <a href="mailto:hola@investsma.com" class="inline-flex items-center justify-center rounded-full bg-white px-5 py-3 text-sm font-semibold text-zinc-900 shadow-lg transition hover:-translate-y-0.5">{{ __('Write to us') }}</a>
                    <a href="{{ route('contact') }}" class="inline-flex items-center justify-center rounded-full border border-white/40 px-5 py-3 text-sm font-semibold text-white hover:bg-white/10">{{ __('Schedule a visit') }}</a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
