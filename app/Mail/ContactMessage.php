<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function build(): self
    {
        return $this
            ->subject('Nuevo contacto en investsma')
            ->replyTo($this->data['email'], $this->data['nombre'] ?? null)
            ->view('emails.contact', ['data' => $this->data]);
    }
}
