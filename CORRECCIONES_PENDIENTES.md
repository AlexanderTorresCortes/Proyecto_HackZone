# 🔧 Correcciones Pendientes - Guía Completa

## ⚠️ PASO 1: Instalar Dependencias (OBLIGATORIO)

**Ejecuta primero estos comandos:**

```bash
composer require maatwebsite/excel barryvdh/dompdf
```

---

## 📝 Problemas a Resolver

### 1. ✅ Botón para cambiar acceso de equipo (Público ↔ Privado)
### 2. ✅ Quitar selección de torneo al crear equipo
### 3. ✅ Validar eventos finalizados
### 4. ✅ Permisos de descarga para jueces
### 5. ✅ Problema de calificación de jueces
### 6. ✅ Paginación de equipos en admin

---

## 🔨 Correcciones Detalladas

### 1️⃣ Agregar método para cambiar acceso de equipo

**Archivo:** `app/Http/Controllers/EquiposController.php`

Agregar este método al final de la clase (antes del último `}`):

```php
/**
 * Cambiar el acceso del equipo entre Público y Privado
 */
public function cambiarAcceso($id)
{
    $equipo = Equipo::findOrFail($id);

    // Verificar que el usuario sea el líder
    if ($equipo->lider_id !== Auth::id()) {
        return redirect()->back()->with('error', 'Solo el líder puede cambiar el acceso del equipo');
    }

    // Toggle acceso
    $equipo->acceso = ($equipo->acceso === 'Público') ? 'Privado' : 'Público';
    $equipo->save();

    return redirect()->back()->with('success', 'Acceso del equipo cambiado a: ' . $equipo->acceso);
}
```

**Archivo:** `routes/web.php`

Busca la sección de rutas de equipos y agrega:

```php
// Dentro del grupo de rutas autenticadas de equipos
Route::post('/equipos/{id}/cambiar-acceso', [EquiposController::class, 'cambiarAcceso'])->name('equipos.cambiarAcceso');
```

**Archivo:** `resources/views/equipos/show.blade.php`

Busca donde se muestra el acceso del equipo y agrega el botón. Busca algo como:

```html
<p>Acceso: {{ $equipo->acceso }}</p>
```

Y reemplázalo por:

```html
<div style="display: flex; align-items: center; gap: 1rem;">
    <p style="margin: 0;">
        <strong>Acceso:</strong>
        <span style="padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem;
                     background: {{ $equipo->acceso === 'Público' ? '#dbeafe' : '#fef3c7' }};
                     color: {{ $equipo->acceso === 'Público' ? '#1e40af' : '#92400e' }};">
            {{ $equipo->acceso }}
        </span>
    </p>

    @if(Auth::id() === $equipo->lider_id)
        <form action="{{ route('equipos.cambiarAcceso', $equipo->id) }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit"
                    style="background: #667eea; color: white; border: none; padding: 0.5rem 1rem;
                           border-radius: 6px; cursor: pointer; font-size: 0.85rem;">
                <i class="fas fa-exchange-alt"></i>
                Cambiar a {{ $equipo->acceso === 'Público' ? 'Privado' : 'Público' }}
            </button>
        </form>
    @endif
</div>
```

---

### 2️⃣ Quitar selección de torneo al crear equipo

**Archivo:** `app/Http/Controllers/EquiposController.php`

En el método `store()`, busca la validación y **QUITA** la línea del torneo:

```php
// ANTES (QUITAR ESTA LÍNEA):
'torneo' => 'required|string|exists:events,titulo',

// DESPUÉS (Validación sin torneo):
$request->validate([
    'nombre' => 'required|string|max:255',
    'descripcion' => 'required|string',
    'ubicacion' => 'required|string',
    'acceso' => 'required|string|in:Público,Privado',
]);
```

Luego, donde se crea el equipo, **QUITA** la asignación del torneo:

