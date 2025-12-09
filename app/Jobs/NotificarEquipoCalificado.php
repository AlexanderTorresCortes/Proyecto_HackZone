<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use App\Models\Evaluacion;
use App\Mail\ProyectoCalificadoMail;
use Illuminate\Bus\Batchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NotificarEquipoCalificado implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public $evaluacion;
    public $tries = 3; // Intentar hasta 3 veces si falla
    public $timeout = 60; // Timeout de 60 segundos

    /**
     * Create a new job instance.
     */
    public function __construct(Evaluacion $evaluacion)
    {
        $this->evaluacion = $evaluacion;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Verificar que el job no ha sido cancelado
        if ($this->batch()?->cancelled()) {
            return;
        }

        // Obtener el equipo de la evaluación
        $equipo = $this->evaluacion->equipo;

        if (!$equipo) {
            \Log::warning("Evaluación {$this->evaluacion->id} no tiene equipo asociado");
            return;
        }

        // Obtener todos los miembros del equipo (líder + miembros)
        $miembros = collect();

        // Agregar el líder
        if ($equipo->lider) {
            $miembros->push($equipo->lider);
        }

        // Agregar los miembros
        if ($equipo->miembros) {
            $equipo->miembros->each(function($miembro) use ($miembros) {
                $miembros->push($miembro);
            });
        }

        // Remover duplicados (por si el líder también está en miembros)
        $miembros = $miembros->unique('id');

        \Log::info("Enviando notificaciones a {$miembros->count()} miembros del equipo {$equipo->nombre}");

        // Enviar correo a cada miembro del equipo
        foreach ($miembros as $miembro) {
            try {
                Mail::to($miembro->email)->send(new ProyectoCalificadoMail($this->evaluacion, $miembro));

                \Log::info("Correo enviado exitosamente a {$miembro->email} - Evaluación #{$this->evaluacion->id}");
            } catch (\Exception $e) {
                \Log::error("Error al enviar correo a {$miembro->email}: " . $e->getMessage());

                // Re-lanzar la excepción para que el job se reintente
                throw $e;
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error("Job NotificarEquipoCalificado falló después de {$this->tries} intentos - Evaluación #{$this->evaluacion->id}: " . $exception->getMessage());

        // Aquí podrías notificar al administrador del sistema
        // o guardar en una tabla de logs para revisión manual
    }
}
