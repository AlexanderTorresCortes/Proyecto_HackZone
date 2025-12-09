<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluación de Equipo - HackZone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; }
        .container { max-width: 900px; margin: 0 auto; padding: 2rem; }
        .header-section { background: white; padding: 2rem; border-radius: 12px 12px 0 0; border-bottom: 3px solid #667eea; }
        .tabs { display: flex; gap: 0.5rem; margin-bottom: 1rem; }
        .tab { padding: 0.5rem 1rem; background: #e0e7ff; color: #4338ca; border-radius: 6px; font-size: 0.9rem; font-weight: 500; }
        .header-section h1 { color: #1e293b; font-size: 1.8rem; margin-bottom: 0.5rem; }
        .team-info { display: flex; align-items: center; gap: 0.75rem; color: #64748b; }
        .team-badge { background: #667eea; color: white; padding: 0.5rem 1rem; border-radius: 6px; font-weight: 600; }
        .progress-bar { background: #e0e7ff; border-radius: 8px; padding: 1rem; margin-top: 1rem; }
        .progress-bar p { color: #4338ca; font-size: 0.9rem; margin-bottom: 0.5rem; }
        .stats { display: flex; gap: 2rem; }
        .stat-item { text-align: center; }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: #1e293b; }
        .stat-label { font-size: 0.8rem; color: #64748b; }
        .form-section { background: white; padding: 2rem; border-radius: 0 0 12px 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .criterio-card { background: #f8f9fa; border: 1px solid #e5e7eb; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; }
        .criterio-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
        .criterio-icon { width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: white; }
        .criterio-icon.purple { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .criterio-icon.blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
        .criterio-icon.green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .criterio-icon.yellow { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); }
        .criterio-icon.red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        .criterio-info h3 { color: #1e293b; font-size: 1.1rem; margin-bottom: 0.25rem; }
        .criterio-info p { color: #64748b; font-size: 0.85rem; }
        .rating-scale { display: flex; gap: 0.5rem; justify-content: space-between; }
        .rating-btn { flex: 1; padding: 0.75rem; border: 2px solid #e5e7eb; background: white; border-radius: 8px; cursor: pointer; transition: all 0.3s; font-weight: 600; color: #64748b; }
        .rating-btn:hover { border-color: #667eea; background: #f0f2ff; }
        .rating-btn.selected { border-color: #667eea; background: #667eea; color: white; }
        .rating-label { display: flex; justify-content: space-between; margin-top: 0.5rem; font-size: 0.75rem; color: #94a3b8; }
        .comentarios-section { margin-top: 2rem; }
        .comentarios-section h3 { color: #1e293b; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem; }
        .comentarios-section textarea { width: 100%; padding: 1rem; border: 2px solid #e5e7eb; border-radius: 8px; resize: vertical; min-height: 120px; font-family: inherit; }
        .comentarios-section textarea:focus { outline: none; border-color: #667eea; }
        .actions { display: flex; gap: 1rem; margin-top: 2rem; }
        .btn { padding: 1rem 2rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 0.5rem; justify-content: center; flex: 1; }
        .btn-secondary { background: #e5e7eb; color: #374151; }
        .btn-secondary:hover { background: #d1d5db; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4); }
        .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; }
        .alert-success { background: #d1fae5; color: #065f46; border-left: 4px solid #10b981; }
        .alert-warning { background: #fef3c7; color: #92400e; border-left: 4px solid #f59e0b; }
        .readonly-mode { pointer-events: none; opacity: 0.7; }
    </style>
</head>
<body>

@include('components.navbar')

<div class="container">
    <div class="header-section">
        <div class="tabs">
            <span class="tab">Vista Administrador</span>
            <span class="tab">Vista Juez</span>
        </div>
        <h1>Evaluación de Equipo</h1>
        <p style="color: #64748b; margin-bottom: 1rem;">Evalúa el proyecto del equipo según los criterios establecidos</p>
        <div class="team-info">
            <span class="team-badge"><i class="fas fa-users"></i> {{ $equipo->nombre }}</span>
            <span><i class="fas fa-trophy"></i> {{ $evento->titulo }}</span>
            <span><i class="fas fa-user-tie"></i> Programador</span>
            <span><i class="fas fa-user"></i> Analista</span>
        </div>

        @if($evaluacion && $evaluacion->estado === 'completada')
            <div class="progress-bar" style="background: #d1fae5;">
                <p style="color: #065f46;"><i class="fas fa-check-circle"></i> Evaluación Completada</p>
                <div class="stats">
                    <div class="stat-item">
                        <div class="stat-value">{{ $evento->criteriosEvaluacion->count() }}</div>
                        <div class="stat-label">Criterios</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ $evaluacion->puntuaciones->count() }}</div>
                        <div class="stat-label">Calificados</div>
                    </div>
                </div>
            </div>
        @else
            <div class="progress-bar">
                <p>Completa todos los criterios para enviar la evaluación</p>
                <div class="stats">
                    <div class="stat-item">
                        <div class="stat-value">{{ $evaluacion ? $evaluacion->puntuaciones->count() : 0 }}</div>
                        <div class="stat-label">Criterios Completados</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ $evento->criteriosEvaluacion->count() - ($evaluacion ? $evaluacion->puntuaciones->count() : 0) }}</div>
                        <div class="stat-label">Pendientes</div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if($equipo->entregas->isNotEmpty())
        <div style="background: white; border-radius: 12px; padding: 2rem; margin-bottom: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h3 style="color: #1e293b; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-folder-open"></i> Archivos del Proyecto
            </h3>
            <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 1.5rem;">Revisa los archivos subidos por el equipo antes de evaluar</p>

            @foreach($equipo->entregas as $entrega)
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 0.75rem; transition: all 0.3s;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <i class="fas fa-file-{{ $entrega->tipo_archivo }}" style="font-size: 2rem; color: {{ $entrega->tipo_archivo == 'zip' ? '#f59e0b' : ($entrega->tipo_archivo == 'pdf' ? '#dc2626' : '#ea580c') }};"></i>
                        <div>
                            <h4 style="color: #1e293b; margin-bottom: 0.25rem;">{{ $entrega->nombre_archivo }}</h4>
                            <p style="color: #64748b; font-size: 0.85rem;">
                                Versión {{ $entrega->version }} •
                                {{ $entrega->formatted_size }} •
                                {{ $entrega->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('usuario.entregas.download', $entrega->id) }}"
                       style="background: #667eea; color: white; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: all 0.3s;"
                       onmouseover="this.style.background='#5568d3'"
                       onmouseout="this.style.background='#667eea'">
                        <i class="fas fa-download"></i> Descargar
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div style="background: #fef3c7; border: 1px solid #fbbf24; border-radius: 12px; padding: 1.5rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 1rem;">
            <i class="fas fa-exclamation-triangle" style="color: #92400e; font-size: 1.5rem;"></i>
            <div>
                <h4 style="color: #92400e; margin-bottom: 0.25rem;">Sin archivos subidos</h4>
                <p style="color: #78350f; font-size: 0.9rem; margin: 0;">El equipo aún no ha subido archivos para este proyecto.</p>
            </div>
        </div>
    @endif

    <form action="{{ route('juez.guardar-evaluacion', [$evento->id, $equipo->id]) }}" method="POST" class="form-section {{ $evaluacion && $evaluacion->estado === 'completada' ? 'readonly-mode' : '' }}">
        @csrf

        @php
            $iconos = ['purple', 'blue', 'green', 'yellow', 'red'];
            $iconClasses = ['fa-lightbulb', 'fa-bullseye', 'fa-palette', 'fa-cogs', 'fa-users'];
        @endphp

        @foreach($evento->criteriosEvaluacion as $index => $criterio)
            @php
                $puntuacionActual = $evaluacion ? $evaluacion->puntuaciones->where('criterio_id', $criterio->id)->first() : null;
            @endphp

            <div class="criterio-card">
                <div class="criterio-header">
                    <div class="criterio-icon {{ $iconos[$index % 5] }}">
                        <i class="fas {{ $iconClasses[$index % 5] }}"></i>
                    </div>
                    <div class="criterio-info">
                        <h3>{{ $criterio->nombre }} <span style="background: #e0e7ff; color: #4338ca; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; margin-left: 0.5rem;">Peso: {{ $criterio->peso }}/10</span></h3>
                        <p>{{ $criterio->descripcion ?? 'Evalúa este aspecto del proyecto' }}</p>
                        <small style="color: #94a3b8; font-size: 0.8rem;"><i class="fas fa-info-circle"></i> Este criterio tiene un peso de {{ $criterio->peso }} en la evaluación final</small>
                    </div>
                </div>

                <div class="rating-scale">
                    @for($i = 1; $i <= 10; $i++)
                        <button type="button"
                                class="rating-btn {{ $puntuacionActual && $puntuacionActual->puntuacion == $i ? 'selected' : '' }}"
                                data-criterio="{{ $criterio->id }}"
                                data-value="{{ $i }}"
                                onclick="selectRating(this)">
                            {{ $i }}
                        </button>
                        <input type="hidden" name="puntuaciones[{{ $criterio->id }}]" id="puntuacion_{{ $criterio->id }}" value="{{ $puntuacionActual ? $puntuacionActual->puntuacion : '' }}">
                    @endfor
                </div>
                <div class="rating-label">
                    <span>Sin calificar</span>
                    <span>Excelente</span>
                </div>
            </div>
        @endforeach

        <div class="comentarios-section">
            <h3><i class="fas fa-comment-alt"></i> Comentarios Generales</h3>
            <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 0.5rem;">Proporciona retroalimentación detallada para el equipo</p>
            <textarea name="comentarios" placeholder="Escribe tus comentarios y sugerencias para el equipo...">{{ $evaluacion ? $evaluacion->comentarios : old('comentarios') }}</textarea>
        </div>

        <div class="actions">
            <a href="{{ route('juez.equipos', $evento->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            @if(!$evaluacion || $evaluacion->estado !== 'completada')
                <button type="submit" name="estado" value="pendiente" class="btn btn-secondary">
                    <i class="fas fa-save"></i> Guardar Borrador
                </button>
                <button type="submit" name="estado" value="completada" class="btn btn-primary" id="btnEnviar">
                    <i class="fas fa-paper-plane"></i> Enviar Evaluación
                </button>
            @endif
        </div>
    </form>
</div>

<script>
function selectRating(btn) {
    // Prevenir comportamiento por defecto
    if (btn.form) {
        btn.form.onsubmit = null;
    }

    const criterioId = btn.dataset.criterio;
    const value = btn.dataset.value;

    // Remover selección de otros botones del mismo criterio
    const allButtons = document.querySelectorAll(`button[data-criterio="${criterioId}"]`);
    allButtons.forEach(b => b.classList.remove('selected'));

    // Seleccionar botón actual
    btn.classList.add('selected');

    // Actualizar input hidden
    const inputHidden = document.getElementById(`puntuacion_${criterioId}`);
    if (inputHidden) {
        inputHidden.value = value;
    }

    // Verificar si todos los criterios están calificados
    verificarCompletitud();
}

function verificarCompletitud() {
    const totalCriterios = {{ $evento->criteriosEvaluacion->count() }};
    const inputs = document.querySelectorAll('input[name^="puntuaciones"]');
    let calificados = 0;

    inputs.forEach(input => {
        if (input.value && input.value.trim() !== '') {
            calificados++;
        }
    });

    const btnEnviar = document.getElementById('btnEnviar');
    if (btnEnviar) {
        if (calificados < totalCriterios) {
            btnEnviar.disabled = true;
            btnEnviar.title = `Califica todos los criterios (${calificados}/${totalCriterios})`;
        } else {
            btnEnviar.disabled = false;
            btnEnviar.title = 'Enviar evaluación';
        }
    }
}

// Verificar al cargar
document.addEventListener('DOMContentLoaded', function() {
    verificarCompletitud();

    // Prevenir submit accidental de los botones de rating
    const ratingButtons = document.querySelectorAll('.rating-btn');
    ratingButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            selectRating(this);
        });
    });
});
</script>

</body>
</html>
