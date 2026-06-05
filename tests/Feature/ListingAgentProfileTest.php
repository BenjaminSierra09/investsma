<?php

use App\Models\Agent;
use App\Models\Listing;
use Illuminate\Support\Facades\Http;

it('shows the assigned agent profile on custom listing pages', function () {
    $agent = Agent::factory()->create([
        'name' => 'María González',
        'title' => 'Asesora inmobiliaria',
        'email' => 'maria@investsma.com',
        'phone' => '+52 415 123 4567',
        'whatsapp' => '524151234567',
        'bio' => 'Especialista en propiedades patrimoniales y oportunidades de inversión en San Miguel de Allende.',
    ]);

    $listing = Listing::factory()->for($agent)->create([
        'title' => 'Casa Jacaranda',
    ]);

    $this->get(route('listings.show', $listing))
        ->assertOk()
        ->assertSee('Asesor asignado')
        ->assertSee('María González')
        ->assertSee('Asesora inmobiliaria')
        ->assertSee('Especialista en propiedades patrimoniales');
});

it('does not show an agent profile on AMPI MLS properties', function () {
    Http::fake([
        'https://ampisanmigueldeallende.com/api/v1/property/mls/*' => Http::response([
            'name' => 'Casa AMPI',
            'description_short_es' => 'Casa en exclusiva MLS.',
            'description_short_en' => 'MLS home.',
            'category' => 'Casa',
            'neighborhood' => 'Centro',
            'city' => 'San Miguel de Allende',
            'price' => 550000,
            'currency' => 'USD',
            'photos' => ['https://example.com/ampi.jpg'],
        ]),
    ]);

    $this->get(route('properties.show', ['mlsId' => 'AMP-101', 'slug' => 'casa-ampi']))
        ->assertOk()
        ->assertSee('Casa AMPI')
        ->assertSee('ID AMPI MLS')
        ->assertDontSee('Asesor asignado')
        ->assertDontSee('María González');
});
