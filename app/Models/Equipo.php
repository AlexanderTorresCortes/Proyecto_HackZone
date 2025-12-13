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

    // Relación con invitaciones
    public function invitaciones()
    {
        return $this->hasMany(InvitacionEquipo::class);
    }

    // Invitaciones pendientes
    public function invitacionesPendientes()
    {
        return $this->hasMany(InvitacionEquipo::class)->where('estado', 'pendiente');
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
     * Obtener los roles disponibles del equipo
     */
    public static function getRolesDisponibles()
    {
        return [
            'Frontend',
            'Backend',
            'Full-Stack',
            'Diseñador UX/UI',
        ];
    }

    /**
     * Verificar si un rol está ocupado
     */
    public function rolOcupado($rol)
    {
        // Para Diseñador UX/UI, permitir 2 miembros
        if ($rol === 'Diseñador UX/UI') {
            return $this->miembros()->where('rol', 'Diseñador UX/UI')->count() >= 2;
        }
        
        // Para otros roles, solo 1 miembro
        return $this->miembros()->where('rol', $rol)->exists();
    }

    /**
     * Obtener miembros por rol
     */
    public function miembrosPorRol($rol)
    {
        return $this->miembros()->where('rol', $rol)->get();
    }

    /**
     * Obtener todos los roles con su estado (ocupado/disponible)
     */
    public function getRolesEstado()
    {
        $roles = self::getRolesDisponibles();
        $estadoRoles = [];

        foreach ($roles as $rol) {
            $miembros = $this->miembrosPorRol($rol);
            $maxPermitido = ($rol === 'Diseñador UX/UI') ? 2 : 1;
            
            $estadoRoles[] = [
                'rol' => $rol,
                'ocupado' => $miembros->count() >= $maxPermitido,
                'disponible' => $miembros->count() < $maxPermitido,
                'miembros' => $miembros,
                'max' => $maxPermitido,
                'actual' => $miembros->count()
            ];
        }

        return $estadoRoles;
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
     * Relación con las entregas del equipo
     */
    public function entregas()
    {
        return $this->hasMany(Entrega::class, 'equipo_id');
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

    /**
     * Relación muchos a muchos con insignias
     */
    public function insignias()
    {
        return $this->belongsToMany(Badge::class, 'equipo_badge')
                    ->withPivot('event_id', 'lugar')
                    ->withTimestamps();
    }

    /**
     * Obtener todos los miembros del equipo (incluyendo líder)
     */
    public function todosLosMiembros()
    {
        $miembros = collect();
        
        // Asegurar que las relaciones estén cargadas
        if (!$this->relationLoaded('lider')) {
            $this->load('lider');
        }
        if (!$this->relationLoaded('miembros')) {
            $this->load('miembros.usuario');
        }
        
        // Agregar líder si existe
        if ($this->lider) {
            $miembros->push($this->lider);
        }
        
        // Agregar miembros del equipo
        if ($this->miembros) {
            foreach ($this->miembros as $miembro) {
                // Cargar la relación usuario si no está cargada
                if (!$miembro->relationLoaded('usuario')) {
                    $miembro->load('usuario');
                }
                
                if ($miembro->usuario) {
                    $miembros->push($miembro->usuario);
                }
            }
        }
        
        return $miembros->unique('id')->filter(function($miembro) {
            return $miembro !== null;
        });
    }
}