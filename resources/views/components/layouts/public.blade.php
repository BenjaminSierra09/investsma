@php
    \App\Support\SeoData::applyIfMissing(
        title: $title ?? 'investsma | Bienes raíces en San Miguel de Allende',
        description: $description ?? null,
        image: $image ?? asset('logotipo.png'),
    );
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => $title ?? 'investsma | Bienes raíces en San Miguel de Allende'])
    </head>
    <body class="site-shell min-h-[100dvh] text-zinc-900 antialiased">
        @php
            $menuItems = \App\Models\MenuItem::tree('main');
            $mapMenuItem = function ($item) use (&$mapMenuItem) {
                return (object) [
                    'label' => $item->label,
                    'url' => $item->resolvedUrl() ?? '#',
                    'children' => $item->children->map(fn ($child) => $mapMenuItem($child)),
                ];
            };

            $items = $menuItems->isNotEmpty()
                ? $menuItems->map(fn ($item) => $mapMenuItem($item))
                : \App\Support\StaticPageRegistry::all()->map(fn ($page) => (object) [
                    'label' => $page['title'],
                    'url' => url($page['url']),
                    'children' => collect(),
                ]);

            $listingChildren = collect([
                (object) [
                    'label' => 'Venta',
                    'url' => route('listings.sales'),
                    'children' => collect(),
                ],
                (object) [
                    'label' => 'Renta',
                    'url' => route('listings.rentals'),
                    'children' => collect(),
                ],
            ]);

            $listingIndex = $items->search(
                fn ($item) => rtrim($item->url, '/') === rtrim(route('listings.index'), '/')
            );

            if ($listingIndex !== false) {
                $existingListingItem = $items[$listingIndex];
                $existingChildren = collect($existingListingItem->children ?? []);

                $items[$listingIndex] = (object) [
                    'label' => $existingListingItem->label,
                    'url' => $existingListingItem->url,
                    'children' => $existingChildren->isNotEmpty() ? $existingChildren : $listingChildren,
                ];
            } else {
                $items = $items->push((object) [
                    'label' => 'Listados',
                    'url' => route('listings.index'),
                    'children' => $listingChildren,
                ]);
            }
        @endphp

        <div class="relative overflow-hidden">
            <header
                id="site-header"
                class="sticky top-0 z-30 border-b border-white/80 bg-white/72 backdrop-blur-xl"
            >
                <div class="section-wrap flex h-[72px] items-center justify-between gap-6">
                    <a
                        href="{{ route('home') }}"
                        class="flex min-w-0 items-center gap-4 font-semibold tracking-tight transition-transform duration-200 hover:scale-[0.995]"
                    >
                        <img src="{{ asset('logotipo.png') }}" alt="Logo investsma" class="h-14 w-auto" />
                        <div class="hidden min-w-0 sm:block">
                            <div class="text-sm font-semibold text-zinc-950">investsma</div>
                            <div class="truncate text-xs text-zinc-500">San Miguel de Allende real estate</div>
                        </div>
                    </a>

                    <nav class="hidden items-center gap-1 rounded-full border border-white/80 bg-white/78 px-3 py-2 text-sm font-medium text-zinc-700 shadow-[0_16px_40px_-34px_rgba(33,24,17,0.35)] lg:flex">
                        @foreach ($items as $item)
                            <div class="group relative">
                                <a
                                    href="{{ $item->url }}"
                                    class="pressable inline-flex items-center rounded-full px-3 py-2 transition-colors duration-150 hover:text-amber-700"
                                >
                                    {{ $item->label }}
                                </a>

                                @if ($item->children->isNotEmpty())
                                    <div class="pointer-events-none absolute left-0 mt-2 min-w-[220px] origin-top-left scale-95 rounded-[22px] border border-white/90 bg-white/98 opacity-0 shadow-xl ring-1 ring-black/5 transition-[transform,opacity] duration-180 [transition-timing-function:var(--ease-out-strong)] group-hover:pointer-events-auto group-hover:scale-100 group-hover:opacity-100">
                                        <div class="space-y-1 p-2">
                                            @include('partials.navigation.desktop-branch', [
                                                'items' => $item->children,
                                                'level' => 0,
                                            ])
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </nav>

                    <div class="hidden items-center gap-3 md:flex">
                        @auth
                            <a href="{{ route('dashboard') }}" class="button-secondary">Dashboard</a>
                        @endauth
                        @guest
                            <a href="{{ route('login') }}" class="button-secondary">Iniciar sesión</a>
                        @endguest
                        <a href="{{ route('contact') }}" class="button-primary">Agenda una visita</a>
                    </div>

                    <button
                        id="menu-toggle"
                        type="button"
                        aria-expanded="false"
                        class="pressable inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/90 bg-white/88 shadow-sm ring-1 ring-zinc-200/70 lg:hidden"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                        </svg>
                    </button>
                </div>

                <div id="mobile-menu" class="hidden border-t border-white/80 bg-white/92 backdrop-blur-sm lg:hidden">
                    <div class="section-wrap space-y-4 py-5">
                        <div class="space-y-3 text-sm text-zinc-700">
                            @include('partials.navigation.mobile-branch', [
                                'items' => $items,
                                'level' => 0,
                            ])
                        </div>

                        <div class="flex flex-col gap-3 border-t border-zinc-200/80 pt-4 sm:flex-row">
                            @auth
                                <a href="{{ route('dashboard') }}" class="button-secondary">Dashboard</a>
                            @endauth
                            @guest
                                <a href="{{ route('login') }}" class="button-secondary">Iniciar sesión</a>
                            @endguest
                            <a href="{{ route('contact') }}" class="button-primary">Agenda una visita</a>
                        </div>
                    </div>
                </div>
            </header>

            <main class="relative z-10">
                {{ $slot }}
            </main>

            <footer class="mt-24 border-t border-white/80 bg-white/72 backdrop-blur-xl">
                <div class="section-wrap grid gap-10 py-14 md:grid-cols-2 xl:grid-cols-[1.4fr_1fr_1fr_1fr]">
                    <div class="max-w-md">
                        <div class="text-xl font-semibold tracking-tight text-zinc-950">investsma</div>
                        <p class="mt-4 text-sm leading-relaxed text-zinc-600">
                            Acompañamos decisiones inmobiliarias en San Miguel de Allende con lectura local, filtros claros y acompañamiento cercano desde la primera visita hasta el cierre.
                        </p>
                    </div>

                    <div>
                        <div class="text-sm font-semibold text-zinc-900">Contacto</div>
                        <div class="mt-4 space-y-3 text-sm text-zinc-600">
                            <div><a href="tel:+524151255042" class="hover:text-amber-700">+52 415 125 5042</a></div>
                            <div><a href="mailto:info@investsma.com" class="hover:text-amber-700">info@investsma.com</a></div>
                            <div>San Miguel de Allende, Guanajuato</div>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold text-zinc-900">Explora</div>
                        <ul class="mt-4 space-y-3 text-sm text-zinc-600">
                            <li><a href="{{ route('home') }}" class="hover:text-amber-700">Inicio</a></li>
                            <li><a href="{{ route('properties.index') }}" class="hover:text-amber-700">Propiedades</a></li>
                            <li><a href="{{ route('listings.index') }}" class="hover:text-amber-700">Listados</a></li>
                            <li><a href="{{ route('about') }}" class="hover:text-amber-700">Nosotros</a></li>
                            <li><a href="{{ route('contact') }}" class="hover:text-amber-700">Agenda una visita</a></li>
                        </ul>
                    </div>

                    <div>
                        <div class="text-sm font-semibold text-zinc-900">Redes</div>
                        <ul class="mt-4 space-y-3 text-sm text-zinc-600">
                            <li><a href="https://investsma.com/" class="hover:text-amber-700" target="_blank" rel="noopener noreferrer">Sitio principal</a></li>
                            <li><a href="https://www.instagram.com/investsma" class="hover:text-amber-700" target="_blank" rel="noopener noreferrer">Instagram</a></li>
                            <li><a href="https://www.facebook.com/InvestSMA.InvestSMA" class="hover:text-amber-700" target="_blank" rel="noopener noreferrer">Facebook</a></li>
                            <li><a href="https://www.linkedin.com/in/investsma" class="hover:text-amber-700" target="_blank" rel="noopener noreferrer">LinkedIn</a></li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-white/80">
                    <div class="section-wrap flex flex-col gap-2 py-4 text-xs text-zinc-500 md:flex-row md:items-center md:justify-between">
                        <div>© {{ now()->year }} investsma. Todos los derechos reservados.</div>
                        <div>
                            Hecho en México por
                            <a href="https://benjaminsierra.com" class="text-amber-700">Benjamin Sierra</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
