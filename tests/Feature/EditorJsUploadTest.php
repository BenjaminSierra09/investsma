<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('renders a csrf meta tag on the cms page form', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('cms.pages.form'))
        ->assertOk()
        ->assertSee('meta name="csrf-token"', false);
});

it('stores uploaded editor images for authenticated cms users', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post('/upload/image', [
            'image' => UploadedFile::fake()->image('editor-photo.jpg'),
        ]);

    $response->assertOk()
        ->assertJsonPath('success', 1);

    $storedUrl = $response->json('file.url');

    expect($storedUrl)->toBeString();

    Storage::disk('public')->assertExists(str($storedUrl)->after('/storage/')->value());
});
