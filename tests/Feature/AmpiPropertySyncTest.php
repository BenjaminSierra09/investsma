<?php

use App\Livewire\Public\PropertiesSearch;
use App\Models\AmpiProperty;
use App\Support\AmpiPropertyApi;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

test('ampi properties are synchronized into the local database', function () {
    config()->set('services.ampi.api_key', 'test-api-key');
    config()->set('cache.default', 'array');

    Http::fake([
        'https://ampisanmigueldeallende.com/api/v1/properties/search*' => Http::response([
            'data' => [
                [
                    'id' => 1001,
                    'mls_id' => 'SMA-1001',
                    'name' => 'Casa Sincronizada',
                    'price' => 200000,
                    'currency' => 'USD',
                    'office_id' => 32,
                    'city' => 'San Miguel de Allende',
                    'neighborhood' => 'Centro',
                    'latitude' => 20.9152,
                    'longitude' => -100.7439,
                ],
            ],
            'meta' => ['last_page' => 1],
        ], 200),
    ]);

    $this->artisan('ampi:sync-properties --per-page=50 --max-pages=1')
        ->assertSuccessful();

    $this->assertDatabaseHas('ampi_properties', [
        'mls_id' => 'SMA-1001',
        'name' => 'Casa Sincronizada',
        'currency' => 'USD',
        'normalized_price' => 3400000,
    ]);
});

test('local searches sort and filter prices using normalized currency values', function () {
    AmpiProperty::factory()->create([
        'mls_id' => 'SMA-USD',
        'name' => 'Casa USD',
        'price' => 200000,
        'currency' => 'USD',
        'raw_payload' => ['mls_id' => 'SMA-USD', 'name' => 'Casa USD'],
    ]);

    AmpiProperty::factory()->create([
        'mls_id' => 'SMA-MXN',
        'name' => 'Casa MXN',
        'price' => 5000000,
        'currency' => 'MXN',
        'raw_payload' => ['mls_id' => 'SMA-MXN', 'name' => 'Casa MXN'],
    ]);

    AmpiProperty::factory()->create([
        'mls_id' => 'SMA-CAD',
        'name' => 'Casa CAD',
        'price' => 500000,
        'currency' => 'CAD',
        'raw_payload' => ['mls_id' => 'SMA-CAD', 'name' => 'Casa CAD'],
    ]);

    $api = app(AmpiPropertyApi::class);

    $highestFirst = collect($api->search(['sort' => 'price_desc', 'per_page' => 10])['data'])
        ->pluck('mls_id')
        ->all();

    expect($highestFirst)->toBe(['SMA-CAD', 'SMA-MXN', 'SMA-USD']);

    $filtered = collect($api->search([
        'currency' => 'USD',
        'price_min' => 250000,
        'sort' => 'price_asc',
        'per_page' => 10,
    ])['data'])->pluck('mls_id')->all();

    expect($filtered)->toBe(['SMA-MXN', 'SMA-CAD']);
});

test('local searches default to newest properties by numeric mls id', function () {
    AmpiProperty::factory()->create([
        'mls_id' => 'SMA-80',
        'name' => 'Propiedad Antigua',
        'price' => 900000,
        'currency' => 'USD',
        'raw_payload' => ['mls_id' => 'SMA-80', 'name' => 'Propiedad Antigua'],
    ]);

    AmpiProperty::factory()->create([
        'mls_id' => 'SMA-200',
        'name' => 'Propiedad Reciente',
        'price' => 100000,
        'currency' => 'USD',
        'raw_payload' => ['mls_id' => 'SMA-200', 'name' => 'Propiedad Reciente'],
    ]);

    $results = collect(app(AmpiPropertyApi::class)->search(['per_page' => 10])['data'])
        ->pluck('mls_id')
        ->all();

    expect($results)->toBe(['SMA-200', 'SMA-80']);
});

test('livewire properties search uses the local database without requiring ampi credentials', function () {
    AmpiProperty::factory()->create([
        'mls_id' => 'SMA-80',
        'name' => 'Casa Local USD',
        'price' => 200000,
        'currency' => 'USD',
        'neighborhood' => 'Centro',
        'raw_payload' => ['mls_id' => 'SMA-80', 'name' => 'Casa Local USD'],
    ]);

    AmpiProperty::factory()->create([
        'mls_id' => 'SMA-200',
        'name' => 'Casa Local MXN',
        'price' => 5000000,
        'currency' => 'MXN',
        'neighborhood' => 'Centro',
        'raw_payload' => ['mls_id' => 'SMA-200', 'name' => 'Casa Local MXN'],
    ]);

    Livewire::test(PropertiesSearch::class)
        ->call('search')
        ->assertSeeInOrder(['Casa Local MXN', 'Casa Local USD'])
        ->assertDontSee('Falta configurar la API key de AMPI.');
});
