<?php

namespace App\Mail;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ListingInquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Listing $listing,
        public array $data,
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Nuevo contacto sobre '.$this->listing->title)
            ->replyTo($this->data['email'], $this->data['nombre'] ?? null)
            ->view('emails.listing-inquiry', [
                'listing' => $this->listing,
                'data' => $this->data,
            ]);
    }
}
