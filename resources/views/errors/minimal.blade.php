<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title') | {{ config('app.name') }}</title>

        <link rel="icon" href="/favicon/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon/favicon.svg" type="image/svg+xml">
        <link rel="icon" href="/favicon/favicon-96x96.png" type="image/png" sizes="96x96">
        <link rel="apple-touch-icon" href="/favicon/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet">

        @vite(['resources/css/app.css'])
    </head>
    <body class="site-shell min-h-[100dvh] text-zinc-900 antialiased">
        <main class="section-wrap flex min-h-[100dvh] items-center py-10 sm:py-16">
            <section class="grid w-full gap-8 lg:grid-cols-[minmax(0,0.92fr)_minmax(0,1.08fr)] lg:items-center">
                <div class="max-w-xl">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-4 font-semibold tracking-tight">
                        <img src="{{ asset('logotipo.png') }}" alt="Logo investsma" class="h-14 w-auto">
                        <span class="grid">
                            <span class="text-sm font-semibold text-zinc-950">investsma</span>
                            <span class="text-xs text-zinc-500">San Miguel de Allende real estate</span>
                        </span>
                    </a>

                    <div class="mt-12">
                        <div class="eyebrow">Error @yield('code')</div>

                        <h1 class="mt-5 text-4xl font-semibold leading-tight tracking-tight text-zinc-950 sm:text-5xl">
                            @yield('title')
                        </h1>

                        <p class="mt-5 max-w-lg text-base leading-relaxed text-zinc-600 sm:text-lg">
                            @yield('message')
                        </p>

                        <p class="mt-4 max-w-lg text-sm leading-relaxed text-zinc-500">
                            @yield('detail', 'Te llevamos de vuelta a una ruta segura para seguir explorando propiedades y oportunidades en San Miguel de Allende.')
                        </p>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('home') }}" class="button-primary">Volver al inicio</a>
                            <a href="{{ route('properties.index') }}" class="button-secondary">Ver propiedades</a>
                        </div>
                    </div>
                </div>

                <div class="surface-panel p-3" data-spotlight>
                    <div class="relative overflow-hidden rounded-[24px] bg-zinc-950">
                        <img
                            src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=1400&q=85"
                            alt="Casa tranquila al atardecer"
                            class="aspect-[4/5] h-full w-full object-cover opacity-85 sm:aspect-[5/4]"
                        >

                        <div class="absolute inset-0 bg-linear-to-t from-zinc-950/78 via-zinc-950/18 to-transparent"></div>

                        <div class="absolute inset-x-0 bottom-0 p-5 sm:p-6">
                            <div class="rounded-[22px] border border-white/15 bg-zinc-950/68 p-5 text-white backdrop-blur-xl">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-sm text-white/68">Siguiente paso</p>
                                        <p class="mt-2 text-xl font-semibold leading-tight">
                                            @yield('action', 'Retomar tu búsqueda con contexto local.')
                                        </p>
                                    </div>

                                    <div class="rounded-full bg-white/10 px-3 py-2 text-sm font-semibold text-amber-200">
                                        @yield('code')
                                    </div>
                                </div>

                                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                                    <a href="{{ route('contact') }}" class="rounded-[18px] bg-white/10 px-4 py-4 text-sm font-medium text-white transition hover:bg-white/15">
                                        Agenda una visita
                                    </a>
                                    <a href="{{ route('listings.index') }}" class="rounded-[18px] bg-white/10 px-4 py-4 text-sm font-medium text-white transition hover:bg-white/15">
                                        Revisar listados
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