```php
// ANTES (QUITAR):
'torneo' => $request->torneo,

// DESPUÉS (Sin torneo):
$equipo = Equipo::create([
    'nombre' => $request->nombre,
    'descripcion' => $request->descripcion,
    'lider_id' => Auth::id(),
    'ubicacion' => $request->ubicacion,
    // 'torneo' => $request->torneo,  ← QUITAR ESTA LÍNEA
    'acceso' => $request->acceso,
    'estado' => 'Reclutando',
]);
```

**Archivo:** Vista de crear equipo (busca el formulario de creación)

Busca y **ELIMINA** todo el select/input del torneo:

```html
<!-- ELIMINAR ESTA SECCIÓN COMPLETA: -->
<div class="form-group">
    <label for="torneo">Torneo</label>
    <select name="torneo" ...>
        ...
    </select>
</div>
```

---

### 3️⃣ Validar eventos finalizados NO permitan inscripción

**Archivo:** `app/Http/Controllers/EventosController.php`

En el método `inscribir()`, agregar validación:

```php
public function inscribir(Request $request, $id)
{
    $evento = Event::findOrFail($id);

    // ✅ AGREGAR ESTA VALIDACIÓN
    if ($evento->fecha_limite_inscripcion < now()) {
        return redirect()->back()->with('error', 'Lo sentimos, este evento ya ha finalizado su periodo de inscripción.');
    }

    // ... resto del código
}
```

**Archivo:** `resources/views/eventos/show.blade.php`

Busca el botón de inscripción y envuélvelo en una condición:

```php
@php
    $inscripcionAbierta = $evento->fecha_limite_inscripcion >= now();
@endphp

@if($inscripcionAbierta)
    <!-- Botón de inscripción normal -->
    <button onclick="mostrarModalInscripcion()" class="btn-inscribir">
        Inscribirse al Evento
    </button>
@else
    <!-- Mensaje de evento finalizado -->
    <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; text-align: center;">
        <i class="fas fa-exclamation-circle"></i>
        <strong>Inscripciones Cerradas</strong><br>
        Este evento ya ha finalizado su periodo de inscripción.
    </div>
@endif
```

---

### 4️⃣ Arreglar permisos de descarga para jueces

**Archivo:** `app/Http/Controllers/Usuario/EntregasController.php`

Busca el método `download()` y modifica la verificación de permisos:

```php
public function download($id)
{
    $entrega = Entrega::findOrFail($id);

    // ✅ PERMITIR DESCARGA A JUECES Y AL EQUIPO DUEÑO
    $esJuez = Auth::user()->rol === 'juez';
    $esMiembroDelEquipo = $entrega->equipo->miembros->contains(Auth::id()) ||
                          $entrega->equipo->lider_id === Auth::id();
    $esAdmin = Auth::user()->rol === 'administrador';

    if (!$esJuez && !$esMiembroDelEquipo && !$esAdmin) {
        abort(403, 'No tienes permiso para descargar este archivo');
    }

    // Descargar archivo
    return Storage::disk('public')->download($entrega->archivo_path, $entrega->nombre_original);
}
```

**O si no existe el método, agregarlo:**

```php
public function download($id)
{
    $entrega = \App\Models\Entrega::findOrFail($id);

    // Verificar permisos
    $esJuez = Auth::user()->rol === 'juez';
    $esMiembroDelEquipo = $entrega->equipo && (
        $entrega->equipo->miembros->contains(Auth::id()) ||
        $entrega->equipo->lider_id === Auth::id()
    );
    $esAdmin = Auth::user()->rol === 'administrador';

    if (!$esJuez && !$esMiembroDelEquipo && !$esAdmin) {
        abort(403, 'No tienes permiso para descargar este archivo');
    }

    return \Storage::disk('public')->download($entrega->archivo_path, $entrega->nombre_original ?? 'archivo');
}
```

---

### 5️⃣ Arreglar problema de calificación de jueces

**Archivo:** `app/Http/Controllers/Juez/JuezDashboardController.php`

