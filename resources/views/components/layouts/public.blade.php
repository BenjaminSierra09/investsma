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
    <body class="bg-gradient-to-br from-[#f6efe7] via-[#f9f7f3] to-[#f1e6d9] text-zinc-900 min-h-screen antialiased">
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

            $hasListings = $items->contains(
                fn ($item) => rtrim($item->url, '/') === rtrim(route('listings.index'), '/')
            );

            if (! $hasListings) {
                $items = $items->push((object) [
                    'label' => 'Listados',
                    'url' => route('listings.index'),
                    'children' => collect(),
                ]);
            }
        @endphp

        <div class="relative overflow-hidden">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -left-24 top-10 h-72 w-72 rounded-full bg-amber-200/40 blur-3xl"></div>
                <div class="absolute right-0 top-40 h-80 w-80 rounded-full bg-emerald-200/40 blur-3xl"></div>
            </div>

            <header class="sticky top-0 z-30 backdrop-blur-xl bg-white/70 shadow-sm">
                <div class="mx-auto max-w-6xl px-6 py-4 flex items-center justify-between">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 font-semibold tracking-tight">
                        <img src="{{ asset('logotipo.png') }}" alt="Logo investsma" class="h-15" />
                    </a>

                    <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-zinc-700">
                        @foreach ($items as $item)
                            <div class="relative group">
                                <a href="{{ $item->url }}" class="transition hover:text-amber-700">{{ $item->label }}</a>
                                @if ($item->children->isNotEmpty())
                                    <div class="absolute left-0 mt-2 min-w-[200px] rounded-xl bg-white shadow-lg ring-1 ring-zinc-200 opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition origin-top">
                                        <div class="p-2 space-y-1">
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

                    <div class="hidden md:flex items-center gap-3 text-zinc-500">
                        <a href="https://investsma.com/" class="hover:text-amber-700 transition" target="_blank" rel="noopener noreferrer" aria-label="Sitio web" title="Sitio web">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c4.97 0 9 4.03 9 9s-4.03 9-9 9-9-4.03-9-9 4.03-9 9-9Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.6 9h16.8M3.6 15h16.8M12 3c-2.4 2.5-3.6 5.5-3.6 9s1.2 6.5 3.6 9c2.4-2.5 3.6-5.5 3.6-9S14.4 5.5 12 3Z" />
                            </svg>
                        </a>
                        <a href="https://www.facebook.com/InvestSMA.InvestSMA" class="hover:text-amber-700 transition" target="_blank" rel="noopener noreferrer" aria-label="Facebook" title="Facebook">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                <path d="M13.5 10.5H15V8h-1.5c-.83 0-1.5.67-1.5 1.5V11H10v2h2v6h2v-6h1.7l.3-2H14v-.5c0-.28.22-.5.5-.5Z" />
                                <path d="M12 3C7 3 3 7 3 12s4 9 9 9 9-4 9-9-4-9-9-9Z" fill="none" stroke="currentColor" stroke-width="1.2" />
                            </svg>
                        </a>
                        <a href="https://www.instagram.com/investsma" class="hover:text-amber-700 transition" target="_blank" rel="noopener noreferrer" aria-label="Instagram" title="Instagram">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" class="h-5 w-5">
                                <rect x="4" y="4" width="16" height="16" rx="4" ry="4" />
                                <circle cx="12" cy="12" r="3.3" />
                                <circle cx="16.5" cy="7.5" r="0.8" fill="currentColor" />
                            </svg>
                        </a>
                        <a href="https://www.tiktok.com/@invest.sma" class="hover:text-amber-700 transition" target="_blank" rel="noopener noreferrer" aria-label="TikTok" title="TikTok">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                <path d="M14 3h2.2c.1 1.5 1.1 2.7 2.7 2.8V8c-.9-.03-1.8-.25-2.6-.67v5.12c0 2.7-2.2 4.9-4.9 4.9A4.9 4.9 0 0 1 6.5 12c0-2.6 2-4.74 4.6-4.9v2.1c-1.4.16-2.4 1.36-2.2 2.78a2.36 2.36 0 0 0 2.5 2.05c1.3-.08 2.3-1.18 2.3-2.5V3Z" />
                            </svg>
                        </a>
                        <a href="https://www.youtube.com/channel/UCqqYbt1tC631RanLj31dWxg" class="hover:text-amber-700 transition" target="_blank" rel="noopener noreferrer" aria-label="YouTube" title="YouTube">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                <path d="M21 8.5s-.2-1.4-.8-2c-.8-.8-1.7-.8-2.2-.9-3.1-.2-7.8-.2-7.8-.2h-.1s-4.7 0-7.8.2c-.5.1-1.4.1-2.2.9-.6.6-.8 2-.8 2S0 10.1 0 11.8v1.4c0 1.7.2 3.3.2 3.3s.2 1.4.8 2c.8.8 1.8.8 2.2.9 1.6.1 6.6.2 7.6.2h.1s4.7 0 7.8-.2c.5-.1 1.4-.1 2.2-.9.6-.6.8-2 .8-2s.2-1.6.2-3.3v-1.4c0-1.7-.2-3.3-.2-3.3ZM9.5 14.7v-4.4l4.2 2.2-4.2 2.2Z" />
                            </svg>
                        </a>
                        <a href="https://www.linkedin.com/in/investsma" class="hover:text-amber-700 transition" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn" title="LinkedIn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                <path d="M20.45 3H3.55C3.25 3 3 3.25 3 3.55v16.9c0 .3.25.55.55.55h16.9c.3 0 .55-.25.55-.55V3.55c0-.3-.25-.55-.55-.55ZM8.1 18.1H5.6v-7h2.5v7Zm-1.25-8c-.8 0-1.3-.55-1.3-1.25 0-.7.5-1.25 1.3-1.25.8 0 1.3.55 1.3 1.25 0 .7-.5 1.25-1.3 1.25Zm11.25 8h-2.5v-3.8c0-1.05-.4-1.75-1.35-1.75-.75 0-1.14.5-1.33.98-.07.16-.08.38-.08.6v4h-2.5s.03-6.5 0-7h2.5v1.05c.33-.5.92-1.23 2.23-1.23 1.63 0 2.88 1.06 2.88 3.34v3.84Z" />
                            </svg>
                        </a>
                    </div>

                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="hidden md:inline-flex items-center gap-2 rounded-full border border-amber-200 px-4 py-2 text-sm font-semibold text-amber-800 bg-white/70 shadow-sm hover:-translate-y-0.5 hover:shadow-md transition">Dashboard</a>
                        @endauth
                        @guest
                            <a href="{{ route('login') }}" class="hidden md:inline-flex items-center gap-2 rounded-full border border-amber-200 px-4 py-2 text-sm font-semibold text-amber-800 bg-white/70 shadow-sm hover:-translate-y-0.5 hover:shadow-md transition">Iniciar sesión</a>
                        @endguest
                        <a href="{{ route('contact') }}" class="hidden md:inline-flex items-center gap-2 rounded-full bg-amber-500 text-white px-4 py-2 text-sm font-semibold shadow-md transition hover:-translate-y-0.5 hover:shadow-lg">Agenda una visita</a>
                        <button id="menu-toggle" class="md:hidden inline-flex h-10 w-10 items-center justify-center rounded-full bg-white shadow ring-1 ring-zinc-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div id="mobile-menu" class="md:hidden hidden border-t border-zinc-200 bg-white/90 backdrop-blur-sm">
                    <div class="mx-auto max-w-6xl px-6 py-4 space-y-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-amber-800 bg-amber-50 border border-amber-100">Dashboard</a>
                        @endauth
                        @guest
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-amber-800 bg-amber-50 border border-amber-100">Iniciar sesión</a>
                        @endguest
                        @include('partials.navigation.mobile-branch', [
                            'items' => $items,
                            'level' => 0,
                        ])
                        <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 rounded-full bg-amber-500 text-white px-4 py-2 text-sm font-semibold shadow-md">Agenda una visita</a>
                        <div class="flex items-center gap-4 pt-2 text-zinc-500">
                            <a href="https://investsma.com/" class="hover:text-amber-700 transition" target="_blank" rel="noopener noreferrer" aria-label="Sitio web" title="Sitio web">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c4.97 0 9 4.03 9 9s-4.03 9-9 9-9-4.03-9-9 4.03-9 9-9Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.6 9h16.8M3.6 15h16.8M12 3c-2.4 2.5-3.6 5.5-3.6 9s1.2 6.5 3.6 9c2.4-2.5 3.6-5.5 3.6-9S14.4 5.5 12 3Z" />
                                </svg>
                            </a>
                            <a href="https://www.facebook.com/InvestSMA.InvestSMA" class="hover:text-amber-700 transition" target="_blank" rel="noopener noreferrer" aria-label="Facebook" title="Facebook">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                    <path d="M13.5 10.5H15V8h-1.5c-.83 0-1.5.67-1.5 1.5V11H10v2h2v6h2v-6h1.7l.3-2H14v-.5c0-.28.22-.5.5-.5Z" />
                                    <path d="M12 3C7 3 3 7 3 12s4 9 9 9 9-4 9-9-4-9-9-9Z" fill="none" stroke="currentColor" stroke-width="1.2" />
                                </svg>
                            </a>
                            <a href="https://www.instagram.com/investsma" class="hover:text-amber-700 transition" target="_blank" rel="noopener noreferrer" aria-label="Instagram" title="Instagram">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" class="h-5 w-5">
                                    <rect x="4" y="4" width="16" height="16" rx="4" ry="4" />
                                    <circle cx="12" cy="12" r="3.3" />
                                    <circle cx="16.5" cy="7.5" r="0.8" fill="currentColor" />
                                </svg>
                            </a>
                            <a href="https://www.tiktok.com/@invest.sma" class="hover:text-amber-700 transition" target="_blank" rel="noopener noreferrer" aria-label="TikTok" title="TikTok">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                    <path d="M14 3h2.2c.1 1.5 1.1 2.7 2.7 2.8V8c-.9-.03-1.8-.25-2.6-.67v5.12c0 2.7-2.2 4.9-4.9 4.9A4.9 4.9 0 0 1 6.5 12c0-2.6 2-4.74 4.6-4.9v2.1c-1.4.16-2.4 1.36-2.2 2.78a2.36 2.36 0 0 0 2.5 2.05c1.3-.08 2.3-1.18 2.3-2.5V3Z" />
                                </svg>
                            </a>
                            <a href="https://www.youtube.com/channel/UCqqYbt1tC631RanLj31dWxg" class="hover:text-amber-700 transition" target="_blank" rel="noopener noreferrer" aria-label="YouTube" title="YouTube">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                    <path d="M21 8.5s-.2-1.4-.8-2c-.8-.8-1.7-.8-2.2-.9-3.1-.2-7.8-.2-7.8-.2h-.1s-4.7 0-7.8.2c-.5.1-1.4.1-2.2.9-.6.6-.8 2-.8 2S0 10.1 0 11.8v1.4c0 1.7.2 3.3.2 3.3s.2 1.4.8 2c.8.8 1.8.8 2.2.9 1.6.1 6.6.2 7.6.2h.1s4.7 0 7.8-.2c.5-.1 1.4-.1 2.2-.9.6-.6.8-2 .8-2s.2-1.6.2-3.3v-1.4c0-1.7-.2-3.3-.2-3.3ZM9.5 14.7v-4.4l4.2 2.2-4.2 2.2Z" />
                                </svg>
                            </a>
                            <a href="https://www.linkedin.com/in/investsma" class="hover:text-amber-700 transition" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn" title="LinkedIn">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                    <path d="M20.45 3H3.55C3.25 3 3 3.25 3 3.55v16.9c0 .3.25.55.55.55h16.9c.3 0 .55-.25.55-.55V3.55c0-.3-.25-.55-.55-.55ZM8.1 18.1H5.6v-7h2.5v7Zm-1.25-8c-.8 0-1.3-.55-1.3-1.25 0-.7.5-1.25 1.3-1.25.8 0 1.3.55 1.3 1.25 0 .7-.5 1.25-1.3 1.25Zm11.25 8h-2.5v-3.8c0-1.05-.4-1.75-1.35-1.75-.75 0-1.14.5-1.33.98-.07.16-.08.38-.08.6v4h-2.5s.03-6.5 0-7h2.5v1.05c.33-.5.92-1.23 2.23-1.23 1.63 0 2.88 1.06 2.88 3.34v3.84Z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <main class="relative z-10">
                {{ $slot }}
            </main>

            <footer class="mt-20 bg-white/80 backdrop-blur-xl border-t border-amber-100">
                <div class="mx-auto max-w-6xl px-6 py-10 grid gap-8 md:grid-cols-4">
                    <div>
                        <div class="flex items-center gap-2 text-lg font-semibold">investsma</div>
                        <p class="mt-2 text-sm text-zinc-600">Acompañamos tu inversión inmobiliaria en San Miguel de Allende con asesoría local y criterios de plusvalía.</p>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-zinc-800">Contacto</div>
                        <div class="mt-3 space-y-2 text-sm text-zinc-600">
                            <div>{{ __('Phone') }}: <a href="tel:+524151255042" class="text-amber-600">+52 415 125 5042</a></div>
                            <div>{{ __('Email') }}: <a href="mailto:info@investsma.com" class="text-amber-600">info@investsma.com</a></div>
                        </div>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-zinc-800">Redes sociales</div>
                        <ul class="mt-3 space-y-2 text-sm text-zinc-600">
                            <li><a href="https://investsma.com/" class="hover:text-amber-700" target="_blank" rel="noopener noreferrer">Web</a></li>
                            <li><a href="https://www.facebook.com/InvestSMA.InvestSMA" class="hover:text-amber-700" target="_blank" rel="noopener noreferrer">Facebook</a></li>
                            <li><a href="https://www.instagram.com/investsma" class="hover:text-amber-700" target="_blank" rel="noopener noreferrer">Instagram</a></li>
                            <li><a href="https://www.tiktok.com/@invest.sma" class="hover:text-amber-700" target="_blank" rel="noopener noreferrer">TikTok</a></li>
                            <li><a href="https://www.youtube.com/channel/UCqqYbt1tC631RanLj31dWxg" class="hover:text-amber-700" target="_blank" rel="noopener noreferrer">YouTube</a></li>
                            <li><a href="https://www.linkedin.com/in/investsma" class="hover:text-amber-700" target="_blank" rel="noopener noreferrer">LinkedIn</a></li>
                        </ul>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-zinc-800">Explora</div>
                        <ul class="mt-3 space-y-2 text-sm text-zinc-600">
                            <li><a href="{{ route('home') }}" class="hover:text-amber-700">Inicio</a></li>
                            <li><a href="{{ route('listings.index') }}" class="hover:text-amber-700">Listados</a></li>
                            <li><a href="{{ route('about') }}" class="hover:text-amber-700">Nosotros</a></li>
                            <li><a href="{{ route('contact') }}" class="hover:text-amber-700">Contacto</a></li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-amber-100 bg-white/70">
                    <div class="mx-auto max-w-6xl px-6 py-4 text-xs text-zinc-500">© {{ now()->year }} investsma. Todos los derechos reservados. Hecho en México por <a href="https://benjaminsierra.com" class="text-amber-700">Benjamin Sierra</a></div>
                </div>
            </footer>
        </div>

        <script>
            const toggle = document.getElementById('menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');

            if (toggle && mobileMenu) {
                toggle.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                });
            }
        </script>
    </body>
</html>
