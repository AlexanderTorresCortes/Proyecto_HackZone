<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    // Campos que permitimos llenar desde el formulario
    protected $fillable = [
        'nombre',
        'descripcion',
        'ubicacion',
        'torneo',
        'acceso',
        'user_id', // Importante para asignar el dueño
        'event_id', // Evento al que pertenece el equipo
        'miembros_actuales',
        'miembros_max'
    ];

    // Relación opcional: Un equipo pertenece a un usuario
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con el líder (creador del equipo)
    public function lider()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con miembros del equipo
    public function miembros()
    {
        return $this->hasMany(EquipoMiembro::class);
    }

    // Relación con solicitudes
    public function solicitudes()
    {
        return $this->hasMany(SolicitudEquipo::class);
    }

    // Solicitudes pendientes
    public function solicitudesPendientes()
    {
        return $this->hasMany(SolicitudEquipo::class)->where('estado', 'pendiente');
    }

    // Verificar si un usuario es miembro
    public function esMiembro($userId)
    {
        return $this->miembros()->where('user_id', $userId)->exists();
    }

    // Verificar si un usuario es el líder
    public function esLider($userId)
    {
        return $this->user_id == $userId;
    }

    // Verificar si el equipo está lleno
    public function estaLleno()
    {
        return $this->miembros_actuales >= $this->miembros_max;
    }

    /**
     * Relación con el evento
     */
    public function evento()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Relación con las evaluaciones recibidas
     */
    public function evaluaciones()
    {
        return $this->hasMany(Evaluacion::class, 'equipo_id');
    }

    /**
     * Calcular promedio de evaluaciones del equipo en un evento
     */
    public function promedioEvaluacion($eventId = null)
    {
        $query = $this->evaluaciones()->where('estado', 'completada');

        if ($eventId) {
            $query->where('event_id', $eventId);
        }

        $evaluaciones = $query->with('puntuaciones')->get();

        if ($evaluaciones->isEmpty()) {
            return 0;
        }

        $totalPromedio = 0;
        foreach ($evaluaciones as $evaluacion) {
            $totalPromedio += $evaluacion->calcularPromedio();
        }

        return round($totalPromedio / $evaluaciones->count(), 2);
    }
}