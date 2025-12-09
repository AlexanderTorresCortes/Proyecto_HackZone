<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\SolicitudEquipo;

class SolicitudAceptadaEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $solicitud;

    /**
     * Create a new message instance.
     */
    public function __construct(SolicitudEquipo $solicitud)
    {
        $this->solicitud = $solicitud;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Tu solicitud ha sido aceptada! - HackZone',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.solicitud-aceptada',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
