<?php

use Illuminate\Support\Facades\Http;

test('home page shows clickable property image and title', function () {
    config()->set('services.ampi.api_key', 'test-api-key');

    Http::fake([
        'https://ampisanmigueldeallende.com/api/v1/properties/search*' => Http::response([
            'data' => [
                [
                    'mls_id' => 'SMA-123',
                    'name' => 'Casa Clickable',
                    'featured_image' => 'https://example.com/casa-clickable.jpg',
                    'price' => 550000,
                    'currency' => 'USD',
                ],
            ],
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
            ],
        ], 200),
        'https://ampisanmigueldeallende.com/api/v1/neighborhoods' => Http::response([], 200),
    ]);

    $response = $this->get(route('home'));

    $detailUrl = route('properties.show', ['mlsId' => 'SMA-123', 'slug' => 'casa-clickable']);

    $response
        ->assertOk()
        ->assertSee('Casa Clickable')
        ->assertSeeHtml('href="'.$detailUrl.'"')
        ->assertSeeHtml('aria-label="See details for Casa Clickable"');
});
