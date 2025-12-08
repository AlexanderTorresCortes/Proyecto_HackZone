<?php

namespace App\Notifications;

use App\Models\SolicitudEquipo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EquipoSolicitudNotification extends Notification
{
    use Queueable;

    public $solicitud;

    /**
     * Create a new notification instance.
     */
    public function __construct(SolicitudEquipo $solicitud)
    {
        $this->solicitud = $solicitud;
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
        return [
            'type' => 'equipo_solicitud',
            'solicitud_id' => $this->solicitud->id,
            'equipo_id' => $this->solicitud->equipo_id,
            'equipo_nombre' => $this->solicitud->equipo->nombre,
            'solicitante_id' => $this->solicitud->user_id,
            'solicitante_nombre' => $this->solicitud->usuario->name,
            'solicitante_username' => $this->solicitud->usuario->username ?? $this->solicitud->usuario->email,
            'solicitante_avatar' => $this->solicitud->usuario->avatar,
            'mensaje' => $this->solicitud->mensaje,
            'rol_solicitado' => $this->solicitud->rol_solicitado,
            'message' => $this->solicitud->usuario->name . ' quiere unirse a tu equipo "' . $this->solicitud->equipo->nombre . '"' . ($this->solicitud->rol_solicitado ? ' como ' . $this->solicitud->rol_solicitado : ''),
        ];
    }
}
