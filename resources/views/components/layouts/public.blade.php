<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => $title ?? 'investsma | Bienes raíces en San Miguel de Allende'])
    </head>
    <body class="bg-gradient-to-br from-[#f6efe7] via-[#f9f7f3] to-[#f1e6d9] text-zinc-900 min-h-screen antialiased">
        @php
            $menuItems = \App\Models\MenuItem::tree('main');
            $items = $menuItems->isNotEmpty()
                ? $menuItems->map(function ($item) {
                    return (object) [
                        'label' => $item->label,
                        'url' => $item->resolvedUrl() ?? '#',
                        'children' => $item->children->map(fn ($child) => (object) [
                            'label' => $child->label,
                            'url' => $child->resolvedUrl() ?? '#',
                            'children' => collect(),
                        ]),
                    ];
                })
                : \App\Support\StaticPageRegistry::all()->map(fn ($page) => (object) [
                    'label' => $page['title'],
                    'url' => url($page['url']),
                    'children' => collect(),
                ]);
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
                                            @foreach ($item->children as $child)
                                                <a href="{{ $child->url }}" class="block rounded-lg px-3 py-2 text-sm text-zinc-700 hover:bg-amber-50 hover:text-amber-800">{{ $child->label }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </nav>

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
                    <div class="mx-auto max-w-6xl px-6 py-4 space-y-2">
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-amber-800 bg-amber-50 border border-amber-100">Dashboard</a>
                        @endauth
                        @guest
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-amber-800 bg-amber-50 border border-amber-100">Iniciar sesión</a>
                        @endguest
                        @foreach ($items as $item)
                            <a href="{{ $item->url }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-zinc-700 hover:bg-amber-50">{{ $item->label }}</a>
                            @foreach ($item->children as $child)
                                <a href="{{ $child->url }}" class="block rounded-lg px-4 py-2 text-sm text-zinc-500 hover:bg-amber-50">{{ $child->label }}</a>
                            @endforeach
                        @endforeach
                        <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 rounded-full bg-amber-500 text-white px-4 py-2 text-sm font-semibold shadow-md">Agenda una visita</a>
                    </div>
                </div>
            </header>

            <main class="relative z-10">
                {{ $slot }}
            </main>

            <footer class="mt-20 bg-white/80 backdrop-blur-xl border-t border-amber-100">
                <div class="mx-auto max-w-6xl px-6 py-10 grid gap-8 md:grid-cols-3">
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
                        <div class="text-sm font-semibold text-zinc-800">Explora</div>
                        <ul class="mt-3 space-y-2 text-sm text-zinc-600">
                            <li><a href="{{ route('home') }}" class="hover:text-amber-700">Inicio</a></li>
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
