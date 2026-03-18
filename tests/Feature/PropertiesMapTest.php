<?php

use Illuminate\Support\Facades\Http;

test('properties map page renders api markers and detail links', function () {
    config()->set('services.ampi.api_key', 'test-api-key');

    Http::fake(function ($request) {
        $page = (int) $request->data()['page'];

        return Http::response(match ($page) {
            1 => [
                'data' => [
                    [
                        'mls_id' => 'SMA-100',
                        'name' => 'Casa Centro',
                        'latitude' => 20.9152,
                        'longitude' => -100.7439,
                        'price' => 450000,
                        'currency' => 'USD',
                        'status' => 'Active',
                        'city' => 'San Miguel de Allende',
                        'neighborhood' => 'Centro',
                        'bedrooms' => 3,
                        'bathrooms' => 2,
                        'construction_meters' => 240,
                        'featured_image' => 'https://example.com/casa-centro.jpg',
                        'description_short_es' => 'Casa lista para habitar en el centro.',
                    ],
                    [
                        'mls_id' => 'SMA-200',
                        'name' => 'Sin coordenadas',
                        'price' => 100000,
                    ],
                ],
                'meta' => [
                    'last_page' => 2,
                ],
            ],
            2 => [
                'data' => [
                    [
                        'mls_id' => 'SMA-300',
                        'name' => 'Villa Atascadero',
                        'latitude' => 20.9201,
                        'longitude' => -100.7301,
                        'price' => 780000,
                        'currency' => 'USD',
                        'status' => 'Active',
                        'city' => 'San Miguel de Allende',
                        'neighborhood' => 'Atascadero',
                        'bedrooms' => 4,
                        'bathrooms' => 4,
                        'construction_meters' => 410,
                        'featured_image' => 'https://example.com/villa-atascadero.jpg',
                        'description_short_es' => 'Vista panorámica con jardín.',
                    ],
                ],
                'meta' => [
                    'last_page' => 2,
                ],
            ],
            default => [
                'data' => [],
                'meta' => [
                    'last_page' => 2,
                ],
            ],
        }, 200);
    });

    $response = $this->get(route('properties.map', ['category' => 'Residencial']));

    $response->assertOk();
    $response->assertViewHas('properties', function (array $properties): bool {
        if (count($properties) !== 2) {
            return false;
        }

        return $properties[0]['detail_url'] === route('properties.show', ['mlsId' => 'SMA-100', 'slug' => 'casa-centro'])
            && $properties[1]['detail_url'] === route('properties.show', ['mlsId' => 'SMA-300', 'slug' => 'villa-atascadero']);
    });
    $response->assertSee('Mapa de propiedades MLS');
    $response->assertSee('Casa Centro');
    $response->assertSee('Villa Atascadero');
    $response->assertSee('2 propiedades con coordenadas');
    $response->assertDontSee('Casa lista para habitar en el centro.');
    $response->assertDontSee('Sin coordenadas');

    Http::assertSentCount(2);
});
