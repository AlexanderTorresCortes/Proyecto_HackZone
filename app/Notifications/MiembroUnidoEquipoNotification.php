<?php

namespace App\Notifications;

use App\Models\EquipoMiembro;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MiembroUnidoEquipoNotification extends Notification
{
    use Queueable;

    public $miembro;

    /**
     * Create a new notification instance.
     */
    public function __construct(EquipoMiembro $miembro)
    {
        $this->miembro = $miembro;
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
            'type' => 'miembro_unido_equipo',
            'equipo_id' => $this->miembro->equipo_id,
            'equipo_nombre' => $this->miembro->equipo->nombre,
            'miembro_id' => $this->miembro->user_id,
            'miembro_nombre' => $this->miembro->usuario->name,
            'miembro_avatar' => $this->miembro->usuario->avatar,
            'rol' => $this->miembro->rol,
            'message' => $this->miembro->usuario->name . ' se ha unido a tu equipo "' . $this->miembro->equipo->nombre . '"' . ($this->miembro->rol ? ' como ' . $this->miembro->rol : ''),
        ];
    }
}
