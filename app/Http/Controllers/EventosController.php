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

        // Verificar que el usuario no tenga ya otro equipo inscrito en este evento
        $equiposDelUsuario = Equipo::where('user_id', auth()->id())
                                   ->where('event_id', $evento->id)
                                   ->where('id', '!=', $equipo->id)
                                   ->first();

        if ($equiposDelUsuario) {
            return back()->with('error', 'Ya tienes otro equipo inscrito en este evento. No puedes inscribir múltiples equipos al mismo evento.');
        }

        // Verificar que el equipo no esté ya inscrito en otro evento ACTIVO
        // Si el equipo está inscrito en un evento finalizado, puede inscribirse a otro
        if ($equipo->event_id !== null) {
            $eventoAnterior = Event::find($equipo->event_id);
            // Si el evento anterior no está finalizado, no permitir la inscripción
            if ($eventoAnterior && !$eventoAnterior->estaFinalizado()) {
                return back()->with('error', 'Este equipo ya está inscrito en otro evento activo');
            }
            // Si el evento anterior está finalizado, permitir la inscripción
            // No necesitamos limpiar el event_id aquí, se actualizará más abajo
        }

        // Verificar que hay cupo disponible (ignorando fechas por ahora)
        if ($evento->estaLleno()) {
            return back()->with('error', 'El evento está lleno');
        }

        // Si el equipo estaba en otro evento, decrementar el contador del evento anterior
        if ($equipo->event_id !== null && $equipo->event_id != $evento->id) {
            $eventoAnterior = Event::find($equipo->event_id);
            if ($eventoAnterior && $eventoAnterior->participantes_actuales > 0) {
                $eventoAnterior->decrement('participantes_actuales');
            }
        }

        // Inscribir el equipo al nuevo evento
        $equipo->event_id = $evento->id;
        $equipo->save();

        // Actualizar contador de participantes del evento nuevo
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
