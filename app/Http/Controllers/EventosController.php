<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event; // Importante: Importar el modelo
use App\Models\Equipo;

class EventosController extends Controller
{
    /**
     * Muestra la página de eventos/torneos (Index)
     */
    public function index()
    {
        // Obtenemos todos los eventos de la BD en lugar del array estático
        $eventos = Event::all(); 
        
        return view('eventos.index', compact('eventos'));
    }

    /**
     * Muestra el detalle de un evento específico
     */
    public function show($id)
    {
        // Buscamos el evento con sus criterios de evaluación
        $event = Event::with('criteriosEvaluacion')->findOrFail($id);

        // Retornamos la vista pasando la variable $event
        return view('eventos.show', compact('event'));
    }

    /**
     * Inscribir un equipo a un evento
     */
    public function inscribir(Request $request, $id)
    {
        $request->validate([
            'equipo_id' => 'required|exists:equipos,id'
        ]);

        $evento = Event::findOrFail($id);
        $equipo = Equipo::findOrFail($request->equipo_id);

        // Verificar que el usuario es el líder del equipo
        if ($equipo->user_id !== auth()->id()) {
            return back()->with('error', 'Solo el líder puede inscribir al equipo');
        }

        // Verificar que el equipo no esté ya inscrito en otro evento
        if ($equipo->event_id !== null) {
            return back()->with('error', 'Este equipo ya está inscrito en otro evento');
        }

        // Verificar que las inscripciones están abiertas
        if (!$evento->inscripcionesAbiertas()) {
            return back()->with('error', 'Las inscripciones para este evento están cerradas');
        }

        // Verificar que hay cupo disponible
        if ($evento->estaLleno()) {
            return back()->with('error', 'El evento está lleno');
        }

        // Inscribir el equipo
        $equipo->event_id = $evento->id;
        $equipo->save();

        // Actualizar contador de participantes del evento
        $evento->increment('participantes_actuales');

        return back()->with('success', '¡Equipo inscrito exitosamente! Ahora formas parte de ' . $evento->titulo);
    }

    /**
     * Ver resultados/ranking de un evento
     */
    public function verResultados($id)
    {
        $evento = Event::with(['criteriosEvaluacion', 'juecesAsignados'])->findOrFail($id);
        $ranking = $evento->calcularRanking();
        $primerosLugares = $evento->getPrimerosLugares();
        $estadisticas = $evento->getEstadisticasEvaluaciones();

        // Asignar insignias a los ganadores
        if (count($ranking) > 0) {
            $evento->asignarInsignias();
        }

        return view('eventos.resultados', compact('evento', 'ranking', 'primerosLugares', 'estadisticas'));
    }
}
