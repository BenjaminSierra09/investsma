<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('cms.pages'));
});

test('dashboard cms routes use the dashboard prefix', function () {
    expect(route('cms.pages', absolute: false))->toBe('/dashboard/paginas');
});

test('dashboard navigation includes agents', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('cms.pages'))
        ->assertOk()
        ->assertSee('Agentes')
        ->assertSee(route('cms.agents', absolute: false));
});

test('old cms page routes redirect to the dashboard prefix', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/cms/paginas');
    $response->assertRedirect('/dashboard/paginas');
});
