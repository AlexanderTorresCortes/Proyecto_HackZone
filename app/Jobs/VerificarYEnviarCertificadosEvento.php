<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Event;

class VerificarYEnviarCertificadosEvento implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $evento;
    
    public $tries = 3;
    public $timeout = 300; // 5 minutos para procesar todos los certificados

    /**
     * Create a new job instance.
     */
    public function __construct(Event $evento)
    {
        $this->evento = $evento;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            \Log::info("Verificando si se deben enviar certificados para el evento ID: {$this->evento->id}");
            
            // Verificar que el evento esté cerrado (por fecha o finalizado manualmente)
            if (!$this->evento->estaFinalizado()) {
                \Log::info("El evento ID {$this->evento->id} aún no está cerrado. No se enviarán certificados.");
                return;
            }
            
            // Verificar si ya hay certificados guardados para este evento (evitar duplicados)
            $certificadosExistentes = \App\Models\Certificado::where('event_id', $this->evento->id)->count();
            
            if ($certificadosExistentes > 0) {
                \Log::info("Ya existen certificados guardados para el evento ID {$this->evento->id}. Verificando si se deben enviar...");
                
                // Obtener ganadores del podio
                $ganadores = $this->evento->obtenerGanadoresConEmpates();
                $hayGanadores = !empty($ganadores['primer_lugar']) || 
                               !empty($ganadores['segundo_lugar']) || 
                               !empty($ganadores['tercer_lugar']);
                
                if ($hayGanadores) {
                    // Verificar si todos los miembros de los equipos ganadores tienen certificados
                    $todosTienenCertificados = true;
                    
                    foreach (['primer_lugar', 'segundo_lugar', 'tercer_lugar'] as $lugarKey) {
                        $lugar = $lugarKey === 'primer_lugar' ? 1 : ($lugarKey === 'segundo_lugar' ? 2 : 3);
                        
                        if (!empty($ganadores[$lugarKey])) {
                            foreach ($ganadores[$lugarKey] as $ganador) {
                                $equipo = $ganador['equipo'];
                                $equipo->load(['lider', 'miembros.usuario']);
                                $miembros = $equipo->todosLosMiembros();
                                
                                foreach ($miembros as $miembro) {
                                    $certificado = \App\Models\Certificado::where('user_id', $miembro->id)
                                        ->where('equipo_id', $equipo->id)
                                        ->where('event_id', $this->evento->id)
                                        ->where('lugar', $lugar)
                                        ->first();
                                    
                                    if (!$certificado) {
                                        $todosTienenCertificados = false;
                                        break 3; // Salir de los tres loops
                                    }
                                }
                            }
                        }
                    }
                    
                    if ($todosTienenCertificados) {
                        \Log::info("Todos los miembros de los equipos ganadores ya tienen certificados. No se enviarán correos duplicados.");
                        return;
                    }
                }
            }
            
            // Si llegamos aquí, necesitamos enviar certificados
            \Log::info("Enviando certificados para el evento ID: {$this->evento->id}");
            
            // Primero, guardar los certificados en la BD (si no existen)
            $this->guardarCertificados();
            
            // Luego, enviar los certificados por correo
            $resultado = $this->evento->enviarCertificadosGanadores();
            
            \Log::info("Certificados enviados para evento ID {$this->evento->id}:", [
                'enviados' => $resultado['certificados_enviados'],
                'omitidos' => $resultado['certificados_omitidos'],
                'errores' => $resultado['total_errores']
            ]);
            
        } catch (\Exception $e) {
            \Log::error("Error verificando/enviando certificados para evento ID {$this->evento->id}: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            throw $e; // Re-lanzar para que el job se reintente
        }
    }
    
    /**
     * Guardar certificados en la BD si no existen
     */
    private function guardarCertificados(): void
    {
        $evento = $this->evento; // Capturar referencia para usar en closure
        $ganadores = $evento->obtenerGanadoresConEmpates();
        
        $guardarCertificados = function($ganadores, $lugar) use ($evento) {
            foreach ($ganadores as $ganador) {
                $equipo = $ganador['equipo'];
                $promedio = $ganador['promedio'];
                
                $equipo->load(['lider', 'miembros.usuario']);
                $miembros = $equipo->todosLosMiembros();
                
                foreach ($miembros as $miembro) {
                    if (!$miembro) continue;
                    
                    // Verificar si el certificado ya existe
                    $certificadoExistente = \App\Models\Certificado::where('user_id', $miembro->id)
                        ->where('equipo_id', $equipo->id)
                        ->where('event_id', $evento->id)
                        ->where('lugar', $lugar)
                        ->first();
                    
                    if (!$certificadoExistente) {
                        \App\Models\Certificado::create([
                            'user_id' => $miembro->id,
                            'equipo_id' => $equipo->id,
                            'event_id' => $evento->id,
                            'lugar' => $lugar,
                            'promedio' => $promedio,
                        ]);
                    }
                }
            }
        };
        
        if (!empty($ganadores['primer_lugar'])) {
            $guardarCertificados($ganadores['primer_lugar'], 1);
        }
        
        if (!empty($ganadores['segundo_lugar'])) {
            $guardarCertificados($ganadores['segundo_lugar'], 2);
        }
        
        if (!empty($ganadores['tercer_lugar'])) {
            $guardarCertificados($ganadores['tercer_lugar'], 3);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error("Job VerificarYEnviarCertificadosEvento falló después de {$this->tries} intentos - Evento ID: {$this->evento->id}: " . $exception->getMessage());
    }
}

