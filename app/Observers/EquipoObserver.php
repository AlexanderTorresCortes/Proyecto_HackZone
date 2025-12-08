<?php

namespace App\Observers;

use App\Models\Equipo;
use App\Models\Chat;
use Illuminate\Support\Facades\Log;

class EquipoObserver
{
    /**
     * Handle the Equipo "created" event.
     * Se ejecuta cuando se crea un nuevo equipo
     */
    public function created(Equipo $equipo): void
    {
        // Crear el chat del equipo automáticamente
        $chat = Chat::crearChatEquipo($equipo);
        Log::info("Chat creado automáticamente para el equipo: {$equipo->nombre} (ID: {$equipo->id})");
    }

    /**
     * Handle the Equipo "deleted" event.
     * Se ejecuta cuando se elimina un equipo
     */
    public function deleted(Equipo $equipo): void
    {
        // Eliminar el chat del equipo (se hace automáticamente por el onDelete cascade)
        Log::info("Equipo eliminado: {$equipo->nombre} (ID: {$equipo->id})");
    }
}