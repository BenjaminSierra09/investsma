<?php

use App\Support\AmpiPropertyApi;
use Mockery\MockInterface;

beforeEach(function () {
    $this->mock(AmpiPropertyApi::class, function (MockInterface $mock): void {
        $mock->shouldReceive('isConfigured')->andReturnTrue();
        $mock->shouldReceive('hasLocalProperties')->andReturnFalse();
        $mock->shouldReceive('fetchNeighborhoodOptions')->andReturn(['Centro', 'Guadiana']);
        $mock->shouldReceive('search')->andReturn([
            'data' => [
                [
                    'id' => 'SMA-100',
                    'mls_id' => 'SMA-100',
                    'name' => 'Casa Centro',
                    'city' => 'San Miguel de Allende',
                    'neighborhood' => 'Centro',
                    'price' => 750000,
                    'currency' => 'USD',
                    'featured_image' => 'https://picsum.photos/seed/test-home-property/1200/900',
                ],
            ],
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 12,
                'total' => 1,
                'from' => 1,
                'to' => 1,
            ],
        ]);
    });
});

it('shows a compact property filter bar on the home page', function () {
    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('Encuentra propiedad con mejor criterio local.')
        ->assertSee('home-filter-bar', false)
        ->assertSee('name="keywords"', false)
        ->assertSee('name="neighborhood"', false)
        ->assertSee('name="category"', false)
        ->assertSee('name="price_max"', false)
        ->assertSee('quick-filter-chip', false);
});

it('renders compact primary filters and a secondary drawer on the properties page', function () {
    $response = $this->get(route('properties.index'));

    $response
        ->assertOk()
        ->assertSee('Inventario activo, filtros más ligeros.')
        ->assertSee('compact-filter-panel', false)
        ->assertSee('advanced-filter-drawer', false)
        ->assertSee('Refinar búsqueda')
        ->assertSee('wire:model.defer="price_min"', false)
        ->assertSee('wire:model.defer="price_max"', false)
        ->assertSee('data-livewire-model="status"', false);
});
