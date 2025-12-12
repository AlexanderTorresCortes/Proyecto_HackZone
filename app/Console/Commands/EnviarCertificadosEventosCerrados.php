<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Jobs\VerificarYEnviarCertificadosEvento;

class EnviarCertificadosEventosCerrados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eventos:enviar-certificados-cerrados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica eventos cerrados por fecha y envía certificados PDF a los ganadores del podio';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Verificando eventos cerrados por fecha...');
        
        // Obtener eventos que están cerrados por fecha (ya pasó la fecha de inicio)
        // pero que aún no tienen finalizado_at establecido (no fueron finalizados manualmente)
        $eventosCerrados = Event::where('fecha_inicio', '<', now())
            ->whereNull('finalizado_at')
            ->whereHas('equipos') // Solo eventos con equipos inscritos
            ->get();
        
        $this->info("Se encontraron {$eventosCerrados->count()} evento(s) cerrado(s) por fecha.");
        
        if ($eventosCerrados->isEmpty()) {
            $this->info('No hay eventos que procesar.');
            return 0;
        }
        
        $procesados = 0;
        $errores = 0;
        
        foreach ($eventosCerrados as $evento) {
            try {
                $this->info("Procesando evento: {$evento->titulo} (ID: {$evento->id})");
                
                // Verificar si hay ganadores
                $ganadores = $evento->obtenerGanadoresConEmpates();
                $hayGanadores = !empty($ganadores['primer_lugar']) || 
                               !empty($ganadores['segundo_lugar']) || 
                               !empty($ganadores['tercer_lugar']);
                
                if (!$hayGanadores) {
                    $this->warn("  El evento no tiene ganadores aún. Omitiendo...");
                    continue;
                }
                
                // Despachar job para verificar y enviar certificados
                VerificarYEnviarCertificadosEvento::dispatch($evento)
                    ->onQueue('notifications');
                
                $this->info("  Job despachado para enviar certificados.");
                $procesados++;
                
            } catch (\Exception $e) {
                $this->error("  Error procesando evento {$evento->id}: " . $e->getMessage());
                $errores++;
                \Log::error("Error en comando EnviarCertificadosEventosCerrados para evento {$evento->id}: " . $e->getMessage());
            }
        }
        
        $this->info("\nResumen:");
        $this->info("  Eventos procesados: {$procesados}");
        $this->info("  Errores: {$errores}");
        
        return 0;
    }
}

