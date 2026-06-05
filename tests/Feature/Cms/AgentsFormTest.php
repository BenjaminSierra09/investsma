<?php

use App\Models\Agent;
use App\Models\User;
use Livewire\Livewire;

it('creates an agent from the cms form', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('pages::cms.agents.form')
        ->set('name', 'María González')
        ->set('title', 'Asesora inmobiliaria')
        ->set('email', 'maria@investsma.com')
        ->set('phone', '+52 415 123 4567')
        ->set('whatsapp', '524151234567')
        ->set('photo_url', 'https://example.com/maria.jpg')
        ->set('bio', 'Especialista en inversión inmobiliaria en San Miguel de Allende.')
        ->set('is_active', true)
        ->call('save')
        ->assertRedirect(route('cms.agents'));

    $agent = Agent::query()->first();

    expect($agent)
        ->not->toBeNull()
        ->name->toBe('María González')
        ->title->toBe('Asesora inmobiliaria')
        ->email->toBe('maria@investsma.com')
        ->is_active->toBeTrue();
});
