<?php

use App\Models\User;

it('shows the users entry in the cms sidebar', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('cms.pages'));

    $response->assertOk();
    $response->assertSee(route('cms.users'), false);
    $response->assertSee('Usuarios');
});

it('lists users in the cms users page', function () {
    $admin = User::factory()->create([
        'name' => 'Admin Invest',
        'email' => 'admin@example.com',
    ]);

    $managedUser = User::factory()->create([
        'name' => 'Maria Lopez',
        'email' => 'maria@example.com',
    ]);

    $this->actingAs($admin);

    $response = $this->get(route('cms.users'));

    $response->assertOk();
    $response->assertSee('Administra los accesos al panel interno.');
    $response->assertSee($admin->name);
    $response->assertSee($managedUser->email);
});
