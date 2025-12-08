<?php

namespace App\Notifications;

use App\Models\SolicitudEquipo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EquipoSolicitudRespuestaNotification extends Notification
{
    use Queueable;

    public $solicitud;
    public $aceptada;

    /**
     * Create a new notification instance.
     */
    public function __construct(SolicitudEquipo $solicitud, bool $aceptada)
    {
        $this->solicitud = $solicitud;
        $this->aceptada = $aceptada;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if ($this->aceptada) {
            $message = '¡Felicidades! Tu solicitud para unirte al equipo "' . $this->solicitud->equipo->nombre . '" ha sido aceptada.';
        } else {
            $message = 'Lamentablemente, tu solicitud para unirte al equipo "' . $this->solicitud->equipo->nombre . '" no fue aceptada.';
        }

        return [
            'type' => 'equipo_solicitud_respuesta',
            'solicitud_id' => $this->solicitud->id,
            'equipo_id' => $this->solicitud->equipo_id,
            'equipo_nombre' => $this->solicitud->equipo->nombre,
            'aceptada' => $this->aceptada,
            'message' => $message,
        ];
    }
}
