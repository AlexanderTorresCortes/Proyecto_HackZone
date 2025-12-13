<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Equipo;
use App\Models\Event;
use App\Mail\CertificadoGanadorMail;

class EnviarCertificadoGanador implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $usuario;
    public $equipo;
    public $evento;
    public $lugar;
    public $promedio;
    
    public $tries = 3;
    public $timeout = 120; // 2 minutos para generar PDF y enviar

    /**
     * Create a new job instance.
     */
    public function __construct(User $usuario, Equipo $equipo, Event $evento, int $lugar, float $promedio)
    {
        $this->usuario = $usuario;
        $this->equipo = $equipo;
        $this->evento = $evento;
        $this->lugar = $lugar;
        $this->promedio = $promedio;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            \Log::info("Enviando certificado a {$this->usuario->email} para lugar {$this->lugar}");
            
            Mail::to($this->usuario->email)
                ->send(new CertificadoGanadorMail($this->usuario, $this->equipo, $this->evento, $this->lugar, $this->promedio));
            
            \Log::info("Certificado enviado exitosamente a {$this->usuario->email}");
        } catch (\Exception $e) {
            \Log::error("Error enviando certificado a {$this->usuario->email}: " . $e->getMessage());
            throw $e; // Re-lanzar para que el job se reintente
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error("Job EnviarCertificadoGanador falló después de {$this->tries} intentos - Usuario: {$this->usuario->email}, Lugar: {$this->lugar}: " . $exception->getMessage());
    }
}

