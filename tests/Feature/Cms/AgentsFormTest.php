<?php

use App\Models\Agent;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

it('uploads a profile photo for an agent from the cms form', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('pages::cms.agents.form')
        ->set('name', 'Carlos Rivera')
        ->set('email', 'carlos@investsma.com')
        ->set('photoUpload', UploadedFile::fake()->image('carlos.jpg'))
        ->call('save')
        ->assertRedirect(route('cms.agents'));

    $agent = Agent::query()->first();

    expect($agent)
        ->not->toBeNull()
        ->photo_url->toContain('/storage/agents/');

    Storage::disk('public')->assertExists(str($agent->photo_url)->after('/storage/')->value());
});

it('rejects non image profile uploads', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('pages::cms.agents.form')
        ->set('name', 'Carlos Rivera')
        ->set('photoUpload', UploadedFile::fake()->create('notes.txt', 8, 'text/plain'))
        ->call('save')
        ->assertHasErrors(['photoUpload' => 'image']);

    expect(Agent::query()->count())->toBe(0);
});
