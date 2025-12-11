<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Equipo;
use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class CertificadoGanadorMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    
    public $timeout = 120; // 2 minutos para generar PDF

    public $usuario;
    public $equipo;
    public $evento;
    public $lugar;
    public $promedio;

    /**
     * Create a new message instance.
     */
    public function __construct(User $usuario, Equipo $equipo, Event $evento, int $lugar, float $promedio)
    {
        $this->usuario = $usuario;
        $this->equipo = $equipo;
        $this->evento = $evento;
        $this->lugar = $lugar;
        $this->promedio = $promedio;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $lugarTexto = $this->lugar == 1 ? 'Primer' : ($this->lugar == 2 ? 'Segundo' : 'Tercer');
        return new Envelope(
            subject: "🎉 Certificado de {$lugarTexto} Lugar - {$this->evento->titulo}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.certificado-ganador',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        // Generar PDF del certificado
        $pdf = PDF::loadView('certificados.ganador', [
            'usuario' => $this->usuario,
            'equipo' => $this->equipo,
            'evento' => $this->evento,
            'lugar' => $this->lugar,
            'promedio' => $this->promedio,
        ]);

        $lugarTexto = $this->lugar == 1 ? 'Primer' : ($this->lugar == 2 ? 'Segundo' : 'Tercer');
        $nombreArchivo = "Certificado_{$lugarTexto}_Lugar_{$this->evento->titulo}_{$this->usuario->name}.pdf";
        
        // Limpiar nombre de archivo de caracteres especiales
        $nombreArchivo = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $nombreArchivo);

        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(
                fn () => $pdf->output(),
                $nombreArchivo
            )->withMime('application/pdf'),
        ];
    }
}

