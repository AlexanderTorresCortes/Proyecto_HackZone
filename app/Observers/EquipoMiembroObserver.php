<?php

namespace App\Observers;

use App\Models\EquipoMiembro;
use App\Models\Chat;
use Illuminate\Support\Facades\Log;

class EquipoMiembroObserver
{
    /**
     * Handle the EquipoMiembro "created" event.
     * Se ejecuta cuando un usuario se une a un equipo
     */
    public function created(EquipoMiembro $equipoMiembro): void
    {
        // Buscar el chat del equipo
        $chat = Chat::where('tipo', 'equipo')
            ->where('equipo_id', $equipoMiembro->equipo_id)
            ->first();

        // Si el chat no existe, crearlo
        if (!$chat) {
            $chat = Chat::crearChatEquipo($equipoMiembro->equipo);
            Log::info("Chat de equipo creado automáticamente para equipo ID: {$equipoMiembro->equipo_id}");
        } else {
            // Si el chat existe, agregar al nuevo miembro
            // Verificar que el usuario no esté ya en el chat
            if (!$chat->miembros()->where('user_id', $equipoMiembro->user_id)->exists()) {
                $chat->miembros()->attach($equipoMiembro->user_id);
                Log::info("Usuario {$equipoMiembro->user_id} agregado al chat del equipo {$equipoMiembro->equipo_id}");
            }
        }
    }

    /**
     * Handle the EquipoMiembro "deleted" event.
     * Se ejecuta cuando un usuario sale de un equipo
     */
    public function deleted(EquipoMiembro $equipoMiembro): void
    {
        // Buscar el chat del equipo
        $chat = Chat::where('tipo', 'equipo')
            ->where('equipo_id', $equipoMiembro->equipo_id)
            ->first();

        if ($chat) {
            // Remover al usuario del chat
            $chat->miembros()->detach($equipoMiembro->user_id);
            Log::info("Usuario {$equipoMiembro->user_id} removido del chat del equipo {$equipoMiembro->equipo_id}");
        }
    }
}