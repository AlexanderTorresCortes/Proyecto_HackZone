<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipo;
use Illuminate\Support\Facades\Auth;

class PerfilController extends Controller
{
    /**
     * Mostrar el perfil del usuario autenticado
     */
    public function index()
    {
        $user = Auth::user();
        
        // Obtener equipos donde el usuario es dueño
        $misEquipos = Equipo::where('user_id', $user->id)->get();
        
        // Obtener equipos disponibles (reclutando y públicos)
        $equiposDisponibles = Equipo::where('estado', 'Reclutando')
                                    ->where('acceso', 'Público')
                                    ->whereColumn('miembros_actuales', '<', 'miembros_max')
                                    ->orderBy('created_at', 'desc')
                                    ->take(4)
                                    ->get();
        
        // Obtener todos los equipos
        $todosEquipos = Equipo::orderBy('created_at', 'desc')->get();
        
        return view('usuario.perfil', compact(
            'misEquipos',
            'equiposDisponibles',
            'todosEquipos'
        ));
    }
}