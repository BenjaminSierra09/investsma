<?php

use App\Livewire\Public\PropertiesSearch;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

test('home search exposes observed neighborhoods missing from the official catalog', function () {
    config()->set('services.ampi.api_key', 'test-api-key');
    config()->set('cache.default', 'array');
    Cache::flush();

    Http::fake(function (Request $request) {
        $query = $request->data();

        if ($request->url() === 'https://ampisanmigueldeallende.com/api/v1/neighborhoods') {
            return Http::response([
                ['name' => 'Centro'],
            ], 200);
        }

        if (str_starts_with($request->url(), 'https://ampisanmigueldeallende.com/api/v1/properties/search')) {
            if ((int) ($query['per_page'] ?? 0) === 100) {
                return Http::response([
                    'data' => [
                        ['neighborhood' => 'San Miguel de Allende Centro'],
                        ['neighborhood' => 'Centro'],
                    ],
                    'last_page' => 1,
                ], 200);
            }

            return Http::response([
                'data' => [
                    [
                        'mls_id' => 'SMA-101',
                        'name' => 'Casa Centro',
                        'neighborhood' => 'San Miguel de Allende Centro',
                        'city' => 'San Miguel de Allende',
                    ],
                ],
                'last_page' => 1,
            ], 200);
        }

        return Http::response([], 404);
    });

    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('Propiedades en San Miguel de Allende con criterio patrimonial.')
        ->assertSee('Explorar propiedades')
        ->assertSeeHtml('name="keywords"')
        ->assertSeeHtml('option value="San Miguel de Allende Centro"')
        ->assertSeeHtml('data-choices');
});

test('home search decodes neighborhood names returned with html entities', function () {
    config()->set('services.ampi.api_key', 'test-api-key');
    config()->set('cache.default', 'array');
    Cache::flush();

    Http::fake(function (Request $request) {
        $query = $request->data();

        if ($request->url() === 'https://ampisanmigueldeallende.com/api/v1/neighborhoods') {
            return Http::response([
                ['name' => 'Los &Oacute;rganos'],
            ], 200);
        }

        if (str_starts_with($request->url(), 'https://ampisanmigueldeallende.com/api/v1/properties/search')) {
            if ((int) ($query['per_page'] ?? 0) === 100) {
                return Http::response([
                    'data' => [
                        ['neighborhood' => 'Los &Oacute;rganos'],
                    ],
                    'last_page' => 1,
                ], 200);
            }

            return Http::response([
                'data' => [],
                'last_page' => 1,
            ], 200);
        }

        return Http::response([], 404);
    });

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Los Órganos')
        ->assertDontSee('&Oacute;');
});

test('properties index renders successfully without neighborhood filters in the query string', function () {
    config()->set('services.ampi.api_key', 'test-api-key');
    config()->set('cache.default', 'array');
    Cache::flush();

    Http::fake(function (Request $request) {
        $query = $request->data();

        if ($request->url() === 'https://ampisanmigueldeallende.com/api/v1/neighborhoods') {
            return Http::response([
                ['name' => 'Centro'],
            ], 200);
        }

        if (str_starts_with($request->url(), 'https://ampisanmigueldeallende.com/api/v1/properties/search')) {
            if ((int) ($query['per_page'] ?? 0) === 100) {
                return Http::response([
                    'data' => [
                        ['neighborhood' => 'Centro'],
                    ],
                    'last_page' => 1,
                ], 200);
            }

            return Http::response([
                'data' => [
                    [
                        'mls_id' => 'SMA-151',
                        'name' => 'Casa Centro',
                        'neighborhood' => 'Centro',
                        'city' => 'San Miguel de Allende',
                    ],
                ],
                'last_page' => 1,
            ], 200);
        }

        return Http::response([], 404);
    });

    $this->get(route('properties.index'))
        ->assertOk()
        ->assertSee('Filtra el inventario con una vista más clara.')
        ->assertSee('Casa Centro');
});

