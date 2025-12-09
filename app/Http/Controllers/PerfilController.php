<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipo;
use App\Models\Event;
use App\Models\User;
use App\Models\Evaluacion;
use Illuminate\Support\Facades\Auth;

class PerfilController extends Controller
{
    /**
     * Mostrar el perfil del usuario autenticado
     */
    public function index()
    {
        $user = Auth::user();

        // Datos específicos según el rol
        if ($user->isAdmin()) {
            // Estadísticas para administrador
            $data = [
                'totalUsuarios' => User::count(),
                'totalEquipos' => Equipo::count(),
                'totalEventos' => Event::count(),
                'eventosActivos' => Event::where('fecha_inicio', '>=', now())->count(),
            ];
            return view('usuario.perfil', compact('data'));

        } elseif ($user->isJuez()) {
            // Datos para juez
            $eventosAsignados = $user->eventosComoJuez;
            $totalEvaluaciones = Evaluacion::where('juez_id', $user->id)->count();
            $evaluacionesCompletadas = Evaluacion::where('juez_id', $user->id)
                                                  ->where('estado', 'completada')
                                                  ->count();
            $evaluacionesPendientes = $totalEvaluaciones - $evaluacionesCompletadas;

            $data = [
                'eventosAsignados' => $eventosAsignados,
                'totalEventos' => $eventosAsignados->count(),
                'totalEvaluaciones' => $totalEvaluaciones,
                'evaluacionesCompletadas' => $evaluacionesCompletadas,
                'evaluacionesPendientes' => $evaluacionesPendientes,
            ];
            return view('usuario.perfil', compact('data'));

        } else {
            // Datos para usuario normal
            $misEquipos = Equipo::where('user_id', $user->id)->get();
            $equiposDisponibles = Equipo::where('estado', 'Reclutando')
                                        ->where('acceso', 'Público')
                                        ->whereColumn('miembros_actuales', '<', 'miembros_max')
                                        ->orderBy('created_at', 'desc')
                                        ->take(4)
                                        ->get();
            $todosEquipos = Equipo::orderBy('created_at', 'desc')->get();

            // Cargar insignias del usuario
            $user->load('insignias');

            $data = [
                'misEquipos' => $misEquipos,
                'equiposDisponibles' => $equiposDisponibles,
                'todosEquipos' => $todosEquipos,
            ];
            return view('usuario.perfil', compact('data'));
        }
    }

    /**
     * Mostrar formulario de edición de perfil
     */
    public function edit()
    {
        return view('usuario.perfil-edit');
    }

    /**
     * Actualizar perfil del usuario
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validación
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'ubicacion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'habilidades' => 'nullable|array',
            'habilidades.*' => 'string|max:50'
        ]);

        // Actualizar datos básicos
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->ubicacion = $request->ubicacion;
        $user->telefono = $request->telefono;
        $user->bio = $request->bio;
        $user->habilidades = $request->habilidades ?? [];

        // Manejo de avatar
        if ($request->hasFile('avatar')) {
            // Eliminar avatar anterior si existe
            if ($user->avatar && file_exists(storage_path('app/public/' . $user->avatar))) {
                unlink(storage_path('app/public/' . $user->avatar));
            }

            // Guardar nuevo avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        $user->save();

        return redirect()->route('perfil.index')->with('success', '¡Perfil actualizado correctamente!');
    }
}