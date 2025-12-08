<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Puntuacion extends Model
{
    use HasFactory;

    protected $table = 'puntuaciones';

    protected $fillable = [
        'evaluacion_id',
        'criterio_id',
        'puntuacion',
    ];

    protected $casts = [
        'puntuacion' => 'integer',
    ];

    /**
     * Relación con la evaluación
     */
    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class, 'evaluacion_id');
    }

    /**
     * Relación con el criterio
     */
    public function criterio()
    {
        return $this->belongsTo(CriterioEvaluacion::class, 'criterio_id');
    }
}
