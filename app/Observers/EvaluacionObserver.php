<?php

namespace App\Observers;

use App\Models\Evaluacion;
use App\Jobs\NotificarEquipoCalificado;

class EvaluacionObserver
{
    /**
     * Handle the Evaluacion "created" event.
     *
     * Este método se ejecuta automáticamente cuando un juez registra una nueva evaluación.
     * Dispara el Job para notificar a todos los miembros del equipo.
     */
    public function created(Evaluacion $evaluacion): void
    {
        // Verificar que la evaluación tiene un equipo asociado
        if ($evaluacion->equipo_id) {
            \Log::info("Nueva evaluación creada - ID: {$evaluacion->id}, Equipo ID: {$evaluacion->equipo_id}, Juez: {$evaluacion->juez_id}");

            // Despachar el Job a la cola para enviar notificaciones
            // Usamos dispatch() para que se ejecute en segundo plano
            NotificarEquipoCalificado::dispatch($evaluacion)
                ->onQueue('notifications') // Cola específica para notificaciones
                ->delay(now()->addSeconds(5)); // Pequeño delay para asegurar que la transacción se completó

            \Log::info("Job NotificarEquipoCalificado despachado para la evaluación #{$evaluacion->id}");
        } else {
            \Log::warning("Evaluación #{$evaluacion->id} creada sin equipo asociado. No se enviarán notificaciones.");
        }
    }

    /**
     * Handle the Evaluacion "updated" event.
     *
     * Opcionalmente, podrías notificar cuando una evaluación es actualizada
     */
    public function updated(Evaluacion $evaluacion): void
    {
        // Si quieres notificar cuando se actualiza una calificación, descomenta esto:
        /*
        if ($evaluacion->equipo_id && $evaluacion->wasChanged('puntuaciones')) {
            \Log::info("Evaluación actualizada - ID: {$evaluacion->id}");

            NotificarEquipoCalificado::dispatch($evaluacion)
                ->onQueue('notifications')
                ->delay(now()->addSeconds(5));
        }
        */
    }

    /**
     * Handle the Evaluacion "deleted" event.
     */
    public function deleted(Evaluacion $evaluacion): void
    {
        \Log::info("Evaluación eliminada - ID: {$evaluacion->id}");
    }

    /**
     * Handle the Evaluacion "restored" event.
     */
    public function restored(Evaluacion $evaluacion): void
    {
        //
    }

    /**
     * Handle the Evaluacion "force deleted" event.
     */
    public function forceDeleted(Evaluacion $evaluacion): void
    {
        //
    }
}
