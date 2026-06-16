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

it('shows invest sma ampi agents on AMPI MLS properties when they belong to office 32', function () {
    config()->set('services.ampi.api_key', 'test-api-key');
    config()->set('cache.default', 'array');

    Http::fake([
        'https://ampisanmigueldeallende.com/api/v1/property/mls/11344' => Http::response([
            'id' => 11344,
            'mls_id' => 'SMA-11344',
            'name' => 'Casa AMPI Invest',
            'description_short_en' => 'MLS home with Invest SMA agent.',
            'category' => 'Casa',
            'neighborhood' => 'Centro',
            'city' => 'San Miguel de Allende',
            'price' => 550000,
            'currency' => 'USD',
            'photos' => ['https://example.com/ampi-invest.jpg'],
            'agents' => [501],
        ]),
        'https://ampisanmigueldeallende.com/api/v1/agent/501' => Http::response([
            'id' => 501,
            'name' => 'Laura Invest',
            'email' => 'laura@investsma.com',
            'mobile' => '+52 415 111 2222',
            'office' => [
                'id' => 32,
                'name' => 'INVEST SMA',
            ],
            'image' => '6576725a343c5_adelanew.jpg',
        ]),
    ]);

    $this->get(route('properties.show', [
        'mlsId' => '11344',
        'slug' => 'casa-ampi-invest',
    ]))
        ->assertOk()
        ->assertSee('Agente INVEST SMA')
        ->assertSee('Laura Invest')
        ->assertSee('laura@investsma.com')
        ->assertSee('+52 415 111 2222')
        ->assertSee('https://ampisanmigueldeallende.com/storage/images/6576725a343c5_adelanew.jpg', false);
});
