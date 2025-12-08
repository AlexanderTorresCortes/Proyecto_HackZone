<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Evento - HackZone Admin</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/crear-evento.css') }}">
</head>
<body>

@include('components.navbar-admin')

<div class="admin-container">
    @include('components.sidebar-admin')

    <!-- Contenido Principal -->
    <main class="admin-main">
        <div class="form-container">
            <h1 class="form-title">Crear Nuevo Evento</h1>
            
            @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
            @endif
            
            @if($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <form action="{{ route('admin.eventos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Nombre del evento -->
                <div class="form-group">
                    <label for="nombre">Nombre del evento:</label>
                    <input type="text" 
                           id="nombre" 
                           name="titulo" 
                           class="form-control" 
                           placeholder="Ej: Hackathón NASA 2025"
                           value="{{ old('titulo') }}"
                           required>
                </div>
                
                <!-- Descripción corta -->
                <div class="form-group">
                    <label for="descripcion_corta">Descripción breve:</label>
                    <textarea id="descripcion_corta" 
                              name="descripcion_corta" 
                              class="form-control form-textarea" 
                              rows="3"
                              placeholder="Descripción breve que aparecerá en la tarjeta del evento (máx 200 caracteres)"
                              maxlength="200"
                              required>{{ old('descripcion_corta') }}</textarea>
                </div>
                
                <!-- Descripción larga -->
                <div class="form-group">
                    <label for="descripcion_larga">Información completa del evento:</label>
                    <textarea id="descripcion_larga" 
                              name="descripcion_larga" 
                              class="form-control form-textarea" 
                              rows="6"
                              placeholder="Describe el evento, objetivos, temas, metodología, etc."
                              required>{{ old('descripcion_larga') }}</textarea>
                </div>
                
                <!-- Imagen de promoción -->
                <div class="form-group">
                    <label for="imagen">Imagen de promoción evento: <span style="color: red;">*</span></label>
                    <div class="file-upload-wrapper">
                        <input type="file"
                               id="imagen"
                               name="imagen"
                               class="file-input"
                               accept="image/*"
                               onchange="previewImage(event)"
                               required>
                        <label for="imagen" class="file-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span id="file-label-text">Seleccionar imagen</span>
                        </label>
                        <div id="imagePreview" class="image-preview"></div>
                    </div>
                    @error('imagen')
                        <small style="color: red; display: block; margin-top: 0.5rem;">{{ $message }}</small>
                    @enderror
                    <small class="form-hint">Formatos permitidos: JPG, PNG, GIF (Máx. 2MB)</small>
                </div>
                
                <!-- Fechas -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="fecha_inicio">
                            <i class="far fa-calendar"></i> Fecha del evento
                        </label>
                        <input type="date" 
                               id="fecha_inicio" 
                               name="fecha_inicio" 
                               class="form-control"
                               value="{{ old('fecha_inicio') }}"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="fecha_limite_inscripcion">
                            <i class="far fa-calendar"></i> Límite de inscripción
                        </label>
                        <input type="date" 
                               id="fecha_limite_inscripcion" 
                               name="fecha_limite_inscripcion" 
                               class="form-control"
                               value="{{ old('fecha_limite_inscripcion') }}"
                               required>
                    </div>
                </div>
                
                <!-- Ubicación y Organización -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="ubicacion">
                            <i class="fas fa-map-marker-alt"></i> Ubicación
                        </label>
                        <input type="text" 
                               id="ubicacion" 
                               name="ubicacion" 
                               class="form-control" 
                               placeholder="Ciudad, País o Virtual"
                               value="{{ old('ubicacion', 'Virtual') }}"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="organizacion">
                            <i class="fas fa-building"></i> Organización
                        </label>
                        <input type="text" 
                               id="organizacion" 
                               name="organizacion" 
                               class="form-control" 
                               placeholder="Nombre de la organización"
                               value="{{ old('organizacion', 'HackZone') }}"
                               required>
                    </div>
                </div>
                
                <!-- Icono de organización y límite de participantes -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="org_icon">
                            <i class="fas fa-icons"></i> Icono de organización (Font Awesome)
                        </label>
                        <input type="text" 
                               id="org_icon" 
                               name="org_icon" 
                               class="form-control" 
                               placeholder="fa-brands fa-google"
                               value="{{ old('org_icon', 'fa-brands fa-google') }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="participantes_max">
                            <i class="fas fa-users"></i> Límite de participantes
                        </label>
                        <input type="number" 
                               id="participantes_max" 
                               name="participantes_max" 
                               class="form-control" 
                               placeholder="50"
                               min="1"
                               value="{{ old('participantes_max', 50) }}"
                               required>
                    </div>
                </div>
                
                <!-- Premios (3 lugares) -->
                <div class="form-group">
                    <label>Premios:</label>
                    <div class="premios-grid">
                        <input type="text" 
                               name="premio_1" 
                               class="form-control premio-input" 
                               placeholder="1er Lugar: $5,000"
                               value="{{ old('premio_1', '$5,000') }}">
                        <input type="text" 
                               name="premio_2" 
                               class="form-control premio-input" 
                               placeholder="2do Lugar: $3,000"
                               value="{{ old('premio_2', '$3,000') }}">
                        <input type="text" 
                               name="premio_3" 
                               class="form-control premio-input" 
                               placeholder="3er Lugar: $1,000"
                               value="{{ old('premio_3', '$1,000') }}">
                    </div>
                </div>
                
                <!-- Requisitos (uno por línea) -->
                <div class="form-group">
                    <label for="requisitos">Requisitos del evento:</label>
                    <textarea id="requisitos" 
                              name="requisitos" 
                              class="form-control form-textarea" 
                              rows="5"
                              placeholder="Un requisito por línea:&#10;- Ser mayor de 18 años&#10;- Conocimientos de programación&#10;- Formar equipo de 3-5 personas"
                              required>{{ old('requisitos') }}</textarea>
                    <small class="form-hint">Escribe un requisito por línea</small>
                </div>
                
                <!-- Cronograma -->
                <div class="form-group">
                    <label><i class="fas fa-clock"></i> Cronograma del evento:</label>

                    <div id="cronograma-container">
                        <!-- Actividad por defecto -->
                        <div class="cronograma-item" data-index="0">
                            <div class="cronograma-row">
                                <div class="form-group" style="flex: 0 0 120px; margin-bottom: 0;">
                                    <input type="time"
                                           class="form-control cronograma-hora"
                                           placeholder="HH:MM"
                                           value="{{ old('cronograma_horas.0') }}">
                                </div>
                                <div class="form-group" style="flex: 1; margin-bottom: 0;">
                                    <input type="text"
                                           class="form-control cronograma-actividad"
                                           placeholder="Descripción de la actividad"
                                           value="{{ old('cronograma_actividades.0') }}">
                                </div>
                                <button type="button" class="btn-remove-actividad" onclick="removeActividad(this)" style="display: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-add-criterio" onclick="addActividad()" style="margin-top: 0.5rem;">
                        <i class="fas fa-plus"></i> Agregar Horario
                    </button>
                    <small class="form-hint">Agrega las actividades del evento con sus horarios</small>

                    <!-- Campo oculto para enviar el cronograma -->
                    <textarea id="cronograma" name="cronograma" style="display: none;"></textarea>
                </div>
                
                <!-- Asignación de Jueces del Sistema -->
                <div class="form-group">
                    <label><i class="fas fa-gavel"></i> Asignar Jueces del Sistema:</label>

                    <!-- Buscador de jueces -->
                    <div class="search-jueces" style="margin-bottom: 1rem;">
                        <input type="text"
                               id="buscarJuez"
                               class="form-control"
                               placeholder="Buscar juez por nombre o email..."
                               onkeyup="filtrarJueces()">
                    </div>

                    <div class="jueces-selector" id="juecesContainer">
                        @if($jueces->isEmpty())
                            <p class="text-muted">No hay jueces registrados en el sistema.</p>
                        @else
                            @foreach($jueces as $juez)
                                <label class="checkbox-card juez-item"
                                       data-nombre="{{ strtolower($juez->name) }}"
                                       data-email="{{ strtolower($juez->email) }}"
                                       data-juez-id="{{ $juez->id }}">
                                    <input type="checkbox"
                                           name="jueces_asignados[]"
                                           value="{{ $juez->id }}"
                                           class="juez-checkbox"
                                           onchange="actualizarJuecesTexto()"
                                           {{ (is_array(old('jueces_asignados')) && in_array($juez->id, old('jueces_asignados'))) ? 'checked' : '' }}>
                                    <div class="checkbox-content">
                                        <i class="fas fa-user-tie"></i>
                                        <span class="juez-name">{{ $juez->name }}</span>
                                        <small class="juez-email">{{ $juez->email }}</small>
                                    </div>
                                </label>
                            @endforeach
                        @endif
                    </div>
                    <small class="form-hint">Los jueces seleccionados podrán evaluar los equipos de este evento y se mostrarán automáticamente en la información pública</small>
                </div>

                <!-- Campo oculto para jueces (generado automáticamente) -->
                <input type="hidden" name="jueces" id="juecesTexto" value="{{ old('jueces', '') }}">

                <!-- Criterios de Evaluación -->
                <div class="form-group">
                    <label><i class="fas fa-clipboard-list"></i> Criterios de Evaluación:</label>
                    <div id="criterios-container">
                        <!-- Criterio por defecto -->
                        <div class="criterio-item" data-index="0">
                            <div class="criterio-header">
                                <h4>Criterio 1</h4>
                                <button type="button" class="btn-remove-criterio" onclick="removeCriterio(this)" style="display: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="criterio-fields">
                                <div class="form-row">
                                    <div class="form-group" style="flex: 2;">
                                        <input type="text"
                                               name="criterios[0][nombre]"
                                               class="form-control"
                                               placeholder="Nombre del criterio (ej: Innovación)"
                                               value="{{ old('criterios.0.nombre') }}"
                                               required>
                                    </div>
                                    <div class="form-group" style="flex: 1;">
                                        <input type="number"
                                               name="criterios[0][peso]"
                                               class="form-control"
                                               placeholder="Peso (1-10)"
                                               min="1"
                                               max="10"
                                               value="{{ old('criterios.0.peso', 1) }}"
                                               required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <textarea name="criterios[0][descripcion]"
                                              class="form-control"
                                              rows="2"
                                              placeholder="Descripción del criterio (opcional)">{{ old('criterios.0.descripcion') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-add-criterio" onclick="addCriterio()">
                        <i class="fas fa-plus"></i> Agregar Criterio
                    </button>
                    <small class="form-hint">Define los criterios que los jueces usarán para evaluar los proyectos</small>
                </div>

                <!-- Botones de acción -->
                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" onclick="window.history.back()">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-check"></i> Crear Evento
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
// Preview de imagen
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('imagePreview');
    const labelText = document.getElementById('file-label-text');

    if (file) {
        // Actualizar texto del label
        labelText.textContent = file.name;

        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <img src="${e.target.result}" alt="Preview">
                <button type="button" class="remove-preview" onclick="removeImage()">
                    <i class="fas fa-times"></i>
                </button>
            `;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
}

// Remover preview
function removeImage() {
    document.getElementById('imagen').value = '';
    document.getElementById('imagePreview').innerHTML = '';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('file-label-text').textContent = 'Seleccionar imagen';
}

// Validar fechas
document.getElementById('fecha_limite_inscripcion').addEventListener('change', function() {
    const inicio = document.getElementById('fecha_inicio').value;
    const limite = this.value;

    if (inicio && limite && new Date(limite) > new Date(inicio)) {
        alert('La fecha límite de inscripción debe ser antes de la fecha del evento');
        this.value = '';
    }
});

// Filtrar jueces en tiempo real
function filtrarJueces() {
    const input = document.getElementById('buscarJuez');
    const filtro = input.value.toLowerCase();
    const jueces = document.querySelectorAll('.juez-item');

    jueces.forEach(juez => {
        const nombre = juez.getAttribute('data-nombre');
        const email = juez.getAttribute('data-email');

        if (nombre.includes(filtro) || email.includes(filtro)) {
            juez.style.display = '';
        } else {
            juez.style.display = 'none';
        }
    });
}

// Actualizar campo de texto de jueces automáticamente
function actualizarJuecesTexto() {
    const checkboxes = document.querySelectorAll('.juez-checkbox:checked');
    const juecesArray = [];

    checkboxes.forEach(checkbox => {
        const label = checkbox.closest('.juez-item');
        const nombre = label.querySelector('.juez-name').textContent.trim();
        const email = label.querySelector('.juez-email').textContent.trim();

        // Formato: Nombre | Especialidad | Tags
        // Para obtener más info del juez necesitaríamos campos adicionales en el modelo User
        // Por ahora usamos nombre y email
        juecesArray.push(`${nombre} | Juez | Evaluación`);
    });

    document.getElementById('juecesTexto').value = juecesArray.join('\n');
}

// Actualizar al cargar la página si hay jueces pre-seleccionados
document.addEventListener('DOMContentLoaded', function() {
    actualizarJuecesTexto();
    actualizarCronogramaOculto();
});

// Gestión de cronograma dinámico
let actividadCount = 1;

function addActividad() {
    const container = document.getElementById('cronograma-container');
    const newIndex = actividadCount;

    const actividadHTML = `
        <div class="cronograma-item" data-index="${newIndex}">
            <div class="cronograma-row">
                <div class="form-group" style="flex: 0 0 120px; margin-bottom: 0;">
                    <input type="time"
                           class="form-control cronograma-hora"
                           placeholder="HH:MM"
                           onchange="actualizarCronogramaOculto()">
                </div>
                <div class="form-group" style="flex: 1; margin-bottom: 0;">
                    <input type="text"
                           class="form-control cronograma-actividad"
                           placeholder="Descripción de la actividad"
                           onchange="actualizarCronogramaOculto()">
                </div>
                <button type="button" class="btn-remove-actividad" onclick="removeActividad(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', actividadHTML);
    actividadCount++;
    updateRemoveActividadesButtons();
}

function removeActividad(button) {
    const actividadItem = button.closest('.cronograma-item');
    actividadItem.remove();
    updateRemoveActividadesButtons();
    actualizarCronogramaOculto();
}

function updateRemoveActividadesButtons() {
    const actividades = document.querySelectorAll('.cronograma-item');
    const removeButtons = document.querySelectorAll('.btn-remove-actividad');

    removeButtons.forEach(btn => {
        btn.style.display = actividades.length > 1 ? 'inline-block' : 'none';
    });
}

function actualizarCronogramaOculto() {
    const actividades = document.querySelectorAll('.cronograma-item');
    const cronogramaArray = [];

    actividades.forEach(item => {
        const hora = item.querySelector('.cronograma-hora').value;
        const actividad = item.querySelector('.cronograma-actividad').value;

        if (hora && actividad) {
            cronogramaArray.push(`${hora} - ${actividad}`);
        }
    });

    document.getElementById('cronograma').value = cronogramaArray.join('\n');
}

// Gestión de criterios dinámicos
let criterioCount = 1;

function addCriterio() {
    const container = document.getElementById('criterios-container');
    const newIndex = criterioCount;

    const criterioHTML = `
        <div class="criterio-item" data-index="${newIndex}">
            <div class="criterio-header">
                <h4>Criterio ${newIndex + 1}</h4>
                <button type="button" class="btn-remove-criterio" onclick="removeCriterio(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="criterio-fields">
                <div class="form-row">
                    <div class="form-group" style="flex: 2;">
                        <input type="text"
                               name="criterios[${newIndex}][nombre]"
                               class="form-control"
                               placeholder="Nombre del criterio (ej: Innovación)"
                               required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <input type="number"
                               name="criterios[${newIndex}][peso]"
                               class="form-control"
                               placeholder="Peso (1-10)"
                               min="1"
                               max="10"
                               value="1"
                               required>
                    </div>
                </div>
                <div class="form-group">
                    <textarea name="criterios[${newIndex}][descripcion]"
                              class="form-control"
                              rows="2"
                              placeholder="Descripción del criterio (opcional)"></textarea>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', criterioHTML);
    criterioCount++;
    updateRemoveButtons();
}

function removeCriterio(button) {
    const criterioItem = button.closest('.criterio-item');
    criterioItem.remove();
    updateCriterioNumbers();
    updateRemoveButtons();
}

function updateCriterioNumbers() {
    const criterios = document.querySelectorAll('.criterio-item');
    criterios.forEach((criterio, index) => {
        const header = criterio.querySelector('.criterio-header h4');
        header.textContent = `Criterio ${index + 1}`;
    });
}

function updateRemoveButtons() {
    const criterios = document.querySelectorAll('.criterio-item');
    const removeButtons = document.querySelectorAll('.btn-remove-criterio');

    // Mostrar botones de eliminar solo si hay más de un criterio
    removeButtons.forEach(btn => {
        btn.style.display = criterios.length > 1 ? 'inline-block' : 'none';
    });
}
</script>

<style>
/* Estilos para selector de jueces */
.jueces-selector {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
    margin-top: 0.5rem;
}

.checkbox-card {
    display: block;
    padding: 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.checkbox-card:hover {
    border-color: #6366f1;
    background-color: #f8f9ff;
}

.checkbox-card input[type="checkbox"] {
    display: none;
}

.checkbox-card input[type="checkbox"]:checked + .checkbox-content {
    color: #6366f1;
}

.checkbox-card input[type="checkbox"]:checked ~ .checkbox-content::before {
    content: '\f00c';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    color: #6366f1;
    font-size: 1.2rem;
}

.checkbox-content {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.checkbox-content i {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    color: #6366f1;
}

.juez-name {
    font-weight: 600;
    color: #333;
}

.juez-email {
    color: #666;
    font-size: 0.875rem;
}

/* Estilos para criterios */
.criterio-item {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.criterio-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.criterio-header h4 {
    margin: 0;
    color: #333;
    font-size: 1rem;
}

.btn-remove-criterio {
    background: #dc3545;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-remove-criterio:hover {
    background: #c82333;
}

.btn-add-criterio {
    background: #28a745;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 0.5rem;
    transition: background 0.3s;
}

.btn-add-criterio:hover {
    background: #218838;
}

.criterio-fields .form-row {
    display: flex;
    gap: 1rem;
}

.text-muted {
    color: #6c757d;
    font-style: italic;
}

/* Estilos para cronograma */
.cronograma-item {
    margin-bottom: 1rem;
}

.cronograma-row {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.btn-remove-actividad {
    background: #dc3545;
    color: white;
    border: none;
    padding: 0.5rem 0.75rem;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.3s;
    flex-shrink: 0;
}

.btn-remove-actividad:hover {
    background: #c82333;
}
</style>

</body>
</html>