test('properties index accepts neighborhood filters passed as a string query parameter', function () {
    config()->set('services.ampi.api_key', 'test-api-key');
    config()->set('cache.default', 'array');
    Cache::flush();

    Http::fake(function (Request $request) {
        $query = $request->data();

        if ($request->url() === 'https://ampisanmigueldeallende.com/api/v1/neighborhoods') {
            return Http::response([
                ['name' => 'Centro'],
            ], 200);
        }

        if (str_starts_with($request->url(), 'https://ampisanmigueldeallende.com/api/v1/properties/search')) {
            if ((int) ($query['per_page'] ?? 0) === 100) {
                return Http::response([
                    'data' => [
                        ['neighborhood' => 'Centro'],
                    ],
                    'last_page' => 1,
                ], 200);
            }

            $selectedNeighborhoods = array_values(array_filter((array) ($query['neighborhood'] ?? [])));

            if ($selectedNeighborhoods === ['Centro']) {
                return Http::response([
                    'data' => [
                        [
                            'mls_id' => 'SMA-152',
                            'name' => 'Casa Con Filtro',
                            'neighborhood' => 'Centro',
                            'city' => 'San Miguel de Allende',
                        ],
                    ],
                    'last_page' => 1,
                ], 200);
            }

            return Http::response([
                'data' => [],
                'last_page' => 1,
            ], 200);
        }

        return Http::response([], 404);
    });

    $this->get(route('properties.index', ['neighborhood' => 'Centro']))
        ->assertOk()
        ->assertSee('Casa Con Filtro');

    Http::assertSent(function (Request $request): bool {
        $query = $request->data();
        $selectedNeighborhoods = array_values(array_filter((array) ($query['neighborhood'] ?? [])));

        return str_starts_with($request->url(), 'https://ampisanmigueldeallende.com/api/v1/properties/search')
            && $selectedNeighborhoods === ['Centro'];
    });
});

test('properties index preserves neighborhoods that contain commas in their names', function () {
    config()->set('services.ampi.api_key', 'test-api-key');
    config()->set('cache.default', 'array');
    Cache::flush();

    Http::fake(function (Request $request) {
        $query = $request->data();

        if ($request->url() === 'https://ampisanmigueldeallende.com/api/v1/neighborhoods') {
            return Http::response([
                ['name' => 'Los Senderos, Valle de los'],
            ], 200);
        }

        if (str_starts_with($request->url(), 'https://ampisanmigueldeallende.com/api/v1/properties/search')) {
            if ((int) ($query['per_page'] ?? 0) === 100) {
                return Http::response([
                    'data' => [
                        ['neighborhood' => 'Los Senderos, Valle de los'],
                    ],
                    'last_page' => 1,
                ], 200);
            }

            $selectedNeighborhoods = array_values(array_filter((array) ($query['neighborhood'] ?? [])));

            if ($selectedNeighborhoods === ['Los Senderos, Valle de los']) {
                return Http::response([
                    'data' => [
                        [
                            'mls_id' => 'SMA-153',
                            'name' => 'Casa Senderos',
                            'neighborhood' => 'Los Senderos, Valle de los',
                            'city' => 'San Miguel de Allende',
                        ],
                    ],
                    'last_page' => 1,
                ], 200);
            }

            return Http::response([
                'data' => [],
                'last_page' => 1,
            ], 200);
        }

        return Http::response([], 404);
    });

    $this->get(route('properties.index', ['neighborhood' => 'Los Senderos, Valle de los']))
        ->assertOk()
        ->assertSee('Casa Senderos')
        ->assertSee('Los Senderos, Valle de los');

    Http::assertSent(function (Request $request): bool {
        $query = $request->data();
        $selectedNeighborhoods = array_values(array_filter((array) ($query['neighborhood'] ?? [])));

        return str_starts_with($request->url(), 'https://ampisanmigueldeallende.com/api/v1/properties/search')
            && $selectedNeighborhoods === ['Los Senderos, Valle de los'];
    });
});

