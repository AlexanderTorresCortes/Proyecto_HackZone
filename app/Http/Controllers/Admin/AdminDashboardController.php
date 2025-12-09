<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Equipo;
use App\Models\Event;
use Carbon\Carbon;
use App\Exports\UsuariosExport;
use App\Exports\EquiposExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;


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
        $usuarios = User::orderBy('rol')->orderBy('name')->get();
        return view('admin.permisos', compact('usuarios'));
    }

    /**
     * Asignar rol de juez a un usuario
     */
    public function asignarJuez(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|exists:users,id',
        ]);

        $usuario = User::findOrFail($request->usuario_id);

        if ($usuario->rol === 'administrador') {
            return redirect()->route('admin.permisos')
                ->with('error', 'No se puede cambiar el rol de un administrador');
        }

        $usuario->rol = 'juez';
        $usuario->save();

        return redirect()->route('admin.permisos')
            ->with('success', 'Usuario "' . $usuario->name . '" ahora es un Juez');
    }

    /**
     * Cambiar rol de un usuario
     */
    public function cambiarRol(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|exists:users,id',
            'nuevo_rol' => 'required|in:usuario,juez',
        ]);

        $usuario = User::findOrFail($request->usuario_id);

        if ($usuario->rol === 'administrador') {
            return redirect()->route('admin.permisos')
                ->with('error', 'No se puede cambiar el rol de un administrador');
        }

        $rolAnterior = $usuario->rol;
        $usuario->rol = $request->nuevo_rol;
        $usuario->save();

        return redirect()->route('admin.permisos')
            ->with('success', 'Rol de "' . $usuario->name . '" cambiado de ' . ucfirst($rolAnterior) . ' a ' . ucfirst($request->nuevo_rol));
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
        $entregas = \App\Models\Entrega::with(['equipo', 'evento', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.archivos', compact('entregas'));
    }

    /**
     * Gestionar Evaluaciones
     */
    public function evaluaciones()
    {
        $eventos = Event::with(['evaluaciones.equipo', 'evaluaciones.juez', 'juecesAsignados'])
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        // Calcular estadísticas de cada evento
        $eventosConEstadisticas = $eventos->map(function($evento) {
            $evento->estadisticas = $evento->getEstadisticasEvaluaciones();
            return $evento;
        });

        return view('admin.evaluaciones', compact('eventosConEstadisticas'));
    }

    /**
     * Ver ranking de un evento específico
     */
    public function verRanking($eventoId)
    {
        $evento = Event::with(['criteriosEvaluacion', 'juecesAsignados'])->findOrFail($eventoId);
        $ranking = $evento->calcularRanking();
        $primerosLugares = $evento->getPrimerosLugares();
        $estadisticas = $evento->getEstadisticasEvaluaciones();

        // Asignar insignias a los ganadores
        if (count($ranking) > 0) {
            $evento->asignarInsignias();
        }

        return view('admin.ranking', compact('evento', 'ranking', 'primerosLugares', 'estadisticas'));
    }

    /**
     * Exportar usuarios a Excel
     */
    public function exportarUsuariosExcel()
    {
        return Excel::download(new UsuariosExport, 'usuarios_' . date('Y-m-d_His') . '.xlsx');
    }

    /**
     * Exportar usuarios a PDF
     */
    public function exportarUsuariosPDF()
    {
        $usuarios = User::orderBy('created_at', 'desc')->get();
        
        $pdf = PDF::loadView('admin.exports.usuarios-pdf', compact('usuarios'));
        return $pdf->download('usuarios_' . date('Y-m-d_His') . '.pdf');
    }

    /**
     * Exportar equipos a Excel
     */
    public function exportarEquiposExcel()
    {
        return Excel::download(new EquiposExport, 'equipos_' . date('Y-m-d_His') . '.xlsx');
    }

    /**
     * Exportar equipos a PDF
     */
    public function exportarEquiposPDF()
    {
        $equipos = Equipo::orderBy('created_at', 'desc')->get();
        
        $pdf = PDF::loadView('admin.exports.equipos-pdf', compact('equipos'));
        return $pdf->download('equipos_' . date('Y-m-d_His') . '.pdf');
    }

}