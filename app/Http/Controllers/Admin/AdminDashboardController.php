<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Equipo;
use App\Models\Event;
use Carbon\Carbon;


class AdminDashboardController extends Controller
{
    /**
     * Mostrar el dashboard de administrador
     */
    public function index()
    {
        // Estadísticas principales
        $totalUsuarios = User::count();
        $equiposActivos = Equipo::whereIn('estado', ['Reclutando', 'Completo'])->count();
        $eventosProgramados = Event::where('fecha_inicio', '>=', Carbon::now())
                                    ->where('fecha_inicio', '<=', Carbon::now()->addDays(30))
                                    ->count();
        $alertasPendientes = 3; // Esto sería dinámico según tu lógica
        
        // Actividad reciente (ejemplo - necesitarías crear un modelo Activity)
        $actividadReciente = $this->obtenerActividadReciente();
        
        return view('admin.dashboard', compact(
            'totalUsuarios',
            'equiposActivos',
            'eventosProgramados',
            'alertasPendientes',
            'actividadReciente'
        ));
    }
    
    /**
     * Obtener actividad reciente del sistema
     * (Esta es una función de ejemplo, necesitarías implementar un sistema de logs)
     */
    private function obtenerActividadReciente()
    {
        // Por ahora, retornamos datos de ejemplo usando usuarios recientes
        $usuariosRecientes = User::latest()->take(5)->get();
        
        $actividades = collect();
        foreach ($usuariosRecientes as $user) {
            $actividades->push((object)[
                'usuario' => $user,
                'descripcion' => 'Registro de nuevo usuario',
                'created_at' => $user->created_at,
                'estado' => 'completado'
            ]);
        }
        
        return $actividades;
    }
    
    /**
     * Gestionar usuarios
     */
    public function usuarios()
    {
        $usuarios = User::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.usuarios', compact('usuarios'));
    }
    
    /**
     * Gestionar equipos
     */
    public function equipos(Request $request)
{
    // Lógica del buscador
    $query = Equipo::query();

    if ($request->has('buscar')) {
        $busqueda = $request->get('buscar');
        $query->where('nombre', 'LIKE', "%{$busqueda}%")
              ->orWhere('id', 'LIKE', "%{$busqueda}%"); // Asumiendo que 'id' es el número de identificación
    }

    // Obtenemos los equipos paginados (10 por página)
    $equipos = $query->paginate(10);

    return view('admin.equipos', compact('equipos'));
}
    
    /**
     * Aprobar usuarios pendientes
     */
    public function aprobarUsuarios()
    {
        // Aquí iría la lógica para mostrar usuarios pendientes de aprobación
        // Necesitarías agregar un campo 'aprobado' en la tabla users
        return view('admin.usuarios.aprobar');
    }
    
    /**
     * Realizar backup de base de datos
     */
    public function backup()
    {
        // Aquí iría la lógica del backup
        // Podrías usar un paquete como spatie/laravel-backup
        
        return redirect()->route('admin.dashboard')->with('success', 'Backup iniciado correctamente');
    }
    
    /**
     * Gestionar permisos
     */
    public function permisos()
    {
        $roles = ['administrador', 'juez', 'usuario'];
        return view('admin.permisos', compact('roles'));
    }
    
    /**
     * Generar reporte mensual
     */
    public function generarReporte()
    {
        $mes = Carbon::now()->format('F Y');
        
        $datos = [
            'mes' => $mes,
            'nuevosUsuarios' => User::whereMonth('created_at', Carbon::now()->month)->count(),
            'nuevosEquipos' => Equipo::whereMonth('created_at', Carbon::now()->month)->count(),
            'eventosRealizados' => Event::whereMonth('fecha_inicio', Carbon::now()->month)->count(),
        ];
        
        return view('admin.reportes.mensual', compact('datos'));
    }
     public function updateEquipo(Request $request, $id)
{
    // 1. Validación de todos los campos
    $request->validate([
        'nombre'       => 'required|string|max:255',
        'descripcion'  => 'nullable|string',
        'miembros_max' => 'required|integer|min:1',
        'estado'       => 'required|string', 
        'acceso'       => 'required|string', 
        'ubicacion'    => 'nullable|string|max:255',
        'torneo'       => 'nullable|string|max:255',
    ]);

    $equipo = Equipo::findOrFail($id);
    $equipo->nombre       = $request->nombre;
    $equipo->descripcion  = $request->descripcion;
    $equipo->miembros_max = $request->miembros_max;
    $equipo->estado       = $request->estado;
    $equipo->acceso       = $request->acceso;
    $equipo->ubicacion    = $request->ubicacion;
    $equipo->torneo       = $request->torneo;
    $equipo->save();

    return redirect()->route('admin.equipos.index')->with('success', 'Datos del equipo actualizados correctamente');
}

    /**
     * Gestionar Sistema de Logros
     */
    public function logros()
    {
        // Aquí se mostrarán los logros del sistema
        // Por ahora solo retornamos la vista
        return view('admin.logros');
    }

    /**
     * Gestionar Carga de Archivos
     */
    public function archivos()
    {
        // Aquí se mostrará el sistema de carga de archivos
        // Por ahora solo retornamos la vista
        return view('admin.archivos');
    }

    /**
     * Gestionar Evaluaciones
     */
    public function evaluaciones()
    {
        // Aquí se mostrarán todas las evaluaciones del sistema
        // Por ahora solo retornamos la vista
        return view('admin.evaluaciones');
    }

}