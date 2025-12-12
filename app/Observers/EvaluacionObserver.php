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
            
            // Si la evaluación fue creada como completada, verificar si se deben enviar certificados
            if ($evaluacion->estado === 'completada') {
                $evento = \App\Models\Event::find($evaluacion->event_id);
                
                if ($evento && $evento->estaFinalizado()) {
                    \Log::info("Evaluación creada como completada y evento está cerrado. Verificando certificados...");
                    
                    \App\Jobs\VerificarYEnviarCertificadosEvento::dispatch($evento)
                        ->onQueue('notifications')
                        ->delay(now()->addSeconds(10));
                }
            }
        } else {
            \Log::warning("Evaluación #{$evaluacion->id} creada sin equipo asociado. No se enviarán notificaciones.");
        }
    }

    /**
     * Handle the Evaluacion "updated" event.
     *
     * Verifica si una evaluación completada fue actualizada y si el evento está cerrado,
     * entonces verifica si se deben enviar certificados a los ganadores del podio.
     */
    public function updated(Evaluacion $evaluacion): void
    {
        // Si la evaluación cambió a estado "completada" y tiene un equipo asociado
        if ($evaluacion->equipo_id && 
            $evaluacion->wasChanged('estado') && 
            $evaluacion->estado === 'completada') {
            
            \Log::info("Evaluación completada actualizada - ID: {$evaluacion->id}, Evento ID: {$evaluacion->event_id}");
            
            // Cargar el evento
            $evento = \App\Models\Event::find($evaluacion->event_id);
            
            if ($evento) {
                // Verificar si el evento está cerrado (por fecha o finalizado manualmente)
                if ($evento->estaFinalizado()) {
                    \Log::info("El evento ID {$evento->id} está cerrado. Verificando si se deben enviar certificados...");
                    
                    // Despachar job para verificar y enviar certificados
                    // Usamos delay para asegurar que la transacción se completó
                    \App\Jobs\VerificarYEnviarCertificadosEvento::dispatch($evento)
                        ->onQueue('notifications')
                        ->delay(now()->addSeconds(10));
                    
                    \Log::info("Job VerificarYEnviarCertificadosEvento despachado para el evento #{$evento->id}");
                }
            }
        }
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
