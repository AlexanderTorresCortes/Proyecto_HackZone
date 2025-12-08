<?php

namespace App\Notifications;

use App\Models\InvitacionEquipo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EquipoInvitacionNotification extends Notification
{
    use Queueable;

    public $invitacion;

    /**
     * Create a new notification instance.
     */
    public function __construct(InvitacionEquipo $invitacion)
    {
        $this->invitacion = $invitacion;
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
            'type' => 'equipo_invitacion',
            'invitacion_id' => $this->invitacion->id,
            'equipo_id' => $this->invitacion->equipo_id,
            'equipo_nombre' => $this->invitacion->equipo->nombre,
            'invitador_id' => $this->invitacion->invitado_por,
            'invitador_nombre' => $this->invitacion->invitador->name,
            'invitador_avatar' => $this->invitacion->invitador->avatar,
            'mensaje' => $this->invitacion->mensaje,
            'message' => $this->invitacion->invitador->name . ' te ha invitado a unirte al equipo "' . $this->invitacion->equipo->nombre . '"',
        ];
    }
}
