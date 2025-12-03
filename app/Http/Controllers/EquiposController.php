<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipo;
use App\Models\Event; // <--- 1. IMPORTANTE: Importamos el modelo de la tabla 'events'
use Illuminate\Support\Facades\Auth;

class EquiposController extends Controller
{
    public function index()
    {
        // Obtener equipos ordenados por fecha
        $equipos = Equipo::orderBy('created_at', 'desc')->get();

        // 2. OBTENER LOS TORNEOS DE LA TABLA 'events'
        // Esto traerá todos los registros de tu tabla events para el select
        $torneos = Event::all(); 
        
        // Enviamos AMBAS variables a la vista
        return view('equipos.index', compact('equipos', 'torneos'));
    }

    public function store(Request $request)
    {
        // Validamos los datos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'ubicacion' => 'required|string',
            
            // Validación mejorada: verifica que el valor enviado exista en la tabla 'events' columna 'titulo'
            // (Asegúrate que la columna de nombre en tu tabla events se llame 'titulo', si no, cambia 'titulo' por 'nombre')
            'torneo' => 'required|string|exists:events,titulo', 
            
            'acceso' => 'required|string|in:Público,Privado', // Validamos que solo sean esas 2 opciones
        ]);

        // Creamos el equipo en la BD
        Equipo::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'ubicacion' => $request->ubicacion,
            'torneo' => $request->torneo,
            'acceso' => $request->acceso,
            'user_id' => Auth::id() ?? 1,
            'miembros_actuales' => 1,
        ]);

        return redirect()->back()->with('success', '¡Equipo creado exitosamente!');
    }
}