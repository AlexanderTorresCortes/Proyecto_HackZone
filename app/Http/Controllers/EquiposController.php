<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipo;
use App\Models\Event; 
use Illuminate\Support\Facades\Auth;
use App\Models\EquipoMiembro;
use App\Models\SolicitudEquipo;

class EquiposController extends Controller
{
    /**
     * Mostrar la lista de equipos
     */
    public function index()
    {
        // Obtener equipos ordenados por fecha con sus relaciones
        $equipos = Equipo::with('lider', 'miembros')
                        ->orderBy('created_at', 'desc')
                        ->get();

        // Obtener los torneos de la tabla 'events'
        $torneos = Event::all(); 
        
        // Enviamos ambas variables a la vista
        return view('equipos.index', compact('equipos', 'torneos'));
    }

    /**
     * Crear un nuevo equipo
     */
    public function store(Request $request)
    {
        // Validamos los datos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'ubicacion' => 'required|string',
            'torneo' => 'required|string|exists:events,titulo', 
            'acceso' => 'required|string|in:Público,Privado',
        ]);

        // Creamos el equipo en la BD
        $equipo = Equipo::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'ubicacion' => $request->ubicacion,
            'torneo' => $request->torneo,
            'acceso' => $request->acceso,
            'user_id' => Auth::id() ?? 1,
            'miembros_actuales' => 1,
            'miembros_max' => 6, // Valor por defecto
        ]);

        // Agregar al creador como miembro con rol de Líder
        EquipoMiembro::create([
            'equipo_id' => $equipo->id,
            'user_id' => Auth::id() ?? 1,
            'rol' => 'Líder'
        ]);

        return redirect()->back()->with('success', '¡Equipo creado exitosamente!');
    }

    /**
     * Mostrar detalles de un equipo específico
     */
    public function show($id)
    {
        // Obtener el equipo con sus relaciones
        $equipo = Equipo::with(['lider', 'miembros.usuario', 'solicitudesPendientes.usuario'])
                       ->findOrFail($id);
        
        // Verificar si el usuario actual es el líder
        $esLider = Auth::check() && $equipo->esLider(Auth::id());
        
        // Verificar si el usuario actual es miembro
        $esMiembro = Auth::check() && $equipo->esMiembro(Auth::id());
        
        // Verificar si el usuario ya tiene una solicitud pendiente
        $tieneSolicitudPendiente = false;
        if (Auth::check()) {
            $tieneSolicitudPendiente = SolicitudEquipo::where('equipo_id', $id)
                ->where('user_id', Auth::id())
                ->where('estado', 'pendiente')
                ->exists();
        }
        
        return view('equipos.show', compact('equipo', 'esLider', 'esMiembro', 'tieneSolicitudPendiente'));
    }

    /**
     * Solicitar unirse a un equipo
     */
    public function solicitarUnirse($id)
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para unirte a un equipo');
        }

        $equipo = Equipo::findOrFail($id);

        // Verificar si el equipo está lleno
        if ($equipo->estaLleno()) {
            return redirect()->back()->with('error', 'El equipo ya está completo');
        }

        // Verificar si ya es miembro
        if ($equipo->esMiembro(Auth::id())) {
            return redirect()->back()->with('error', 'Ya eres miembro de este equipo');
        }

        // Verificar si es el líder
        if ($equipo->esLider(Auth::id())) {
            return redirect()->back()->with('error', 'Ya eres el líder de este equipo');
        }

        // Verificar si ya tiene una solicitud pendiente
        $solicitudExistente = SolicitudEquipo::where('equipo_id', $id)
            ->where('user_id', Auth::id())
            ->where('estado', 'pendiente')
            ->first();

        if ($solicitudExistente) {
            return redirect()->back()->with('error', 'Ya tienes una solicitud pendiente para este equipo');
        }

        // Si el equipo es PÚBLICO, unirse directamente
        if ($equipo->acceso === 'Público') {
            EquipoMiembro::create([
                'equipo_id' => $equipo->id,
                'user_id' => Auth::id(),
                'rol' => 'Miembro'
            ]);

            // Actualizar contador de miembros
            $equipo->increment('miembros_actuales');

            return redirect()->back()->with('success', '¡Te has unido al equipo exitosamente!');
        }

        // Si el equipo es PRIVADO, crear solicitud
        SolicitudEquipo::create([
            'equipo_id' => $equipo->id,
            'user_id' => Auth::id(),
            'estado' => 'pendiente',
            'mensaje' => 'Solicitud para unirse al equipo'
        ]);

        return redirect()->back()->with('success', 'Solicitud enviada. El líder del equipo la revisará pronto.');
    }

    /**
     * Aceptar solicitud de un usuario (solo líder)
     */
    public function aceptarSolicitud($solicitudId)
    {
        $solicitud = SolicitudEquipo::findOrFail($solicitudId);
        $equipo = $solicitud->equipo;

        // Verificar que el usuario actual es el líder
        if (!$equipo->esLider(Auth::id())) {
            return redirect()->back()->with('error', 'No tienes permiso para realizar esta acción');
        }

        // Verificar que el equipo no esté lleno
        if ($equipo->estaLleno()) {
            $solicitud->update(['estado' => 'rechazada']);
            return redirect()->back()->with('error', 'El equipo ya está completo');
        }

        // Agregar al usuario como miembro
        EquipoMiembro::create([
            'equipo_id' => $equipo->id,
            'user_id' => $solicitud->user_id,
            'rol' => 'Miembro'
        ]);

        // Actualizar contador de miembros
        $equipo->increment('miembros_actuales');

        // Actualizar estado de la solicitud
        $solicitud->update(['estado' => 'aceptada']);

        return redirect()->back()->with('success', 'Solicitud aceptada exitosamente');
    }

    /**
     * Rechazar solicitud de un usuario (solo líder)
     */
    public function rechazarSolicitud($solicitudId)
    {
        $solicitud = SolicitudEquipo::findOrFail($solicitudId);
        $equipo = $solicitud->equipo;

        // Verificar que el usuario actual es el líder
        if (!$equipo->esLider(Auth::id())) {
            return redirect()->back()->with('error', 'No tienes permiso para realizar esta acción');
        }

        // Actualizar estado de la solicitud
        $solicitud->update(['estado' => 'rechazada']);

        return redirect()->back()->with('success', 'Solicitud rechazada');
    }

    /**
     * Ver todas las solicitudes pendientes de los equipos del usuario (para el líder)
     */
    public function verSolicitudes()
    {
        // Obtener todos los equipos donde el usuario es líder
        $equipos = Equipo::where('user_id', Auth::id())
                        ->with(['solicitudesPendientes.usuario'])
                        ->get();

        return view('equipos.solicitudes', compact('equipos'));
    }
}