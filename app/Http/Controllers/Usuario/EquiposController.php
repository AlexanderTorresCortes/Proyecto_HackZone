<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipo;
use App\Models\Event; 
use Illuminate\Support\Facades\Auth;
use App\Models\EquipoMiembro;
use App\Models\SolicitudEquipo;
use App\Models\InvitacionEquipo;
use App\Models\User;
use App\Notifications\EquipoSolicitudNotification;
use App\Notifications\EquipoSolicitudRespuestaNotification;
use App\Notifications\EquipoInvitacionNotification;
use App\Notifications\MiembroUnidoEquipoNotification;

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
            'acceso' => 'required|string|in:Público,Privado',
        ]);

        // Creamos el equipo en la BD
        $equipo = Equipo::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'ubicacion' => $request->ubicacion,
            'torneo' => 'Sin torneo asignado', // Valor por defecto
            'acceso' => $request->acceso,
            'user_id' => Auth::id() ?? 1,
            'miembros_actuales' => 1,
            'miembros_max' => 5, // Equipos de 5 integrantes (1 líder + 4 roles)
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
    public function solicitarUnirse(Request $request, $id)
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para unirte a un equipo');
        }

        $request->validate([
            'rol_solicitado' => 'required|string|in:Frontend,Backend,Full-Stack,Diseñador UX/UI',
        ]);

        $equipo = Equipo::findOrFail($id);

        // Verificar si el equipo está lleno
        if ($equipo->estaLleno()) {
            return redirect()->back()->with('error', 'El equipo ya está completo');
        }

        // Verificar si el rol solicitado está disponible
        if ($equipo->rolOcupado($request->rol_solicitado)) {
            return redirect()->back()->with('error', 'El rol ' . $request->rol_solicitado . ' ya está ocupado');
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
            // Verificar que el rol solicitado no esté ocupado
            if ($equipo->rolOcupado($request->rol_solicitado)) {
                return redirect()->back()->with('error', 'El rol ' . $request->rol_solicitado . ' ya está ocupado');
            }

            $miembro = EquipoMiembro::create([
                'equipo_id' => $equipo->id,
                'user_id' => Auth::id(),
                'rol' => $request->rol_solicitado
            ]);

            // Actualizar contador de miembros
            $equipo->increment('miembros_actuales');

            // Notificar al líder
            $equipo->lider->notify(new MiembroUnidoEquipoNotification($miembro));

            return redirect()->back()->with('success', '¡Te has unido al equipo exitosamente!');
        }

        // Si el equipo es PRIVADO, crear solicitud
        $solicitud = SolicitudEquipo::create([
            'equipo_id' => $equipo->id,
            'user_id' => Auth::id(),
            'estado' => 'pendiente',
            'mensaje' => 'Solicitud para unirse al equipo',
            'rol_solicitado' => $request->rol_solicitado
        ]);

        // Notificar al líder del equipo
        $equipo->lider->notify(new EquipoSolicitudNotification($solicitud));

        // Enviar correo al líder
        try {
            \Illuminate\Support\Facades\Mail::to($equipo->lider->email)
                ->send(new \App\Mail\SolicitudEquipoEmail($solicitud));
        } catch (\Exception $e) {
            // Log error pero no fallar la solicitud
            \Log::error('Error enviando correo de solicitud: ' . $e->getMessage());
        }

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

        // Agregar al usuario como miembro con el rol solicitado
        $miembro = EquipoMiembro::create([
            'equipo_id' => $equipo->id,
            'user_id' => $solicitud->user_id,
            'rol' => $solicitud->rol_solicitado ?? 'Miembro'
        ]);

        // Actualizar contador de miembros
        $equipo->increment('miembros_actuales');

        // Actualizar estado de la solicitud
        $solicitud->update(['estado' => 'aceptada']);

        // Notificar al solicitante que fue aceptado
        $solicitud->usuario->notify(new EquipoSolicitudRespuestaNotification($solicitud, true));

        // Enviar correo al solicitante
        try {
            \Illuminate\Support\Facades\Mail::to($solicitud->usuario->email)
                ->send(new \App\Mail\SolicitudAceptadaEmail($solicitud));
        } catch (\Exception $e) {
            // Log error pero no fallar la aceptación
            \Log::error('Error enviando correo de solicitud aceptada: ' . $e->getMessage());
        }

        // Notificar al líder que alguien se unió
        $equipo->lider->notify(new MiembroUnidoEquipoNotification($miembro));

        // Marcar la notificación como leída
        auth()->user()->notifications()
            ->where('data->solicitud_id', $solicitud->id)
            ->where('data->type', 'equipo_solicitud')
            ->update(['read_at' => now()]);

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

        // Notificar al solicitante que fue rechazado
        $solicitud->usuario->notify(new EquipoSolicitudRespuestaNotification($solicitud, false));

        // Marcar la notificación como leída
        auth()->user()->notifications()
            ->where('data->solicitud_id', $solicitud->id)
            ->where('data->type', 'equipo_solicitud')
            ->update(['read_at' => now()]);

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

    /**
     * Buscar usuarios para invitar (solo líder)
     */
    public function buscarUsuarios(Request $request, $equipoId)
    {
        $equipo = Equipo::findOrFail($equipoId);

        // Verificar que el usuario actual es el líder
        if (!$equipo->esLider(Auth::id())) {
            return response()->json(['error' => 'No tienes permiso para realizar esta acción'], 403);
        }

        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json(['usuarios' => []]);
        }

        // Buscar usuarios que no sean miembros del equipo y no sean el líder
        $usuarios = User::where('rol', 'usuario')
            ->where('id', '!=', Auth::id())
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('username', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->whereDoesntHave('equiposComoMiembro', function($q) use ($equipoId) {
                $q->where('equipo_id', $equipoId);
            })
            ->take(10)
            ->get(['id', 'name', 'username', 'email', 'avatar']);

        return response()->json(['usuarios' => $usuarios]);
    }

    /**
     * Enviar invitación a un usuario (solo líder)
     */
    public function enviarInvitacion(Request $request, $equipoId)
    {
        $equipo = Equipo::findOrFail($equipoId);

        // Verificar que el usuario actual es el líder
        if (!$equipo->esLider(Auth::id())) {
            return redirect()->back()->with('error', 'No tienes permiso para realizar esta acción');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'mensaje' => 'nullable|string|max:500',
        ]);

        $userId = $request->user_id;

        // Verificar que el equipo no esté lleno
        if ($equipo->estaLleno()) {
            return redirect()->back()->with('error', 'El equipo ya está completo');
        }

        // Verificar que el usuario no sea miembro
        if ($equipo->esMiembro($userId)) {
            return redirect()->back()->with('error', 'Este usuario ya es miembro del equipo');
        }

        // Verificar que no haya una invitación pendiente
        $invitacionExistente = InvitacionEquipo::where('equipo_id', $equipoId)
            ->where('user_id', $userId)
            ->where('estado', 'pendiente')
            ->first();

        if ($invitacionExistente) {
            return redirect()->back()->with('error', 'Ya existe una invitación pendiente para este usuario');
        }

        // Crear la invitación
        $invitacion = InvitacionEquipo::create([
            'equipo_id' => $equipoId,
            'user_id' => $userId,
            'invitado_por' => Auth::id(),
            'estado' => 'pendiente',
            'mensaje' => $request->mensaje ?? 'Invitación para unirse al equipo',
        ]);

        // Notificar al usuario invitado
        $usuarioInvitado = User::findOrFail($userId);
        $usuarioInvitado->notify(new EquipoInvitacionNotification($invitacion));

        return redirect()->back()->with('success', 'Invitación enviada correctamente.');
    }

    /**
     * Aceptar invitación
     */
    public function aceptarInvitacion($invitacionId)
    {
        $invitacion = InvitacionEquipo::findOrFail($invitacionId);
        $equipo = $invitacion->equipo;

        // Verificar que el usuario actual es el invitado
        if ($invitacion->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'No tienes permiso para realizar esta acción');
        }

        // Verificar que la invitación está pendiente
        if (!$invitacion->estaPendiente()) {
            return redirect()->back()->with('error', 'Esta invitación ya ha sido procesada');
        }

        // Verificar que el equipo no esté lleno
        if ($equipo->estaLleno()) {
            $invitacion->update(['estado' => 'rechazada']);
            return redirect()->back()->with('error', 'El equipo ya está completo');
        }

        // Agregar al usuario como miembro (el líder asignará el rol después)
        $miembro = EquipoMiembro::create([
            'equipo_id' => $equipo->id,
            'user_id' => Auth::id(),
            'rol' => null // El líder asignará el rol desde la gestión del equipo
        ]);

        // Actualizar contador de miembros
        $equipo->increment('miembros_actuales');

        // Actualizar estado de la invitación
        $invitacion->update(['estado' => 'aceptada']);

        // Notificar al líder que alguien se unió
        $equipo->lider->notify(new MiembroUnidoEquipoNotification($miembro));

        // Marcar la notificación como leída
        Auth::user()->notifications()
            ->where('data->invitacion_id', $invitacion->id)
            ->where('data->type', 'equipo_invitacion')
            ->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'Invitación aceptada. ¡Bienvenido al equipo!');
    }

    /**
     * Rechazar invitación
     */
    public function rechazarInvitacion($invitacionId)
    {
        $invitacion = InvitacionEquipo::findOrFail($invitacionId);

        // Verificar que el usuario actual es el invitado
        if ($invitacion->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'No tienes permiso para realizar esta acción');
        }

        // Actualizar estado de la invitación
        $invitacion->update(['estado' => 'rechazada']);

        // Marcar la notificación como leída
        Auth::user()->notifications()
            ->where('data->invitacion_id', $invitacion->id)
            ->where('data->type', 'equipo_invitacion')
            ->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'Invitación rechazada');
    }

    /**
     * Asignar rol a un miembro (solo líder)
     */
    public function asignarRol(Request $request, $miembroId)
    {
        $request->validate([
            'rol' => 'nullable|string|in:Frontend,Backend,Full-Stack,Diseñador UX/UI',
        ]);

        $miembro = EquipoMiembro::findOrFail($miembroId);
        $equipo = $miembro->equipo;

        // Verificar que el usuario actual es el líder
        if (!$equipo->esLider(Auth::id())) {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para realizar esta acción'], 403);
        }

        // Verificar que no es el líder
        if ($miembro->rol === 'Líder') {
            return response()->json(['success' => false, 'message' => 'No se puede cambiar el rol del líder'], 400);
        }

        // Si se asigna un rol, verificar que no esté ocupado
        if ($request->rol) {
            // Para Diseñador UX/UI, verificar que no haya 2 ya
            if ($request->rol === 'Diseñador UX/UI') {
                $countDiseñadores = $equipo->miembros()->where('rol', 'Diseñador UX/UI')->count();
                if ($miembro->rol !== 'Diseñador UX/UI' && $countDiseñadores >= 2) {
                    return response()->json(['success' => false, 'message' => 'Ya hay 2 Diseñadores UX/UI en el equipo'], 400);
                }
            } else {
                // Para otros roles, verificar que no esté ocupado por otro miembro
                $rolOcupado = $equipo->miembros()
                    ->where('rol', $request->rol)
                    ->where('id', '!=', $miembroId)
                    ->exists();
                
                if ($rolOcupado) {
                    return response()->json(['success' => false, 'message' => 'Este rol ya está asignado a otro miembro'], 400);
                }
            }
        }

        // Asignar el rol
        $miembro->update(['rol' => $request->rol]);

        return response()->json(['success' => true, 'message' => 'Rol asignado correctamente']);
    }

    /**
     * Cambiar el acceso del equipo entre Público y Privado (solo líder)
     */
    public function cambiarAcceso($id)
    {
        $equipo = Equipo::findOrFail($id);

        // Verificar que el usuario sea el líder
        if (!$equipo->esLider(Auth::id())) {
            return redirect()->back()->with('error', 'Solo el líder puede cambiar el acceso del equipo');
        }

        // Toggle acceso
        $equipo->acceso = ($equipo->acceso === 'Público') ? 'Privado' : 'Público';
        $equipo->save();

        return redirect()->back()->with('success', 'Acceso del equipo cambiado a: ' . $equipo->acceso);
    }
}