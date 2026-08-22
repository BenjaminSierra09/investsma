<?php

use App\Livewire\Public\PropertiesSearch;
use App\Models\AmpiProperty;
use App\Support\AmpiPropertyApi;
use Illuminate\Support\Facades\Artisan;
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
            'meta' => ['last_page' => 1, 'total' => 1],
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

test('ampi property sync is scheduled as a direct artisan command', function () {
    Artisan::call('schedule:list');

    expect(Artisan::output())
        ->toContain('0 2 * * *')
        ->toContain('ampi:sync-properties --delete-missing')
        ->not->toContain('App\Jobs\SyncAmpiPropertiesJob');
});

test('ampi property sync permanently deletes properties missing from a complete api response', function () {
    config()->set('services.ampi.api_key', 'test-api-key');
    config()->set('cache.default', 'array');

    AmpiProperty::factory()->create([
        'mls_id' => 'SMA-EXPIRED',
        'name' => 'Propiedad Expirada',
    ]);

    Http::fake([
        'https://ampisanmigueldeallende.com/api/v1/properties/search*' => Http::response([
            'data' => [
                [
                    'id' => 1002,
                    'mls_id' => 'SMA-ACTIVE',
                    'name' => 'Propiedad Vigente',
                    'price' => 300000,
                    'currency' => 'USD',
                ],
            ],
            'meta' => ['last_page' => 1, 'total' => 1],
        ]),
    ]);

    $this->artisan('ampi:sync-properties --delete-missing --per-page=50 --max-pages=1')
        ->expectsOutputToContain('1 missing properties permanently deleted')
        ->assertSuccessful();

    $this->assertDatabaseMissing('ampi_properties', ['mls_id' => 'SMA-EXPIRED']);
    $this->assertDatabaseHas('ampi_properties', ['mls_id' => 'SMA-ACTIVE']);
});

test('ampi property sync does not delete properties when a later api page fails', function () {
    config()->set('services.ampi.api_key', 'test-api-key');
    config()->set('cache.default', 'array');

    AmpiProperty::factory()->create([
        'mls_id' => 'SMA-PRESERVED',
        'name' => 'Propiedad Preservada',
    ]);

    Http::fake(function ($request) {
        if ((int) $request->data()['page'] === 1) {
            return Http::response([
                'data' => [
                    [
                        'id' => 1003,
                        'mls_id' => 'SMA-FIRST-PAGE',
                        'name' => 'Propiedad Primera Página',
                    ],
                ],
                'meta' => ['last_page' => 2, 'total' => 2],
            ]);
        }

        return Http::response(['message' => 'Unavailable'], 503);
    });

    $this->artisan('ampi:sync-properties --delete-missing --per-page=50 --max-pages=2')
        ->assertFailed();

    $this->assertDatabaseHas('ampi_properties', ['mls_id' => 'SMA-PRESERVED']);
    $this->assertDatabaseHas('ampi_properties', ['mls_id' => 'SMA-FIRST-PAGE']);
});

test('ampi property sync does not delete properties when the page limit truncates the catalog', function () {
    config()->set('services.ampi.api_key', 'test-api-key');
    config()->set('cache.default', 'array');

    AmpiProperty::factory()->create([
        'mls_id' => 'SMA-PRESERVED',
        'name' => 'Propiedad Preservada',
    ]);

    Http::fake([
        'https://ampisanmigueldeallende.com/api/v1/properties/search*' => Http::response([
            'data' => [
                [
                    'id' => 1004,
                    'mls_id' => 'SMA-FIRST-PAGE',
                    'name' => 'Propiedad Primera Página',
                ],
            ],
            'meta' => ['last_page' => 2, 'total' => 2],
        ]),
    ]);

    $this->artisan('ampi:sync-properties --delete-missing --per-page=50 --max-pages=1')
        ->assertFailed();

    $this->assertDatabaseHas('ampi_properties', ['mls_id' => 'SMA-PRESERVED']);
});

test('ampi property sync preserves local properties when the api unexpectedly returns an empty catalog', function () {
    config()->set('services.ampi.api_key', 'test-api-key');
    config()->set('cache.default', 'array');

    AmpiProperty::factory()->create([
        'mls_id' => 'SMA-PRESERVED',
        'name' => 'Propiedad Preservada',
    ]);

    Http::fake([
        'https://ampisanmigueldeallende.com/api/v1/properties/search*' => Http::response([
            'data' => [],
            'meta' => ['last_page' => 1, 'total' => 0],
        ]),
    ]);

    $this->artisan('ampi:sync-properties --delete-missing')
        ->assertFailed();

    $this->assertDatabaseHas('ampi_properties', ['mls_id' => 'SMA-PRESERVED']);
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

test('property detail hydrates missing local photos from the ampi detail endpoint', function () {
    config()->set('services.ampi.api_key', 'test-api-key');
    config()->set('cache.default', 'array');

    AmpiProperty::factory()->create([
        'mls_id' => 'SMA-11344',
        'name' => '1000m2 Lot Offering Great Mountain And Valley Views',
        'slug' => '1000m2-lot-offering-great-mountain-and-valley-views',
        'category' => 'Land and Lots',
        'neighborhood' => 'Atascadero',
        'city' => 'San Miguel de Allende',
        'price' => 150000,
        'currency' => 'USD',
        'photos' => [],
        'featured_image' => null,
        'raw_payload' => [
            'mls_id' => 'SMA-11344',
            'name' => '1000m2 Lot Offering Great Mountain And Valley Views',
        ],
    ]);

    Http::fake([
        'https://ampisanmigueldeallende.com/api/v1/property/mls/11344' => Http::response([
            'mls_id' => 'SMA-11344',
            'name' => '1000m2 Lot Offering Great Mountain And Valley Views',
            'description_short_en' => 'A lot with mountain and valley views.',
            'category' => 'Land and Lots',
            'neighborhood' => 'Atascadero',
            'city' => 'San Miguel de Allende',
            'price' => 150000,
            'currency' => 'USD',
            'photos' => [
                ['url' => 'https://example.com/detail-1.jpg'],
                'https://example.com/detail-2.jpg',
            ],
        ], 200),
    ]);

    $this->get(route('properties.show', [
        'mlsId' => '11344',
        'slug' => '1000m2-lot-offering-great-mountain-and-valley-views',
    ]))
        ->assertOk()
        ->assertSee('https://example.com/detail-1.jpg', false)
        ->assertSee('https://example.com/detail-2.jpg', false);

    expect(AmpiProperty::query()->where('mls_id', 'SMA-11344')->first()?->photos)
        ->toBe([
            'https://example.com/detail-1.jpg',
            'https://example.com/detail-2.jpg',
        ]);
});
