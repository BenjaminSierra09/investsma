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

it('keeps child parent relationship when sorting siblings', function () {
    $parent = MenuItem::query()->create([
        'menu' => 'main',
        'label' => 'Padre',
        'type' => 'url',
        'url' => 'https://example.com/padre',
        'order' => 0,
    ]);

    MenuItem::query()->create([
        'menu' => 'main',
        'label' => 'Hijo',
        'type' => 'url',
        'url' => 'https://example.com/hijo',
        'parent_id' => $parent->id,
        'order' => 1,
    ]);

    MenuItem::query()->create([
        'menu' => 'main',
        'label' => 'Otro',
        'type' => 'url',
        'url' => 'https://example.com/otro',
        'order' => 2,
    ]);

    $component = Livewire::test('pages::cms.menus.form');
    $items = collect($component->get('items'));

    $parentTempId = (string) $items->firstWhere('label', 'Padre')['temp_id'];
    $childTempId = (string) $items->firstWhere('label', 'Hijo')['temp_id'];
    $otherTempId = (string) $items->firstWhere('label', 'Otro')['temp_id'];

    expect((string) $items->firstWhere('label', 'Hijo')['parent_temp_id'])
        ->toBe($parentTempId);

    $component->call('sortItem', $otherTempId, 0);

    $itemsAfterSort = collect($component->get('items'));

    expect((string) $itemsAfterSort->firstWhere('temp_id', $childTempId)['parent_temp_id'])
        ->toBe($parentTempId);

    $component->call('save');

    $savedParent = MenuItem::query()->where('label', 'Padre')->firstOrFail();
    $savedChild = MenuItem::query()->where('label', 'Hijo')->firstOrFail();

    expect($savedChild->parent_id)->toBe($savedParent->id);
});

it('updates only the selected item label after sorting', function () {
    MenuItem::query()->create([
        'menu' => 'main',
        'label' => 'Uno',
        'type' => 'url',
        'url' => 'https://example.com/uno',
        'order' => 0,
    ]);

    MenuItem::query()->create([
        'menu' => 'main',
        'label' => 'Dos',
        'type' => 'url',
        'url' => 'https://example.com/dos',
        'order' => 1,
    ]);

    $component = Livewire::test('pages::cms.menus.form');
    $items = collect($component->get('items'));

    $unoTempId = (string) $items->firstWhere('label', 'Uno')['temp_id'];
    $dosTempId = (string) $items->firstWhere('label', 'Dos')['temp_id'];

    $component->call('sortItem', $dosTempId, 0);
    $component->call('updateItemLabel', $unoTempId, 'Uno editado');

    $itemsAfterEdit = collect($component->get('items'))->keyBy('temp_id');

    expect($itemsAfterEdit[$unoTempId]['label'])->toBe('Uno editado')
        ->and($itemsAfterEdit[$dosTempId]['label'])->toBe('Dos');
});
