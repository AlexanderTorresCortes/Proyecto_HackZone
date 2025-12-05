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

<div class="admin-container">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <button class="btn-back" onclick="window.location.href='{{ route('admin.dashboard') }}'">
                <i class="fas fa-chevron-left"></i>
            </button>
            <h2>Panel</h2>
        </div>
        
        <nav class="sidebar-nav">
            <a href="#" class="nav-item active">
                <i class="fas fa-plus-circle"></i>
                <span>Crear Evento</span>
            </a>
        </nav>
    </aside>
    
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
                    <label for="imagen">Imagen de promoción evento:</label>
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
                            <span>Seleccionar imagen</span>
                        </label>
                        <div id="imagePreview" class="image-preview"></div>
                    </div>
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
                    <label for="cronograma">Cronograma del evento:</label>
                    <textarea id="cronograma" 
                              name="cronograma" 
                              class="form-control form-textarea" 
                              rows="5"
                              placeholder="Formato: HH:MM - Actividad&#10;10:00 - Registro e inauguración&#10;11:00 - Inicio del desarrollo&#10;18:00 - Presentación de proyectos"
                              required>{{ old('cronograma') }}</textarea>
                    <small class="form-hint">Formato: HH:MM - Actividad (uno por línea)</small>
                </div>
                
                <!-- Jueces -->
                <div class="form-group">
                    <label for="jueces">Jueces evaluadores:</label>
                    <textarea id="jueces" 
                              name="jueces" 
                              class="form-control form-textarea" 
                              rows="5"
                              placeholder="Formato: Nombre | Cargo | Especialidades (separadas por coma)&#10;Dr. Juan Pérez | CTO en TechCorp | IA, Machine Learning, Python&#10;Ing. María López | Senior Developer | Backend, Cloud, AWS"
                              required>{{ old('jueces') }}</textarea>
                    <small class="form-hint">Formato: Nombre | Cargo | Especialidades (uno por línea)</small>
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
    
    if (file) {
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
</script>

</body>
</html>