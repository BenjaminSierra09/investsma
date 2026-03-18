<?php

use App\Models\Page;
use Illuminate\Support\Facades\Http;

it('renders seo metadata on the home page', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('<title>Bienes raíces en San Miguel de Allende | investsma</title>', false);
    $response->assertSee('name="description" content="Invierte en San Miguel de Allende con propiedades seleccionadas, análisis local y acompañamiento inmobiliario para compra, renta o plusvalía."', false);
    $response->assertSee('rel="canonical" href="'.route('home').'"', false);
    $response->assertSee('https://www.googletagmanager.com/gtag/js?id=G-B7R99X31XB', false);
    $response->assertSee("gtag('config', 'G-B7R99X31XB');", false);
});

it('renders seo metadata on static public routes', function (string $routeName, string $title, string $description) {
    $response = $this->get(route($routeName));

    $response->assertOk();
    $response->assertSee('<title>'.$title.'</title>', false);
    $response->assertSee('name="description" content="'.$description.'"', false);
    $response->assertSee('rel="canonical" href="'.route($routeName).'"', false);
})->with([
    'about' => [
        'routeName' => 'about',
        'title' => 'Nosotros | investsma',
        'description' => 'Conoce al equipo de investsma y nuestra metodología para evaluar propiedades, plusvalía y riesgos en San Miguel de Allende.',
    ],
    'contact' => [
        'routeName' => 'contact',
        'title' => 'Contacto | investsma',
        'description' => 'Habla con investsma para encontrar casas, lotes y oportunidades de inversión inmobiliaria en San Miguel de Allende.',
    ],
    'properties' => [
        'routeName' => 'properties.index',
        'title' => 'Propiedades | investsma',
        'description' => 'Explora propiedades en San Miguel de Allende con filtros por zona, precio, tipo y características para identificar mejores oportunidades.',
    ],
]);

it('renders seo metadata on published cms pages', function () {
    $page = Page::query()->create([
        'title' => 'Guía de inversión',
        'slug' => 'guia-inversion',
        'status' => 'published',
        'content' => ['html' => '<p>Contenido de prueba para la guía.</p>'],
        'meta_title' => 'Guía de inversión inmobiliaria',
        'meta_description' => 'Aprende cómo evaluar oportunidades inmobiliarias en San Miguel de Allende.',
    ]);

    $response = $this->get(route('page.show', ['page' => $page->slug]));

    $response->assertOk();
    $response->assertSee('<title>Guía de inversión inmobiliaria | investsma</title>', false);
    $response->assertSee('name="description" content="Aprende cómo evaluar oportunidades inmobiliarias en San Miguel de Allende."', false);
    $response->assertSee('rel="canonical" href="'.route('page.show', ['page' => $page->slug]).'"', false);
});

it('renders seo metadata on property detail pages', function () {
    Http::fake([
        'https://ampisanmigueldeallende.com/api/v1/property/mls/*' => Http::response([
            'name' => 'Casa Luna',
            'category' => 'Residential',
            'neighborhood' => 'Centro',
            'city' => 'San Miguel de Allende',
            'description_short_es' => 'Casa con terraza, buena ubicación y potencial de renta.',
            'description_short_en' => 'Home with terrace, great location, and rental potential.',
            'photos' => ['https://example.com/casa-luna.jpg'],
            'price' => 450000,
            'currency' => 'USD',
        ]),
    ]);

    $response = $this->get(route('properties.show', [
        'mlsId' => 'MLS-123',
        'slug' => 'casa-luna',
    ]));

    $response->assertOk();
    $response->assertSee('<title>Casa Luna | investsma</title>', false);
    $response->assertSee('name="description" content="Casa con terraza, buena ubicación y potencial de renta."', false);
    $response->assertSee('rel="canonical" href="'.route('properties.show', ['mlsId' => 'MLS-123', 'slug' => 'casa-luna']).'"', false);
    $response->assertSee('property="og:image" content="https://example.com/casa-luna.jpg"', false);
});