test('livewire property search renders observed neighborhoods and forwards keywords to api filters', function () {
    config()->set('services.ampi.api_key', 'test-api-key');
    config()->set('cache.default', 'array');
    Cache::flush();

    Http::fake(function (Request $request) {
        $query = $request->data();

        if ($request->url() === 'https://ampisanmigueldeallende.com/api/v1/neighborhoods') {
            return Http::response([
                ['name' => 'Centro'],
            ], 200);
        }

        if (str_starts_with($request->url(), 'https://ampisanmigueldeallende.com/api/v1/properties/search')) {
            if ((int) ($query['per_page'] ?? 0) === 100) {
                return Http::response([
                    'data' => [
                        ['neighborhood' => 'Libramiento A Dolores'],
                        ['neighborhood' => 'Centro'],
                    ],
                    'last_page' => 1,
                ], 200);
            }

            $selectedNeighborhoods = array_values(array_filter((array) ($query['neighborhood'] ?? [])));

            if ($selectedNeighborhoods === ['Libramiento A Dolores'] && ($query['keywords'] ?? null) === 'jardin') {
                return Http::response([
                    'data' => [
                        [
                            'mls_id' => 'SMA-202',
                            'name' => 'Casa Libramiento',
                            'neighborhood' => 'Libramiento A Dolores',
                            'city' => 'San Miguel de Allende',
                        ],
                    ],
                    'last_page' => 1,
                ], 200);
            }

            return Http::response([
                'data' => [],
                'last_page' => 1,
            ], 200);
        }

        return Http::response([], 404);
    });

    Livewire::test(PropertiesSearch::class)
        ->assertSee('Filtra el inventario con una vista más clara.')
        ->assertSee('Libramiento A Dolores')
        ->set('neighborhood', ['Libramiento A Dolores'])
        ->set('keywords', 'jardin')
        ->call('search')
        ->assertSee('Casa Libramiento');

    Http::assertSent(function (Request $request): bool {
        $query = $request->data();
        $selectedNeighborhoods = array_values(array_filter((array) ($query['neighborhood'] ?? [])));

        return str_starts_with($request->url(), 'https://ampisanmigueldeallende.com/api/v1/properties/search')
            && $selectedNeighborhoods === ['Libramiento A Dolores']
            && ($query['keywords'] ?? null) === 'jardin';
    });
});

test('livewire property search normalizes string neighborhood values before rendering and searching', function () {
    config()->set('services.ampi.api_key', 'test-api-key');
    config()->set('cache.default', 'array');
    Cache::flush();

    Http::fake(function (Request $request) {
        $query = $request->data();

        if ($request->url() === 'https://ampisanmigueldeallende.com/api/v1/neighborhoods') {
            return Http::response([
                ['name' => 'Centro'],
            ], 200);
        }

        if (str_starts_with($request->url(), 'https://ampisanmigueldeallende.com/api/v1/properties/search')) {
            if ((int) ($query['per_page'] ?? 0) === 100) {
                return Http::response([
                    'data' => [
                        ['neighborhood' => 'Centro'],
                    ],
                    'last_page' => 1,
                ], 200);
            }

            $selectedNeighborhoods = array_values(array_filter((array) ($query['neighborhood'] ?? [])));

            if ($selectedNeighborhoods === ['Centro']) {
                return Http::response([
                    'data' => [
                        [
                            'mls_id' => 'SMA-203',
                            'name' => 'Casa Centro String',
                            'neighborhood' => 'Centro',
                            'city' => 'San Miguel de Allende',
                        ],
                    ],
                    'last_page' => 1,
                ], 200);
            }

            return Http::response([
                'data' => [],
                'last_page' => 1,
            ], 200);
        }

        return Http::response([], 404);
    });

    Livewire::test(PropertiesSearch::class)
        ->set('neighborhood', 'Centro')
        ->call('search')
        ->assertSee('Casa Centro String');

    Http::assertSent(function (Request $request): bool {
        $query = $request->data();
        $selectedNeighborhoods = array_values(array_filter((array) ($query['neighborhood'] ?? [])));

        return str_starts_with($request->url(), 'https://ampisanmigueldeallende.com/api/v1/properties/search')
            && $selectedNeighborhoods === ['Centro'];
    });
});

