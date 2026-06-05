<?php

use App\Models\Agent;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

it('creates a listing from the cms form', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $agent = Agent::factory()->create();

    $this->actingAs($user);

    Livewire::test('pages::cms.listings.form')
        ->set('title', 'Casa Magnolia')
        ->set('slug', 'casa-magnolia')
        ->set('status', 'published')
        ->set('listing_type', 'rent')
        ->set('currency', 'USD')
        ->set('price', '450000')
        ->set('agent_id', $agent->id)
        ->set('location', 'Guadiana, San Miguel de Allende')
        ->set('summary', 'Casa lista para habitar.')
        ->set('description', 'Descripción amplia de la propiedad.')
        ->set('bedrooms', 3)
        ->set('bathrooms', 3)
        ->set('construction_m2', 240)
        ->set('lot_m2', 310)
        ->set('photoUploads', [
            UploadedFile::fake()->image('cover.jpg'),
            UploadedFile::fake()->image('gallery-2.jpg'),
        ])
        ->set('contact_email', 'info@investsma.com')
        ->call('save')
        ->assertRedirect(route('cms.listings'));

    $listing = Listing::query()->first();

    expect($listing)
        ->not->toBeNull()
        ->title->toBe('Casa Magnolia')
        ->slug->toBe('casa-magnolia')
        ->status->toBe('published')
        ->listing_type->toBe('rent')
        ->agent_id->toBe($agent->id);

    expect($listing->gallery)->toHaveCount(2);
    expect($listing->cover_image)->toBe($listing->gallery[0]);

    foreach ($listing->gallery as $image) {
        Storage::disk('public')->assertExists(str($image)->after('/storage/')->value());
    }
});
