<?php

return [
    'menus' => [
        'main' => 'Menú principal',
    ],

    'static_pages' => [
        [
            'key' => 'home',
            'title' => 'Inicio',
            'route' => 'home',
            'url' => '/',
            'view' => 'public.home',
            'description' => 'Página principal de investsma',
        ],
        [
            'key' => 'about',
            'title' => 'Nosotros',
            'route' => 'about',
            'url' => '/nosotros',
            'view' => 'public.about',
            'description' => 'Información de la empresa y el equipo.',
        ],
        [
            'key' => 'contact',
            'title' => 'Contacto',
            'route' => 'contact',
            'url' => '/contacto',
            'view' => 'public.contact',
            'description' => 'Formulario de contacto y datos de la oficina.',
        ],
        [
            'key' => 'listings',
            'title' => 'Listados',
            'route' => 'listings.index',
            'url' => '/listados',
            'view' => 'public.listings-index',
            'description' => 'Propiedades propias publicadas directamente por investsma.',
        ],
    ],
];
