<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Entrega;
use App\Models\User;

class TrabajoSubidoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $entrega;
    public $juez;
    public $equipo;
    public $evento;

    /**
     * Create a new message instance.
     */
    public function __construct(Entrega $entrega, User $juez)
    {
        $this->entrega = $entrega;
        $this->juez = $juez;
        $this->equipo = $entrega->equipo;
        $this->evento = $entrega->evento;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva Entrega de Trabajo - ' . $this->equipo->nombre . ' - HackZone',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.trabajo-subido',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
