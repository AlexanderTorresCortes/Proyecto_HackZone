<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationsDropdown extends Component
{
    public $notifications;
    public $unreadCount;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = Auth::user()->notifications()->take(10)->get();
        $this->unreadCount = Auth::user()->unreadNotifications->count();
    }

    public function markAsRead($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            $this->loadNotifications();
        }
    }

    public function acceptRequest($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        if ($notification && $notification->data['type'] === 'equipo_solicitud') {
            $solicitudId = $notification->data['solicitud_id'];
            $solicitud = \App\Models\SolicitudEquipo::findOrFail($solicitudId);
            $equipo = $solicitud->equipo;

            // Verificar que el usuario actual es el líder
            if (!$equipo->esLider(Auth::id())) {
                $this->dispatch('error', 'No tienes permiso para realizar esta acción');
                return;
            }

            // Verificar que el equipo no esté lleno
            if ($equipo->estaLleno()) {
                $solicitud->update(['estado' => 'rechazada']);
                $this->dispatch('error', 'El equipo ya está completo');
                $this->loadNotifications();
                return;
            }

            // Agregar al usuario como miembro con el rol solicitado
            $miembro = \App\Models\EquipoMiembro::create([
                'equipo_id' => $equipo->id,
                'user_id' => $solicitud->user_id,
                'rol' => $solicitud->rol_solicitado ?? null
            ]);

            // Actualizar contador de miembros
            $equipo->increment('miembros_actuales');

            // Actualizar estado de la solicitud
            $solicitud->update(['estado' => 'aceptada']);

            // Notificar al solicitante que fue aceptado
            $solicitud->usuario->notify(new \App\Notifications\EquipoSolicitudRespuestaNotification($solicitud, true));

            // Enviar correo al solicitante
            try {
                \Illuminate\Support\Facades\Mail::to($solicitud->usuario->email)
                    ->send(new \App\Mail\SolicitudAceptadaEmail($solicitud));
            } catch (\Exception $e) {
                // Log error pero no fallar la aceptación
                \Log::error('Error enviando correo de solicitud aceptada: ' . $e->getMessage());
            }

            // Notificar al líder que alguien se unió
            $equipo->lider->notify(new \App\Notifications\MiembroUnidoEquipoNotification($miembro));

            // Marcar la notificación como leída
            Auth::user()->notifications()
                ->where('data->solicitud_id', $solicitud->id)
                ->where('data->type', 'equipo_solicitud')
                ->update(['read_at' => now()]);

            $this->loadNotifications();
            $this->dispatch('notification-updated');
            $this->dispatch('success', 'Solicitud aceptada exitosamente');
        }
    }

    public function rejectRequest($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        if ($notification && $notification->data['type'] === 'equipo_solicitud') {
            $solicitudId = $notification->data['solicitud_id'];
            $solicitud = \App\Models\SolicitudEquipo::findOrFail($solicitudId);
            $equipo = $solicitud->equipo;

            // Verificar que el usuario actual es el líder
            if (!$equipo->esLider(Auth::id())) {
                $this->dispatch('error', 'No tienes permiso para realizar esta acción');
                return;
            }

            // Actualizar estado de la solicitud
            $solicitud->update(['estado' => 'rechazada']);

            // Notificar al solicitante que fue rechazado
            $solicitud->usuario->notify(new \App\Notifications\EquipoSolicitudRespuestaNotification($solicitud, false));

            // Marcar la notificación como leída
            Auth::user()->notifications()
                ->where('data->solicitud_id', $solicitud->id)
                ->where('data->type', 'equipo_solicitud')
                ->update(['read_at' => now()]);

            $this->loadNotifications();
            $this->dispatch('notification-updated');
            $this->dispatch('success', 'Solicitud rechazada');
        }
    }

    public function acceptInvitation($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        if ($notification && $notification->data['type'] === 'equipo_invitacion') {
            $invitacionId = $notification->data['invitacion_id'];
            $invitacion = \App\Models\InvitacionEquipo::findOrFail($invitacionId);
            $equipo = $invitacion->equipo;

            // Verificar que el usuario actual es el invitado
            if ($invitacion->user_id !== Auth::id()) {
                $this->dispatch('error', 'No tienes permiso para realizar esta acción');
                return;
            }

            // Verificar que la invitación está pendiente
            if (!$invitacion->estaPendiente()) {
                $this->dispatch('error', 'Esta invitación ya ha sido procesada');
                return;
            }

            // Verificar que el equipo no esté lleno
            if ($equipo->estaLleno()) {
                $invitacion->update(['estado' => 'rechazada']);
                $this->dispatch('error', 'El equipo ya está completo');
                $this->loadNotifications();
                return;
            }

            // Agregar al usuario como miembro (el líder asignará el rol después)
            $miembro = \App\Models\EquipoMiembro::create([
                'equipo_id' => $equipo->id,
                'user_id' => Auth::id(),
                'rol' => null // El líder asignará el rol desde la gestión del equipo
            ]);

            // Actualizar contador de miembros
            $equipo->increment('miembros_actuales');

            // Actualizar estado de la invitación
            $invitacion->update(['estado' => 'aceptada']);

            // Notificar al líder que alguien se unió
            $equipo->lider->notify(new \App\Notifications\MiembroUnidoEquipoNotification($miembro));

            // Marcar la notificación como leída
            Auth::user()->notifications()
                ->where('data->invitacion_id', $invitacion->id)
                ->where('data->type', 'equipo_invitacion')
                ->update(['read_at' => now()]);

            $this->loadNotifications();
            $this->dispatch('notification-updated');
            $this->dispatch('success', 'Invitación aceptada. ¡Bienvenido al equipo!');
        }
    }

    public function rejectInvitation($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        if ($notification && $notification->data['type'] === 'equipo_invitacion') {
            $invitacionId = $notification->data['invitacion_id'];
            $invitacion = \App\Models\InvitacionEquipo::findOrFail($invitacionId);

            // Verificar que el usuario actual es el invitado
            if ($invitacion->user_id !== Auth::id()) {
                $this->dispatch('error', 'No tienes permiso para realizar esta acción');
                return;
            }

            // Actualizar estado de la invitación
            $invitacion->update(['estado' => 'rechazada']);

            // Marcar la notificación como leída
            Auth::user()->notifications()
                ->where('data->invitacion_id', $invitacion->id)
                ->where('data->type', 'equipo_invitacion')
                ->update(['read_at' => now()]);

            $this->loadNotifications();
            $this->dispatch('notification-updated');
            $this->dispatch('success', 'Invitación rechazada');
        }
    }

    public function render()
    {
        return view('livewire.notifications-dropdown');
    }
}
