<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'titulo',
        'organizacion',
        'org_icon',
        'imagen',
        'descripcion_corta',
        'descripcion_larga',
        'fecha_inicio',
        'fecha_limite_inscripcion',
        'ubicacion',
        'participantes_max',
        'participantes_actuales',
        'requisitos',
        'premios',
        'cronograma',
        'jueces',
    ];

    /**
     * Campos que deben ser tratados como tipos especiales
     */
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_limite_inscripcion' => 'date',
        'requisitos' => 'array',
        'premios' => 'array',
        'cronograma' => 'array',
        'jueces' => 'array',
    ];

    /**
     * Obtener el número de participantes formateado
     */
    public function getParticipantesAttribute()
    {
        return $this->participantes_actuales . '/' . $this->participantes_max;
    }

    /**
     * Verificar si el evento está lleno
     */
    public function estaLleno()
    {
        return $this->participantes_actuales >= $this->participantes_max;
    }

    /**
     * Obtener el porcentaje de ocupación
     */
    public function porcentajeOcupacion()
    {
        if ($this->participantes_max == 0) return 0;
        return round(($this->participantes_actuales / $this->participantes_max) * 100);
    }

    /**
     * Verificar si las inscripciones están abiertas
     */
    public function inscripcionesAbiertas()
    {
        return now() <= $this->fecha_limite_inscripcion && !$this->estaLleno();
    }

    /**
     * Obtener la URL de la imagen
     */
    public function getImagenUrlAttribute()
    {
        if ($this->imagen) {
            return asset('storage/' . $this->imagen);
        }
        return asset('images/eventos/default.jpg');
    }

    /**
     * Obtener días restantes hasta la fecha límite de inscripción
     */
    public function diasRestantesInscripcion()
    {
        $hoy = now();
        $limite = $this->fecha_limite_inscripcion;
        
        if ($hoy > $limite) {
            return 0;
        }
        
        return $hoy->diffInDays($limite);
    }

    /**
     * Verificar si el evento ya pasó
     */
    public function yaPaso()
    {
        return $this->fecha_inicio < now();
    }

    /**
     * Scope para eventos activos (aún no han pasado)
     */
    public function scopeActivos($query)
    {
        return $query->where('fecha_inicio', '>=', now());
    }

    /**
     * Scope para eventos pasados
     */
    public function scopePasados($query)
    {
        return $query->where('fecha_inicio', '<', now());
    }

    /**
     * Scope para eventos con inscripciones abiertas
     */
    public function scopeInscripcionesAbiertas($query)
    {
        return $query->where('fecha_limite_inscripcion', '>=', now())
                     ->whereRaw('participantes_actuales < participantes_max');
    }

    /**
     * Relación con criterios de evaluación
     */
    public function criteriosEvaluacion()
    {
        return $this->hasMany(CriterioEvaluacion::class, 'event_id');
    }

    /**
     * Relación muchos a muchos con jueces asignados
     */
    public function juecesAsignados()
    {
        return $this->belongsToMany(User::class, 'evento_juez', 'event_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * Relación con evaluaciones del evento
     */
    public function evaluaciones()
    {
        return $this->hasMany(Evaluacion::class, 'event_id');
    }
}