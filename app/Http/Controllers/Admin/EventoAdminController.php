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

        // Enviar correo a todos los usuarios sobre el nuevo evento
        try {
            $usuarios = \App\Models\User::where('rol', 'usuario')->get();
            foreach ($usuarios as $usuario) {
                \Illuminate\Support\Facades\Mail::to($usuario->email)
                    ->send(new \App\Mail\NuevoEventoEmail($evento));
            }
        } catch (\Exception $e) {
            // Log error pero no fallar la creación del evento
            \Log::error('Error enviando correos de nuevo evento: ' . $e->getMessage());
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

        // Actualizar criterios de evaluación
        if ($request->has('criterios')) {
            // Eliminar criterios existentes
            $evento->criteriosEvaluacion()->delete();

            // Crear nuevos criterios
            foreach ($request->criterios as $index => $criterio) {
                CriterioEvaluacion::create([
                    'event_id' => $evento->id,
                    'nombre' => $criterio['nombre'],
                    'descripcion' => $criterio['descripcion'] ?? null,
                    'peso' => $criterio['peso'],
                    'orden' => $index,
                ]);
            }
        }

        // Actualizar jueces asignados
        if ($request->has('jueces_asignados')) {
            $evento->juecesAsignados()->sync($request->jueces_asignados);
        }

        return redirect()
            ->route('admin.calendario')
            ->with('success', 'Evento "' . $evento->titulo . '" actualizado exitosamente');
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

    /**
     * Finalizar evento y enviar certificados a los ganadores
     */
    public function finalizar($id)
    {
        try {
            \Log::info("=== INICIANDO FINALIZACIÓN DEL EVENTO ID: {$id} ===");
            
            // Cargar el evento con todas las relaciones necesarias
            $evento = Event::with([
                'equipos.lider',
                'equipos.miembros.usuario',
                'juecesAsignados'
            ])->findOrFail($id);
            
            // Verificar si el evento ya está finalizado
            if ($evento->estaFinalizado()) {
                return redirect()
                    ->route('admin.eventos.index')
                    ->with('error', 'Este evento ya está finalizado.');
            }
            
            \Log::info("Evento cargado: {$evento->titulo}");
            \Log::info("Equipos inscritos: " . $evento->equipos->count());
            
            // Verificar que hay equipos inscritos
            if ($evento->equipos->isEmpty()) {
                \Log::warning("El evento no tiene equipos inscritos");
                return redirect()
                    ->route('admin.eventos.index')
                    ->with('error', 'No se pueden enviar certificados. El evento no tiene equipos inscritos.');
            }
            
            // Obtener ganadores con manejo de empates
            $ganadores = $evento->obtenerGanadoresConEmpates();
            
            \Log::info("Ganadores obtenidos:", [
                'primer_lugar' => count($ganadores['primer_lugar']),
                'segundo_lugar' => count($ganadores['segundo_lugar']),
                'tercer_lugar' => count($ganadores['tercer_lugar'])
            ]);
            
            // Verificar que hay ganadores
            if (empty($ganadores['primer_lugar']) && 
                empty($ganadores['segundo_lugar']) && 
                empty($ganadores['tercer_lugar'])) {
                \Log::warning("No hay ganadores para el evento {$id}");
                
                // Verificar si hay evaluaciones
                $totalEvaluaciones = \App\Models\Evaluacion::where('event_id', $id)->count();
                $evaluacionesCompletadas = \App\Models\Evaluacion::where('event_id', $id)
                    ->where('estado', 'completada')
                    ->count();
                
                \Log::info("Evaluaciones totales: {$totalEvaluaciones}, Completadas: {$evaluacionesCompletadas}");
                
                return redirect()
                    ->route('admin.eventos.index')
                    ->with('error', "No se pueden enviar certificados. El evento tiene {$totalEvaluaciones} evaluaciones totales y {$evaluacionesCompletadas} completadas. Asegúrate de que los jueces hayan completado sus evaluaciones.");
            }
            
            $certificadosGuardados = 0;
            $errores = [];
            
            // Función auxiliar para guardar certificados en la base de datos
            $guardarCertificados = function($ganadores, $lugar) use (&$certificadosGuardados, &$errores, $evento) {
                foreach ($ganadores as $ganador) {
                    $equipo = $ganador['equipo'];
                    $promedio = $ganador['promedio'];
                    
                    // Recargar el equipo con las relaciones necesarias
                    $equipo->load(['lider', 'miembros.usuario']);
                    
                    \Log::info("Procesando equipo: {$equipo->nombre} (ID: {$equipo->id}) para lugar {$lugar}");
                    
                    // Obtener todos los miembros del equipo
                    $miembros = $equipo->todosLosMiembros();
                    
                    \Log::info("Miembros encontrados en equipo {$equipo->nombre}: " . $miembros->count());
                    
                    if ($miembros->isEmpty()) {
                        \Log::warning("El equipo {$equipo->nombre} no tiene miembros");
                        $errores[] = "El equipo '{$equipo->nombre}' no tiene miembros registrados";
                        continue;
                    }
                    
                    foreach ($miembros as $miembro) {
                        if (!$miembro) {
                            \Log::warning("Miembro nulo encontrado en equipo {$equipo->nombre}");
                            continue;
                        }
                        
                        try {
                            \Log::info("Guardando certificado para {$miembro->name} (ID: {$miembro->id}) para lugar {$lugar}");
                            
                            // Verificar si el certificado ya existe
                            $certificadoExistente = \App\Models\Certificado::where('user_id', $miembro->id)
                                ->where('equipo_id', $equipo->id)
                                ->where('event_id', $evento->id)
                                ->where('lugar', $lugar)
                                ->first();
                            
                            if ($certificadoExistente) {
                                \Log::info("Certificado ya existe para {$miembro->name}, actualizando...");
                                $certificadoExistente->update([
                                    'promedio' => $promedio
                                ]);
                            } else {
                                // Crear nuevo certificado
                                \App\Models\Certificado::create([
                                    'user_id' => $miembro->id,
                                    'equipo_id' => $equipo->id,
                                    'event_id' => $evento->id,
                                    'lugar' => $lugar,
                                    'promedio' => $promedio,
                                ]);
                            }
                            
                            $certificadosGuardados++;
                            \Log::info("Certificado guardado exitosamente para {$miembro->name}");
                        } catch (\Exception $e) {
                            $errorMsg = "Error guardando certificado para {$miembro->name}: " . $e->getMessage();
                            $errores[] = $errorMsg;
                            \Log::error($errorMsg);
                            \Log::error("Stack trace: " . $e->getTraceAsString());
                        }
                    }
                }
            };
            
            // Guardar certificados a primer lugar
            if (!empty($ganadores['primer_lugar'])) {
                $guardarCertificados($ganadores['primer_lugar'], 1);
            }
            
            // Guardar certificados a segundo lugar
            if (!empty($ganadores['segundo_lugar'])) {
                $guardarCertificados($ganadores['segundo_lugar'], 2);
            }
            
            // Guardar certificados a tercer lugar
            if (!empty($ganadores['tercer_lugar'])) {
                $guardarCertificados($ganadores['tercer_lugar'], 3);
            }
            
            // Asignar insignias a los ganadores
            try {
                $evento->asignarInsignias();
            } catch (\Exception $e) {
                \Log::error("Error asignando insignias: " . $e->getMessage());
            }
            
            // Enviar certificados por correo electrónico a los ganadores del podio
            try {
                \Log::info("Enviando certificados por correo a los ganadores del podio...");
                $resultadoEnvio = $evento->enviarCertificadosGanadores();
                
                \Log::info("Resultado del envío de certificados:", [
                    'enviados' => $resultadoEnvio['certificados_enviados'],
                    'omitidos' => $resultadoEnvio['certificados_omitidos'],
                    'errores' => $resultadoEnvio['total_errores']
                ]);
                
                if ($resultadoEnvio['total_errores'] > 0) {
                    \Log::warning("Errores al enviar certificados por correo: " . implode('; ', $resultadoEnvio['errores']));
                }
            } catch (\Exception $e) {
                \Log::error("Error enviando certificados por correo: " . $e->getMessage());
                \Log::error("Stack trace: " . $e->getTraceAsString());
                // No fallar el proceso si hay error al enviar correos
            }
            
            $mensaje = "Evento finalizado exitosamente. Se guardaron {$certificadosGuardados} certificados para los ganadores.";
            if (!empty($errores)) {
                $mensaje .= " Hubo " . count($errores) . " errores al guardar algunos certificados.";
                \Log::warning("Errores al guardar certificados: " . implode('; ', $errores));
            }
            $mensaje .= " Los certificados PDF se están enviando por correo electrónico a los integrantes de los equipos ganadores.";
            
            // Marcar el evento como finalizado
            $evento->finalizado_at = now();
            $evento->save();
            
            // Limpiar el event_id de todos los equipos inscritos en este evento
            // Esto permite que los equipos puedan inscribirse a otros eventos
            $equiposInscritos = $evento->equipos;
            foreach ($equiposInscritos as $equipo) {
                $equipo->event_id = null;
                $equipo->save();
            }
            
            \Log::info("Finalización del evento completada. Certificados guardados: {$certificadosGuardados}. Equipos liberados: " . $equiposInscritos->count());
            
            return redirect()
                ->route('admin.eventos.index')
                ->with('success', $mensaje);
                
        } catch (\Exception $e) {
            \Log::error("Error al finalizar evento: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            return redirect()
                ->route('admin.eventos.index')
                ->with('error', 'Error al finalizar el evento: ' . $e->getMessage());
        }
    }
}