Busca el método `guardarEvaluacion()` y verifica que:

```php
public function guardarEvaluacion(Request $request, $eventoId, $equipoId)
{
    // Validación
    $request->validate([
        'puntuaciones' => 'required|array',
        'puntuaciones.*' => 'required|integer|min:0|max:10',
        'comentarios' => 'nullable|string',
    ]);

    // ✅ VERIFICAR QUE EXISTAN LOS CRITERIOS
    $evento = Event::with('criteriosEvaluacion')->findOrFail($eventoId);

    if ($evento->criteriosEvaluacion->isEmpty()) {
        return redirect()->back()->with('error', 'Este evento no tiene criterios de evaluación configurados');
    }

    // Crear evaluación
    $evaluacion = \App\Models\Evaluacion::create([
        'event_id' => $eventoId,
        'equipo_id' => $equipoId,
        'juez_id' => Auth::id(),
        'puntuaciones' => $request->puntuaciones,
        'comentarios' => $request->comentarios,
    ]);

    return redirect()->route('juez.equipos', $eventoId)
        ->with('success', 'Evaluación registrada correctamente');
}
```

**Verificar que la tabla `evaluaciones` tenga la estructura correcta:**

```bash
php artisan tinker

# Verificar estructura
\Schema::getColumnListing('evaluaciones');

# Debe tener: id, event_id, equipo_id, juez_id, puntuaciones, comentarios, created_at, updated_at
```

---

### 6️⃣ Arreglar paginación de equipos en admin

**Archivo:** `app/Http/Controllers/Admin/AdminDashboardController.php`

En el método `equipos()`, asegúrate que use `paginate()`:

```php
public function equipos(Request $request)
{
    $query = Equipo::with('lider', 'miembros');

    if ($request->has('buscar')) {
        $busqueda = $request->get('buscar');
        $query->where('nombre', 'LIKE', "%{$busqueda}%")
              ->orWhere('id', 'LIKE', "%{$busqueda}%");
    }

    $equipos = $query->orderBy('created_at', 'desc')->paginate(10);

    return view('admin.equipos', compact('equipos'));
}
```

**Archivo:** `resources/views/admin/equipos.blade.php`

Al final de la vista, agregar la paginación:

```html
<!-- Al final, después de la tabla -->
<div style="margin-top: 2rem; display: flex; justify-content: center;">
    {{ $equipos->links() }}
</div>
```

---

## ✅ Checklist de Implementación

- [ ] Instalar dependencias: `composer require maatwebsite/excel barryvdh/dompdf`
- [ ] Agregar método `cambiarAcceso()` en EquiposController
- [ ] Agregar ruta para cambiar acceso
- [ ] Agregar botón en vista de equipo
- [ ] Quitar validación de torneo en `store()`
- [ ] Quitar campo torneo del formulario
- [ ] Agregar validación de fecha en `inscribir()`
- [ ] Agregar mensaje de evento finalizado en vista
- [ ] Modificar permisos en `download()`
- [ ] Verificar método `guardarEvaluacion()`
- [ ] Asegurar paginación en equipos admin
- [ ] Agregar links de paginación en vista

---

## 🧪 Probar Después de Aplicar

```bash
# 1. Limpiar caché
php artisan optimize:clear

# 2. Probar exportaciones
# - Ir a admin/equipos
# - Clic en "Exportar Excel"
# - Clic en "Exportar PDF"

# 3. Probar cambio de acceso
# - Ir a un equipo donde seas líder
# - Clic en "Cambiar a Privado/Público"

# 4. Probar inscripción
# - Ir a un evento finalizado
# - Verificar que NO permita inscribirse

# 5. Probar descarga como juez
# - Entrar como juez
# - Ir a entregas
# - Descargar archivo

# 6. Probar calificación como juez
# - Entrar como juez
# - Evaluar un equipo
# - Verificar que se guarde correctamente
```

---

**¿Necesitas ayuda con alguna corrección específica? ¡Avísame!**
