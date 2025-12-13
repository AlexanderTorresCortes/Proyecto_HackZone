<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Evaluacion;
use App\Models\User;

class ProyectoCalificadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $evaluacion;
    public $miembro;
    public $equipo;
    public $evento;
    public $juez;
    public $puntuacionTotal;

    /**
     * Create a new message instance.
     */
    public function __construct(Evaluacion $evaluacion, User $miembro)
    {
        $this->evaluacion = $evaluacion;
        $this->miembro = $miembro;
        $this->equipo = $evaluacion->equipo;
        $this->evento = $evaluacion->evento;
        $this->juez = $evaluacion->juez;

        // Calcular puntuación total
        $this->puntuacionTotal = $evaluacion->calcularPuntuacionTotal();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Tu proyecto ha sido calificado! - ' . $this->evento->titulo,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.proyecto-calificado',
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
