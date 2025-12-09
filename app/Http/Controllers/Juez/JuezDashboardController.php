<?php

namespace App\Http\Controllers\Juez;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Equipo;
use App\Models\Evaluacion;
use App\Models\Puntuacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class JuezDashboardController extends Controller
{
    /**
     * Mostrar dashboard del juez con eventos asignados
     */
    public function index()
    {
        $juez = Auth::user();

        // Obtener eventos asignados al juez
        $eventos = $juez->eventosComoJuez()
                       ->with('criteriosEvaluacion')
                       ->orderBy('fecha_inicio', 'desc')
                       ->get();

        // Estadísticas
        $totalEventos = $eventos->count();
        $totalEvaluaciones = $juez->evaluacionesRealizadas()->count();
        $evaluacionesPendientes = $juez->evaluacionesRealizadas()
                                       ->where('estado', 'pendiente')
                                       ->count();
        $evaluacionesCompletadas = $juez->evaluacionesRealizadas()
                                        ->where('estado', 'completada')
                                        ->count();

        return view('juez.dashboard', compact(
            'eventos',
            'totalEventos',
            'totalEvaluaciones',
            'evaluacionesPendientes',
            'evaluacionesCompletadas'
        ));
    }

    /**
     * Mostrar equipos de un evento para evaluar
     */
    public function verEquipos($eventoId)
    {
        $juez = Auth::user();
        $evento = Event::with(['criteriosEvaluacion', 'juecesAsignados'])
                      ->findOrFail($eventoId);

        // Verificar que el juez está asignado a este evento
        if (!$evento->juecesAsignados->contains($juez->id)) {
            abort(403, 'No tienes permiso para evaluar este evento');
        }

        // Obtener equipos inscritos en el evento específico
        // Filtrar por evento si existe la relación, sino obtener todos
        $equipos = Equipo::with(['lider', 'miembros.usuario'])
                        ->where('event_id', $eventoId)
                        ->get();

        // Para cada equipo, verificar si ya fue evaluado por este juez
        foreach ($equipos as $equipo) {
            $evaluacion = Evaluacion::where('event_id', $eventoId)
                                   ->where('equipo_id', $equipo->id)
                                   ->where('juez_id', $juez->id)
                                   ->first();

            $equipo->evaluacion = $evaluacion;
            $equipo->evaluado = $evaluacion && $evaluacion->estado === 'completada';
        }

        return view('juez.equipos', compact('evento', 'equipos'));
    }

    /**
     * Mostrar formulario de evaluación de un equipo
     */
    public function evaluarEquipo($eventoId, $equipoId)
    {
        $juez = Auth::user();
        $evento = Event::with('criteriosEvaluacion')->findOrFail($eventoId);
        $equipo = Equipo::with(['lider', 'miembros.usuario', 'entregas' => function($query) {
            $query->orderBy('version', 'desc');
        }])->findOrFail($equipoId);

        // Verificar que el juez está asignado a este evento
        if (!$evento->juecesAsignados->contains($juez->id)) {
            abort(403, 'No tienes permiso para evaluar este evento');
        }

        // Buscar evaluación existente
        $evaluacion = Evaluacion::where('event_id', $eventoId)
                                ->where('equipo_id', $equipoId)
                                ->where('juez_id', $juez->id)
                                ->with('puntuaciones')
                                ->first();

        return view('juez.evaluar', compact('evento', 'equipo', 'evaluacion'));
    }

    /**
     * Guardar evaluación de un equipo
     */
    public function guardarEvaluacion(Request $request, $eventoId, $equipoId)
    {
        $juez = Auth::user();
        $evento = Event::with('criteriosEvaluacion')->findOrFail($eventoId);

        // Verificar que el juez está asignado a este evento
        if (!$evento->juecesAsignados->contains($juez->id)) {
            abort(403, 'No tienes permiso para evaluar este evento');
        }

        // Validar
        $validated = $request->validate([
            'puntuaciones' => 'required|array',
            'puntuaciones.*' => 'required|integer|min:1|max:10',
            'comentarios' => 'nullable|string|max:1000',
            'estado' => 'required|in:pendiente,completada',
        ], [
            'puntuaciones.required' => 'Debes calificar al menos un criterio',
            'puntuaciones.*.required' => 'Todas las puntuaciones deben tener un valor',
            'puntuaciones.*.integer' => 'Las puntuaciones deben ser números enteros',
            'puntuaciones.*.min' => 'La puntuación mínima es 1',
            'puntuaciones.*.max' => 'La puntuación máxima es 10',
        ]);

        // Si el estado es completada, verificar que todos los criterios estén calificados
        if ($validated['estado'] === 'completada') {
            $totalCriterios = $evento->criteriosEvaluacion->count();
            $puntuacionesProporcionadas = count($validated['puntuaciones']);

            if ($puntuacionesProporcionadas < $totalCriterios) {
                return redirect()
                    ->back()
                    ->withErrors(['puntuaciones' => "Debes calificar todos los criterios ({$puntuacionesProporcionadas}/{$totalCriterios}). Completa la evaluación antes de enviarla."])
                    ->withInput();
            }
        }

        // Crear o actualizar evaluación
        $evaluacion = Evaluacion::updateOrCreate(
            [
                'event_id' => $eventoId,
                'equipo_id' => $equipoId,
                'juez_id' => $juez->id,
            ],
            [
                'comentarios' => $validated['comentarios'],
                'estado' => $validated['estado'],
            ]
        );

        // Guardar puntuaciones para cada criterio
        foreach ($validated['puntuaciones'] as $criterioId => $puntuacion) {
            Puntuacion::updateOrCreate(
                [
                    'evaluacion_id' => $evaluacion->id,
                    'criterio_id' => $criterioId,
                ],
                [
                    'puntuacion' => $puntuacion,
                ]
            );
        }

        $mensaje = $validated['estado'] === 'completada'
                  ? 'Evaluación guardada y completada exitosamente'
                  : 'Evaluación guardada como borrador';

        // Enviar email si la evaluación está completada
        if ($validated['estado'] === 'completada') {
            $this->enviarEmailEvaluacion($evaluacion, $evento, $equipoId, $juez);
        }

        return redirect()
            ->route('juez.equipos', $eventoId)
            ->with('success', $mensaje);
    }

    /**
     * Ver ranking del evento (solo visualización)
     */
    public function verRanking($eventoId)
    {
        $juez = Auth::user();
        $evento = Event::with(['criteriosEvaluacion', 'juecesAsignados'])->findOrFail($eventoId);

        // Verificar que el juez está asignado a este evento
        if (!$evento->juecesAsignados->contains($juez->id)) {
            abort(403, 'No tienes permiso para ver este ranking');
        }

        $ranking = $evento->calcularRanking();
        $primerosLugares = $evento->getPrimerosLugares();
        $estadisticas = $evento->getEstadisticasEvaluaciones();

        // Asignar insignias a los ganadores
        if (count($ranking) > 0) {
            $evento->asignarInsignias();
        }

        return view('juez.ranking', compact('evento', 'ranking', 'primerosLugares', 'estadisticas'));
    }

    /**
     * Enviar email de evaluación completada a los miembros del equipo
     */
    private function enviarEmailEvaluacion($evaluacion, $evento, $equipoId, $juez)
    {
        try {
            $equipo = Equipo::with(['lider', 'miembros.usuario'])->findOrFail($equipoId);

            // Calcular puntuación total
            $puntuacionTotal = 0;
            $puntuaciones = [];

            foreach ($evaluacion->puntuaciones as $puntuacion) {
                $criterio = $evento->criteriosEvaluacion->where('id', $puntuacion->criterio_id)->first();
                if ($criterio) {
                    $puntuacionTotal += $puntuacion->puntuacion * ($criterio->peso / 10);
                    $puntuaciones[$puntuacion->criterio_id] = $puntuacion->puntuacion;
                }
            }

            // Obtener todos los miembros del equipo
            $miembros = collect();
            if ($equipo->lider) {
                $miembros->push($equipo->lider);
            }
            foreach ($equipo->miembros as $miembro) {
                if ($miembro->usuario) {
                    $miembros->push($miembro->usuario);
                }
            }

            // Enviar email a cada miembro
            foreach ($miembros->unique('id') as $miembro) {
                try {
                    Mail::send('emails.proyecto-calificado', [
                        'miembro' => $miembro,
                        'equipo' => $equipo,
                        'evento' => $evento,
                        'juez' => $juez,
                        'evaluacion' => $evaluacion,
                        'puntuacionTotal' => $puntuacionTotal,
                    ], function ($message) use ($miembro) {
                        $message->to($miembro->email, $miembro->name)
                                ->subject('¡Tu Proyecto ha sido Calificado! - HackZone');
                    });
                } catch (\Exception $e) {
                    // Log el error pero continuar con los demás emails
                    \Log::error("Error enviando email a {$miembro->email}: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            // Log el error pero no detener el proceso
            \Log::error("Error en enviarEmailEvaluacion: " . $e->getMessage());
        }
    }
}
