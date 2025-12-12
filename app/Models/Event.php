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
        'finalizado_at',
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
        'finalizado_at' => 'datetime',
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
     * TEMPORALMENTE: Ignora las fechas, solo verifica si hay cupo disponible
     */
    public function inscripcionesAbiertas()
    {
        // Ignorar fechas por ahora, solo verificar si hay cupo
        return !$this->estaLleno();
    }

    /**
     * Obtener la URL de la imagen
     */
    public function getImagenUrlAttribute()
    {
        if ($this->imagen) {
            // Si contiene 'eventos/' es del formulario (storage)
            // Si no, es del seeder (public/images)
            if (str_contains($this->imagen, 'eventos/')) {
                return asset('storage/' . $this->imagen);
            } else {
                return asset('images/' . $this->imagen);
            }
        }
        // Retornar una imagen placeholder o null si no hay imagen
        return null;
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
     * Verificar si el evento está finalizado
     * Un evento está finalizado si tiene finalizado_at o si la fecha_inicio ya pasó
     */
    public function estaFinalizado()
    {
        return $this->finalizado_at !== null || $this->yaPaso();
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

    /**
     * Relación con entregas del evento
     */
    public function entregas()
    {
        return $this->hasMany(Entrega::class, 'event_id');
    }

    /**
     * Relación con equipos inscritos en el evento
     */
    public function equipos()
    {
        return $this->hasMany(Equipo::class, 'event_id');
    }

    /**
     * Calcular ranking de equipos del evento
     * Retorna un array con los equipos ordenados por calificación promedio
     * Optimizado para evitar consultas N+1
     */
    public function calcularRanking()
    {
        // Cargar equipos con relaciones necesarias
        $equipos = $this->equipos()->with(['lider'])->get();

        \Log::info("Calculando ranking para evento {$this->id}. Equipos encontrados: " . $equipos->count());

        if ($equipos->isEmpty()) {
            return [];
        }

        // Obtener todas las evaluaciones completadas de todos los equipos en una sola consulta
        $equiposIds = $equipos->pluck('id')->toArray();
        $evaluaciones = \App\Models\Evaluacion::where('event_id', $this->id)
            ->whereIn('equipo_id', $equiposIds)
            ->where('estado', 'completada')
            ->with(['puntuaciones.criterio'])
            ->get()
            ->groupBy('equipo_id');

        \Log::info("Evaluaciones completadas encontradas: " . $evaluaciones->count());

        $ranking = [];

        foreach ($equipos as $equipo) {
            $evaluacionesCompletadas = $evaluaciones->get($equipo->id, collect());

            \Log::info("Equipo {$equipo->nombre} (ID: {$equipo->id}) tiene {$evaluacionesCompletadas->count()} evaluaciones completadas");

            if ($evaluacionesCompletadas->isEmpty()) {
                \Log::warning("Equipo {$equipo->nombre} no tiene evaluaciones completadas, se omite del ranking");
                continue;
            }

            // Calcular promedio de todas las evaluaciones
            $sumaPromedios = 0;
            $evaluacionesValidas = 0;
            
            foreach ($evaluacionesCompletadas as $evaluacion) {
                // Asegurar que la evaluación tenga puntuaciones
                if (!$evaluacion->puntuaciones || $evaluacion->puntuaciones->isEmpty()) {
                    \Log::warning("Evaluación ID {$evaluacion->id} no tiene puntuaciones");
                    continue;
                }
                
                $promedioEval = $evaluacion->calcularPromedio();
                if ($promedioEval > 0) {
                    $sumaPromedios += $promedioEval;
                    $evaluacionesValidas++;
                    \Log::info("Evaluación ID {$evaluacion->id} del equipo {$equipo->nombre}: promedio = {$promedioEval}");
                }
            }

            if ($evaluacionesValidas == 0) {
                \Log::warning("Equipo {$equipo->nombre} no tiene evaluaciones válidas con puntuaciones");
                continue;
            }

            $promedioFinal = $sumaPromedios / $evaluacionesValidas;

            $ranking[] = [
                'equipo' => $equipo,
                'promedio' => round($promedioFinal, 2),
                'evaluaciones_recibidas' => $evaluacionesValidas,
                'total_jueces' => $this->juecesAsignados->count()
            ];

            \Log::info("Equipo {$equipo->nombre} agregado al ranking con promedio: {$promedioFinal}");
        }

        // Ordenar por promedio descendente
        usort($ranking, function($a, $b) {
            return $b['promedio'] <=> $a['promedio'];
        });

        \Log::info("Ranking final calculado con " . count($ranking) . " equipos");

        return $ranking;
    }

    /**
     * Obtener los 3 primeros lugares del evento
     */
    public function getPrimerosLugares()
    {
        $ranking = $this->calcularRanking();
        return array_slice($ranking, 0, 3);
    }

    /**
     * Asignar insignias a los equipos ganadores
     */
    public function asignarInsignias()
    {
        $ranking = $this->calcularRanking();
        $primerosLugares = array_slice($ranking, 0, 3);

        foreach ($primerosLugares as $index => $item) {
            $lugar = $index + 1;
            $equipo = $item['equipo'];

            // Obtener o crear la insignia para este lugar
            $badge = \App\Models\Badge::obtenerInsigniaPorLugar($lugar);

            // Verificar si el equipo ya tiene esta insignia para este evento
            $yaTieneInsignia = $equipo->insignias()
                ->wherePivot('event_id', $this->id)
                ->where('badges.id', $badge->id)
                ->exists();

            if (!$yaTieneInsignia) {
                // Asignar insignia al equipo
                $equipo->insignias()->attach($badge->id, [
                    'event_id' => $this->id,
                    'lugar' => $lugar
                ]);

                // Asignar insignia a todos los miembros del equipo (incluyendo líder)
                $miembros = $equipo->todosLosMiembros();
                foreach ($miembros as $miembro) {
                    if ($miembro) {
                        // Verificar si el usuario ya tiene esta insignia para este equipo y evento
                        $yaTieneInsigniaUsuario = $miembro->insignias()
                            ->wherePivot('equipo_id', $equipo->id)
                            ->wherePivot('event_id', $this->id)
                            ->where('badges.id', $badge->id)
                            ->exists();

                        if (!$yaTieneInsigniaUsuario) {
                            $miembro->insignias()->attach($badge->id, [
                                'equipo_id' => $equipo->id,
                                'event_id' => $this->id
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Obtener estadísticas de evaluaciones del evento
     */
    public function getEstadisticasEvaluaciones()
    {
        $totalEquipos = $this->equipos()->count();
        $totalJueces = $this->juecesAsignados->count();
        $evaluacionesEsperadas = $totalEquipos * $totalJueces;

        $evaluacionesCompletadas = $this->evaluaciones()
            ->where('estado', 'completada')
            ->count();

        $evaluacionesEnProceso = $this->evaluaciones()
            ->where('estado', 'pendiente')
            ->count();

        $evaluacionesPendientes = $evaluacionesEsperadas - ($evaluacionesCompletadas + $evaluacionesEnProceso);

        return [
            'total_equipos' => $totalEquipos,
            'total_jueces' => $totalJueces,
            'evaluaciones_esperadas' => $evaluacionesEsperadas,
            'completadas' => $evaluacionesCompletadas,
            'en_proceso' => $evaluacionesEnProceso,
            'pendientes' => max(0, $evaluacionesPendientes),
            'porcentaje_completado' => $evaluacionesEsperadas > 0
                ? round(($evaluacionesCompletadas / $evaluacionesEsperadas) * 100, 2)
                : 0
        ];
    }

    /**
     * Obtener ganadores con manejo de empates
     * Retorna un array con los lugares (1, 2, 3) y los equipos que ocupan cada lugar
     * Si hay empates, múltiples equipos pueden ocupar el mismo lugar
     */
    public function obtenerGanadoresConEmpates()
    {
        $ranking = $this->calcularRanking();
        
        if (empty($ranking)) {
            return [
                'primer_lugar' => [],
                'segundo_lugar' => [],
                'tercer_lugar' => []
            ];
        }

        $ganadores = [
            'primer_lugar' => [],
            'segundo_lugar' => [],
            'tercer_lugar' => []
        ];

        $lugarActual = 1;
        $promedioAnterior = null;
        $indiceRanking = 0;

        while ($lugarActual <= 3 && $indiceRanking < count($ranking)) {
            $item = $ranking[$indiceRanking];
            $promedioActual = $item['promedio'];

            // Si es el primer elemento o tiene el mismo promedio que el anterior, mismo lugar
            if ($promedioAnterior === null || $promedioActual == $promedioAnterior) {
                // Asignar al lugar correspondiente
                if ($lugarActual == 1) {
                    $ganadores['primer_lugar'][] = $item;
                } elseif ($lugarActual == 2) {
                    $ganadores['segundo_lugar'][] = $item;
                } elseif ($lugarActual == 3) {
                    $ganadores['tercer_lugar'][] = $item;
                }
            } else {
                // Si el promedio es diferente, avanzar al siguiente lugar
                $lugarActual++;
                
                // Si ya pasamos el lugar 3, salir
                if ($lugarActual > 3) {
                    break;
                }
                
                // Asignar al nuevo lugar
                if ($lugarActual == 2) {
                    $ganadores['segundo_lugar'][] = $item;
                } elseif ($lugarActual == 3) {
                    $ganadores['tercer_lugar'][] = $item;
                }
            }

            $promedioAnterior = $promedioActual;
            $indiceRanking++;
        }

        return $ganadores;
    }
}