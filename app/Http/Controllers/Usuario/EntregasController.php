<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Entrega;
use App\Models\Equipo;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EntregasController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();

        $equiposComoLider = Equipo::where('user_id', $usuario->id)
            ->with(['evento', 'entregas' => function($query) {
                $query->orderBy('version', 'desc');
            }])
            ->get();

        $equiposComoMiembro = $usuario->equiposComoMiembro()
            ->with(['equipo.evento', 'equipo.entregas' => function($query) {
                $query->orderBy('version', 'desc');
            }])
            ->get();

        return view('usuario.entregas', compact('equiposComoLider', 'equiposComoMiembro'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'equipo_id' => 'required|exists:equipos,id',
            'archivo' => 'required|file|mimes:zip,pdf,pptx|max:51200'
        ]);

        $equipo = Equipo::findOrFail($request->equipo_id);

        if ($equipo->user_id !== Auth::id()) {
            return back()->with('error', 'Solo el líder del equipo puede subir archivos.');
        }

        if (!$equipo->event_id) {
            return back()->with('error', 'El equipo no está inscrito en ningún evento.');
        }

        $archivo = $request->file('archivo');

        $ultimaEntrega = Entrega::where('equipo_id', $equipo->id)
            ->where('event_id', $equipo->event_id)
            ->orderBy('version', 'desc')
            ->first();

        $nuevaVersion = $ultimaEntrega ? $ultimaEntrega->version + 1 : 1;

        $nombreArchivo = $equipo->nombre . '_v' . $nuevaVersion . '_' . time() . '.' . $archivo->getClientOriginalExtension();
        $ruta = $archivo->storeAs('entregas/' . $equipo->event_id, $nombreArchivo, 'public');

        Entrega::create([
            'equipo_id' => $equipo->id,
            'event_id' => $equipo->event_id,
            'user_id' => Auth::id(),
            'nombre_archivo' => $archivo->getClientOriginalName(),
            'ruta_archivo' => $ruta,
            'tipo_archivo' => $archivo->getClientOriginalExtension(),
            'tamaño' => $archivo->getSize(),
            'version' => $nuevaVersion,
            'estado' => 'pendiente'
        ]);

        return back()->with('success', 'Archivo subido correctamente (Versión ' . $nuevaVersion . ')');
    }

    public function download($id)
    {
        $entrega = Entrega::findOrFail($id);

        $equipo = $entrega->equipo;
        if ($equipo->user_id !== Auth::id() && !$equipo->miembros->contains('user_id', Auth::id())) {
            abort(403, 'No tienes permiso para descargar este archivo.');
        }

        if (!Storage::disk('public')->exists($entrega->ruta_archivo)) {
            return back()->with('error', 'El archivo no existe.');
        }

        return Storage::disk('public')->download($entrega->ruta_archivo, $entrega->nombre_archivo);
    }

    public function destroy($id)
    {
        $entrega = Entrega::findOrFail($id);

        if ($entrega->equipo->user_id !== Auth::id()) {
            return back()->with('error', 'Solo el líder del equipo puede eliminar archivos.');
        }

        if (Storage::disk('public')->exists($entrega->ruta_archivo)) {
            Storage::disk('public')->delete($entrega->ruta_archivo);
        }

        $entrega->delete();

        return back()->with('success', 'Archivo eliminado correctamente.');
    }
}
