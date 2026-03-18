<?php

use App\Mail\ListingInquiryMail;
use App\Models\Listing;
use Illuminate\Support\Facades\Mail;

it('shows only published listings on the public index', function () {
    $publishedListing = Listing::factory()->create([
        'title' => 'Casa publicada',
        'listing_type' => 'sale',
    ]);

    Listing::factory()->draft()->create([
        'title' => 'Casa borrador',
    ]);

    $response = $this->get(route('listings.index'));

    $response
        ->assertOk()
        ->assertSee('Casa publicada')
        ->assertSee('Venta')
        ->assertDontSee('Casa borrador');

    expect($publishedListing->slug)->not->toBeEmpty();
});

it('shows a published listing and sends inquiry emails', function () {
    Mail::fake();

    $listing = Listing::factory()->create([
        'title' => 'Casa del Centro',
        'listing_type' => 'rent',
        'contact_email' => 'ventas@example.com',
    ]);

    $this->get(route('listings.show', $listing))
        ->assertOk()
        ->assertSee('Casa del Centro')
        ->assertSee('Renta')
        ->assertSee('Solicita más información');

    $response = $this->post(route('listings.inquire', $listing), [
        'nombre' => 'María López',
        'email' => 'maria@example.com',
        'telefono' => '+52 415 000 0000',
        'mensaje' => 'Quiero agendar una visita.',
    ]);

    $response
        ->assertRedirect()
        ->assertSessionHas('listing_inquiry_status');

    Mail::assertSent(ListingInquiryMail::class, function (ListingInquiryMail $mail) use ($listing) {
        return $mail->listing->is($listing)
            && $mail->data['email'] === 'maria@example.com';
    });
});
