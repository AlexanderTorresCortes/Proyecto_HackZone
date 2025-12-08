<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    use HasFactory;

    protected $table = 'evaluaciones';

    protected $fillable = [
        'event_id',
        'equipo_id',
        'juez_id',
        'comentarios',
        'estado',
    ];

    protected $casts = [
        'estado' => 'string',
    ];

    /**
     * Relación con el evento
     */
    public function evento()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Relación con el equipo evaluado
     */
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }

    /**
     * Relación con el juez que evalúa
     */
    public function juez()
    {
        return $this->belongsTo(User::class, 'juez_id');
    }

    /**
     * Relación con las puntuaciones individuales
     */
    public function puntuaciones()
    {
        return $this->hasMany(Puntuacion::class, 'evaluacion_id');
    }

    /**
     * Calcular puntuación total de la evaluación (sin ponderar)
     */
    public function calcularPuntuacionTotal()
    {
        return $this->puntuaciones()->sum('puntuacion');
    }

    /**
     * Calcular puntuación promedio simple (sin peso)
     */
    public function calcularPromedioSimple()
    {
        $total = $this->puntuaciones()->count();
        if ($total === 0) return 0;

        return $this->calcularPuntuacionTotal() / $total;
    }

    /**
     * Calcular puntuación ponderada según peso de criterios
     * Fórmula: Σ(puntuación × peso) / Σ(peso)
     */
    public function calcularPromedio()
    {
        $puntuaciones = $this->puntuaciones()->with('criterio')->get();

        if ($puntuaciones->isEmpty()) return 0;

        $sumaPonderada = 0;
        $sumaPesos = 0;

        foreach ($puntuaciones as $puntuacion) {
            $peso = $puntuacion->criterio ? $puntuacion->criterio->peso : 1;
            $sumaPonderada += $puntuacion->puntuacion * $peso;
            $sumaPesos += $peso;
        }

        if ($sumaPesos === 0) return 0;

        return round($sumaPonderada / $sumaPesos, 2);
    }

    /**
     * Verificar si la evaluación está completada
     */
    public function estaCompletada()
    {
        return $this->estado === 'completada';
    }
}
