<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Models\CriterioEvaluacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventoAdminController extends Controller
{
    /**
     * Mostrar formulario de crear evento
     */
    public function create()
    {
        // Obtener todos los jueces disponibles
        $jueces = User::where('rol', 'juez')->orderBy('name')->get();

        return view('admin.eventos.create', compact('jueces'));
    }
    
    /**
     * Guardar nuevo evento (ADAPTADO A TU ESTRUCTURA)
     */
    public function store(Request $request)
    {
        // Validar datos
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion_corta' => 'required|string|max:200',
            'descripcion_larga' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_limite_inscripcion' => 'required|date|before_or_equal:fecha_inicio',
            'ubicacion' => 'required|string|max:255',
            'organizacion' => 'required|string|max:255',
            'org_icon' => 'nullable|string|max:100',
            'participantes_max' => 'required|integer|min:1',
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'premio_1' => 'nullable|string|max:255',
            'premio_2' => 'nullable|string|max:255',
            'premio_3' => 'nullable|string|max:255',
            'requisitos' => 'required|string',
            'cronograma' => 'nullable|string',
            'jueces' => 'nullable|string',
            'criterios' => 'required|array|min:1',
            'criterios.*.nombre' => 'required|string|max:255',
            'criterios.*.descripcion' => 'nullable|string',
            'criterios.*.peso' => 'required|integer|min:1|max:10',
            'jueces_asignados' => 'nullable|array',
            'jueces_asignados.*' => 'exists:users,id',
        ], [
            'titulo.required' => 'El nombre del evento es obligatorio',
            'descripcion_corta.required' => 'La descripción breve es obligatoria',
            'descripcion_corta.max' => 'La descripción breve no debe superar 200 caracteres',
            'descripcion_larga.required' => 'La descripción completa es obligatoria',
            'fecha_inicio.required' => 'La fecha del evento es obligatoria',
            'fecha_limite_inscripcion.required' => 'La fecha límite de inscripción es obligatoria',
            'fecha_limite_inscripcion.before_or_equal' => 'La fecha límite debe ser antes o igual a la fecha del evento',
            'ubicacion.required' => 'La ubicación es obligatoria',
            'organizacion.required' => 'La organización es obligatoria',
            'participantes_max.required' => 'El límite de participantes es obligatorio',
            'participantes_max.min' => 'Debe haber al menos 1 participante',
            'imagen.required' => 'La imagen del evento es obligatoria',
            'imagen.image' => 'El archivo debe ser una imagen',
            'imagen.max' => 'La imagen no debe superar los 2MB',
            'requisitos.required' => 'Los requisitos son obligatorios',
            'jueces_asignados.required' => 'Debe asignar al menos un juez al evento',
        ]);
        
        // Procesar imagen (REQUERIDA)
        $imagenPath = $request->file('imagen')->store('eventos', 'public');
        
        // Procesar premios como array asociativo: {1: "$5,000", 2: "$3,000", 3: "$1,000"}
        $premios = [];
        if ($request->premio_1) $premios['1'] = $request->premio_1;
        if ($request->premio_2) $premios['2'] = $request->premio_2;
        if ($request->premio_3) $premios['3'] = $request->premio_3;
        
        // Procesar requisitos: convertir texto con saltos de línea a array
        $requisitosArray = array_filter(
            array_map('trim', explode("\n", $request->requisitos)),
            function($item) { return !empty($item); }
        );
        
        // Procesar cronograma: "10:00 - Actividad" → [{hora: "10:00", actividad: "Actividad"}]
        $cronogramaArray = [];
        if ($request->filled('cronograma')) {
            $cronogramaLineas = array_filter(
                array_map('trim', explode("\n", $request->cronograma)),
                function($item) { return !empty($item); }
            );

            foreach ($cronogramaLineas as $linea) {
                if (preg_match('/^(\d{1,2}:\d{2})\s*-\s*(.+)$/', $linea, $matches)) {
                    $cronogramaArray[] = [
                        'hora' => $matches[1],
                        'actividad' => $matches[2]
                    ];
                } else {
                    // Si no cumple el formato, mostrar error amigable
                    return back()->withErrors([
                        'cronograma' => 'El cronograma debe tener el formato: HH:MM - Actividad. Error en línea: "' . $linea . '"'
                    ])->withInput();
                }
            }
        }

        // Procesar jueces: "Nombre | Cargo | Tags" → [{nombre: "...", rol: "...", tags: [...]}]
        $juecesArray = [];
        if ($request->filled('jueces')) {
            $juecesLineas = array_filter(
                array_map('trim', explode("\n", $request->jueces)),
                function($item) { return !empty($item); }
            );

            foreach ($juecesLineas as $linea) {
                $partes = array_map('trim', explode('|', $linea));
                if (count($partes) >= 3) {
                    $tags = array_map('trim', explode(',', $partes[2]));
                    $juecesArray[] = [
                        'nombre' => $partes[0],
                        'rol' => $partes[1],
                        'tags' => $tags
                    ];
                } else {
                    // Si no cumple el formato, mostrar error amigable
                    return back()->withErrors([
                        'jueces' => 'Los jueces deben tener el formato: Nombre | Cargo | Especialidades. Error en línea: "' . $linea . '"'
                    ])->withInput();
                }
            }
        }
        
        // Crear evento
        $evento = Event::create([
            'titulo' => $validated['titulo'],
            'descripcion_corta' => $validated['descripcion_corta'],
            'descripcion_larga' => $validated['descripcion_larga'],
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_limite_inscripcion' => $validated['fecha_limite_inscripcion'],
            'ubicacion' => $validated['ubicacion'],
            'organizacion' => $validated['organizacion'],
            'org_icon' => $validated['org_icon'] ?? 'fa-brands fa-google',
            'participantes_max' => $validated['participantes_max'],
            'participantes_actuales' => 0,
            'imagen' => $imagenPath,
            'premios' => $premios,
            'requisitos' => $requisitosArray,
            'cronograma' => $cronogramaArray,
            'jueces' => $juecesArray,
        ]);

        // Guardar criterios de evaluación
        if (isset($validated['criterios'])) {
            foreach ($validated['criterios'] as $index => $criterio) {
                CriterioEvaluacion::create([
                    'event_id' => $evento->id,
                    'nombre' => $criterio['nombre'],
                    'descripcion' => $criterio['descripcion'] ?? null,
                    'peso' => $criterio['peso'],
                    'orden' => $index,
                ]);
            }
        }

        // Asignar jueces al evento
        if (isset($validated['jueces_asignados']) && is_array($validated['jueces_asignados'])) {
            $evento->juecesAsignados()->attach($validated['jueces_asignados']);
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Evento "' . $evento->titulo . '" creado exitosamente');
    }
    
    /**
     * Listar todos los eventos (para gestión)
     */
    public function index()
    {
        $eventos = Event::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.eventos.index', compact('eventos'));
    }
    
    /**
     * Mostrar formulario de editar evento
     */
    public function edit($id)
    {
        $evento = Event::with('criteriosEvaluacion', 'juecesAsignados')->findOrFail($id);

        // Obtener todos los jueces disponibles
        $jueces = User::where('rol', 'juez')->orderBy('name')->get();

        // Convertir arrays de vuelta a texto para el formulario
        $evento->requisitos_texto = implode("\n", $evento->requisitos ?? []);

        $cronograma_texto = [];
        foreach ($evento->cronograma ?? [] as $item) {
            $cronograma_texto[] = $item['hora'] . ' - ' . $item['actividad'];
        }
        $evento->cronograma_texto = implode("\n", $cronograma_texto);

        $jueces_texto = [];
        foreach ($evento->jueces ?? [] as $juez) {
            $tags = implode(', ', $juez['tags'] ?? []);
            $jueces_texto[] = $juez['nombre'] . ' | ' . $juez['rol'] . ' | ' . $tags;
        }
        $evento->jueces_texto = implode("\n", $jueces_texto);

        return view('admin.eventos.edit', compact('evento', 'jueces'));
    }
    
    /**
     * Actualizar evento
     */
    public function update(Request $request, $id)
    {
        $evento = Event::findOrFail($id);
        
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion_corta' => 'required|string|max:200',
            'descripcion_larga' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_limite_inscripcion' => 'required|date|before_or_equal:fecha_inicio',
            'ubicacion' => 'required|string|max:255',
            'organizacion' => 'required|string|max:255',
            'org_icon' => 'nullable|string|max:100',
            'participantes_max' => 'required|integer|min:1',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // Actualizar imagen si se sube una nueva
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior
            if ($evento->imagen) {
                Storage::disk('public')->delete($evento->imagen);
            }
            $validated['imagen'] = $request->file('imagen')->store('eventos', 'public');
        }
        
        // Procesar premios
        $premios = [];
        if ($request->premio_1) $premios['1'] = $request->premio_1;
        if ($request->premio_2) $premios['2'] = $request->premio_2;
        if ($request->premio_3) $premios['3'] = $request->premio_3;
        $validated['premios'] = $premios;
        
        // Procesar requisitos
        $validated['requisitos'] = array_filter(
            array_map('trim', explode("\n", $request->requisitos)),
            function($item) { return !empty($item); }
        );
        
        // Procesar cronograma
        $cronogramaArray = [];
        $cronogramaLineas = array_filter(
            array_map('trim', explode("\n", $request->cronograma)),
            function($item) { return !empty($item); }
        );
        foreach ($cronogramaLineas as $linea) {
            if (preg_match('/^(\d{2}:\d{2})\s*-\s*(.+)$/', $linea, $matches)) {
                $cronogramaArray[] = [
                    'hora' => $matches[1],
                    'actividad' => $matches[2]
                ];
            }
        }
        $validated['cronograma'] = $cronogramaArray;
        
        // Procesar jueces
        $juecesArray = [];
        $juecesLineas = array_filter(
            array_map('trim', explode("\n", $request->jueces)),
            function($item) { return !empty($item); }
        );
        foreach ($juecesLineas as $linea) {
            $partes = array_map('trim', explode('|', $linea));
            if (count($partes) >= 3) {
                $tags = array_map('trim', explode(',', $partes[2]));
                $juecesArray[] = [
                    'nombre' => $partes[0],
                    'rol' => $partes[1],
                    'tags' => $tags
                ];
            }
        }
        $validated['jueces'] = $juecesArray;
        
        $evento->update($validated);
        
        return redirect()
            ->route('admin.eventos.index')
            ->with('success', 'Evento actualizado exitosamente');
    }
    
    /**
     * Eliminar evento
     */
    public function destroy($id)
    {
        $evento = Event::findOrFail($id);
        
        // Eliminar imagen asociada
        if ($evento->imagen) {
            Storage::disk('public')->delete($evento->imagen);
        }
        
        $evento->delete();
        
        return redirect()
            ->route('admin.eventos.index')
            ->with('success', 'Evento eliminado exitosamente');
    }
}