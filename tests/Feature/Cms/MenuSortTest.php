<?php

use App\Models\MenuItem;
use Livewire\Livewire;

it('reorders menu items using the sort handler and persists order', function () {
    MenuItem::query()->create([
        'menu' => 'main',
        'label' => 'Primero',
        'type' => 'url',
        'url' => 'https://example.com/uno',
        'order' => 0,
    ]);

    MenuItem::query()->create([
        'menu' => 'main',
        'label' => 'Segundo',
        'type' => 'url',
        'url' => 'https://example.com/dos',
        'order' => 1,
    ]);

    $component = Livewire::test('pages::cms.menus.form');

    $firstTempId = $component->get('items.0.temp_id');

    $component
        ->call('sortItem', $firstTempId, 1)
        ->assertSet('items.0.label', 'Segundo')
        ->assertSet('items.1.label', 'Primero')
        ->call('save');

    expect(MenuItem::forMenu('main')->pluck('label')->all())
        ->toBe(['Segundo', 'Primero']);
});