test('livewire property search keeps string neighborhoods with commas intact', function () {
    config()->set('services.ampi.api_key', 'test-api-key');
    config()->set('cache.default', 'array');
    Cache::flush();

    Http::fake(function (Request $request) {
        $query = $request->data();

        if ($request->url() === 'https://ampisanmigueldeallende.com/api/v1/neighborhoods') {
            return Http::response([
                ['name' => 'Los Senderos, Valle de los'],
            ], 200);
        }

        if (str_starts_with($request->url(), 'https://ampisanmigueldeallende.com/api/v1/properties/search')) {
            if ((int) ($query['per_page'] ?? 0) === 100) {
                return Http::response([
                    'data' => [
                        ['neighborhood' => 'Los Senderos, Valle de los'],
                    ],
                    'last_page' => 1,
                ], 200);
            }

            $selectedNeighborhoods = array_values(array_filter((array) ($query['neighborhood'] ?? [])));

            if ($selectedNeighborhoods === ['Los Senderos, Valle de los']) {
                return Http::response([
                    'data' => [
                        [
                            'mls_id' => 'SMA-204',
                            'name' => 'Casa Valle',
                            'neighborhood' => 'Los Senderos, Valle de los',
                            'city' => 'San Miguel de Allende',
                        ],
                    ],
                    'last_page' => 1,
                ], 200);
            }

            return Http::response([
                'data' => [],
                'last_page' => 1,
            ], 200);
        }

        return Http::response([], 404);
    });

    Livewire::test(PropertiesSearch::class)
        ->set('neighborhood', 'Los Senderos, Valle de los')
        ->call('search')
        ->assertSee('Casa Valle');

    Http::assertSent(function (Request $request): bool {
        $query = $request->data();
        $selectedNeighborhoods = array_values(array_filter((array) ($query['neighborhood'] ?? [])));

        return str_starts_with($request->url(), 'https://ampisanmigueldeallende.com/api/v1/properties/search')
            && $selectedNeighborhoods === ['Los Senderos, Valle de los'];
    });
});

test('livewire property search reuses cached results for identical filters', function () {
    config()->set('services.ampi.api_key', 'test-api-key');
    config()->set('cache.default', 'array');
    Cache::flush();

    $searchRequestCount = 0;

    Http::fake(function (Request $request) use (&$searchRequestCount) {
        $query = $request->data();

        if ($request->url() === 'https://ampisanmigueldeallende.com/api/v1/neighborhoods') {
            return Http::response([
                ['name' => 'Centro'],
            ], 200);
        }

        if (str_starts_with($request->url(), 'https://ampisanmigueldeallende.com/api/v1/properties/search')) {
            if ((int) ($query['per_page'] ?? 0) === 100) {
                return Http::response([
                    'data' => [
                        ['neighborhood' => 'Centro'],
                    ],
                    'last_page' => 1,
                ], 200);
            }

            $searchRequestCount++;

            return Http::response([
                'data' => [
                    [
                        'mls_id' => 'SMA-303',
                        'name' => 'Casa Cache',
                        'neighborhood' => 'Centro',
                        'city' => 'San Miguel de Allende',
                    ],
                ],
                'last_page' => 1,
            ], 200);
        }

        return Http::response([], 404);
    });

    Livewire::test(PropertiesSearch::class)
        ->assertSee('Casa Cache')
        ->call('search')
        ->call('search')
        ->assertSee('Casa Cache');

    expect($searchRequestCount)->toBe(1);
});
