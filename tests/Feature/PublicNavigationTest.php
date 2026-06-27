<?php

use App\Models\Agent;
use App\Models\MenuItem;

it('keeps desktop dropdowns visible while moving from the menu item to its submenu', function () {
    $parent = MenuItem::query()->create([
        'menu' => 'main',
        'label' => 'Servicios',
        'type' => 'url',
        'url' => '/servicios',
        'order' => 1,
    ]);

    MenuItem::query()->create([
        'menu' => 'main',
        'label' => 'Compra asistida',
        'type' => 'url',
        'url' => '/servicios/compra',
        'parent_id' => $parent->id,
        'order' => 2,
    ]);

    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('data-desktop-menu-item', false)
        ->assertSee('data-hover-bridge', false)
        ->assertSee('data-desktop-menu-panel', false)
        ->assertSee('absolute left-0 top-full z-40 min-w-[220px] pt-2', false)
        ->assertSee('group-hover:pointer-events-auto group-hover:opacity-100', false)
        ->assertSee('Compra asistida');

    expect(file_get_contents(resource_path('css/app.css')))
        ->toContain('[data-desktop-menu-item]:hover > [data-hover-bridge]')
        ->toContain('[data-desktop-menu-item]:focus-within > [data-hover-bridge]');
});

it('shows agents in the public menu and renders active agent profiles', function () {
    Agent::factory()->create([
        'name' => 'María González',
        'title' => 'Asesora inmobiliaria',
        'bio' => 'Especialista en propiedades patrimoniales.',
    ]);

    Agent::factory()->inactive()->create([
        'name' => 'Agente Inactivo',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Agentes')
        ->assertSee(route('agents.index', absolute: false));

    $this->get(route('agents.index'))
        ->assertOk()
        ->assertSee('María González')
        ->assertSee('Asesora inmobiliaria')
        ->assertSee('Especialista en propiedades patrimoniales.')
        ->assertDontSee('Agente Inactivo');
});
