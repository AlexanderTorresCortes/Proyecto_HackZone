<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CriterioEvaluacion extends Model
{
    use HasFactory;

    protected $table = 'criterios_evaluacion';

    protected $fillable = [
        'event_id',
        'nombre',
        'descripcion',
        'peso',
        'orden',
    ];

    protected $casts = [
        'peso' => 'integer',
        'orden' => 'integer',
    ];

    /**
     * Relación con el evento
     */
    public function evento()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Relación con las puntuaciones
     */
    public function puntuaciones()
    {
        return $this->hasMany(Puntuacion::class, 'criterio_id');
    }
}
