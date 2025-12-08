@extends('layouts.inicio')

@section('title', 'Detalles del Equipo')

@section('content')

<link rel="stylesheet" href="{{ asset('css/equipo-detalle.css') }}">

<div class="detalle-section">
    <div class="contenedor-detalle">

        {{-- Alertas --}}
        @if(session('success'))
        <div class="alerta alerta-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
        <div class="alerta alerta-error">{{ session('error') }}</div>
        @endif

        {{-- Botón volver --}}
        <a href="{{ route('equipos.index') }}" class="btn-volver">
            <i class="fas fa-arrow-left"></i> Volver a equipos
        </a>

        {{-- Header del equipo --}}
        <div class="header-detalle">
            <div>
                <h1>{{ $equipo->nombre }}</h1>
                <div class="badges-info">
                    <span class="badge badge-estado">
                        @if($equipo->estaLleno())
                            Completo
                        @else
                            Reclutando
                        @endif
                    </span>
                    <span class="badge badge-acceso">{{ $equipo->acceso }}</span>
                </div>
            </div>
            
            @if(!$esLider && !$esMiembro && !$equipo->estaLleno())
                @if($tieneSolicitudPendiente)
                    <button class="btn-principal" disabled>Solicitud Enviada</button>
                @else
                    <button type="button" class="btn-principal" onclick="mostrarModalRol({{ $equipo->id }}, '{{ $equipo->acceso }}')">
                        <i class="fas fa-user-plus"></i> Solicitar Unirse
                    </button>
                @endif
            @endif
        </div>

        {{-- Grid de información --}}
        <div class="grid-info">
            {{-- Columna principal --}}
            <div class="columna-principal">
                
                {{-- Información general --}}
                <div class="tarjeta">
                    <h2><i class="fas fa-info-circle"></i> Información General</h2>
                    <div class="info-item">
                        <span class="info-label">Descripción:</span>
                        <p>{{ $equipo->descripcion }}</p>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-trophy"></i> Torneo:</span>
                        <p>{{ $equipo->torneo }}</p>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-map-marker-alt"></i> Ubicación:</span>
                        <p>{{ $equipo->ubicacion }}</p>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-calendar"></i> Fecha de creación:</span>
                        <p>{{ $equipo->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>

                {{-- Miembros del equipo --}}
                <div class="tarjeta">
                    <h2><i class="fas fa-users"></i> Miembros del Equipo ({{ $equipo->miembros_actuales }}/{{ $equipo->miembros_max }})</h2>
                    
                    <div class="lista-miembros">
                        @foreach($equipo->miembros as $miembro)
                        <div class="miembro-card">
                            <div class="miembro-avatar">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="miembro-info">
                                <h4>{{ $miembro->usuario->name }}</h4>
                                <span class="miembro-rol {{ $miembro->rol === 'Líder' ? 'rol-lider' : ($miembro->rol ? '' : 'rol-sin-asignar') }}">
                                    @if($miembro->rol === 'Líder')
                                        <i class="fas fa-crown"></i>
                                    @endif
                                    {{ $miembro->rol ?? 'Sin rol asignado' }}
                                </span>
                            </div>
                            <div class="miembro-contacto">
                                <span>{{ $miembro->usuario->email }}</span>
                            </div>
                            @if($esLider && $miembro->rol !== 'Líder')
                                <div class="miembro-acciones">
                                    <select class="select-rol" onchange="asignarRol({{ $miembro->id }}, this.value)" data-miembro-id="{{ $miembro->id }}">
                                        <option value="">Sin rol</option>
                                        @foreach(\App\Models\Equipo::getRolesDisponibles() as $rol)
                                            <option value="{{ $rol }}" {{ $miembro->rol === $rol ? 'selected' : '' }}>{{ $rol }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                        @endforeach

                        {{-- Espacios vacíos --}}
                        @for($i = $equipo->miembros_actuales; $i < $equipo->miembros_max; $i++)
                        <div class="miembro-card miembro-vacio">
                            <div class="miembro-avatar">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="miembro-info">
                                <h4>Espacio disponible</h4>
                                <span class="miembro-rol">Esperando miembro</span>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>

            </div>

            {{-- Columna lateral --}}
            <div class="columna-lateral">
                
                {{-- Información del líder --}}
                <div class="tarjeta tarjeta-lider">
                    <h3><i class="fas fa-crown"></i> Líder del Equipo</h3>
                    <div class="lider-info">
                        <div class="lider-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <h4>{{ $equipo->lider->name }}</h4>
                        <p>{{ $equipo->lider->email }}</p>
                        
                        @if(!$esLider && $esMiembro)
                        <button class="btn-contactar">
                            <i class="fas fa-envelope"></i> Contactar
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Roles del equipo --}}
                <div class="tarjeta">
                    <h3><i class="fas fa-user-tag"></i> Roles del Equipo</h3>
                    @php
                        $rolesEstado = $equipo->getRolesEstado();
                    @endphp
                    @foreach($rolesEstado as $rolEstado)
                        <div class="rol-estado-item {{ $rolEstado['ocupado'] ? 'rol-ocupado' : 'rol-disponible' }}">
                            <div class="rol-estado-header">
                                <span class="rol-estado-nombre">{{ $rolEstado['rol'] }}</span>
                                @if($rolEstado['rol'] === 'Diseñador UX/UI')
                                    <span class="rol-estado-contador">({{ $rolEstado['actual'] }}/{{ $rolEstado['max'] }})</span>
                                @endif
                                @if($rolEstado['ocupado'])
                                    <i class="fas fa-check-circle" style="color: #28a745;"></i>
                                @else
                                    <i class="fas fa-circle" style="color: #ccc;"></i>
                                @endif
                            </div>
                            @if($rolEstado['ocupado'] && $rolEstado['miembros']->count() > 0)
                                <div class="rol-estado-miembros">
                                    @foreach($rolEstado['miembros'] as $miembro)
                                        <small>{{ $miembro->usuario->name }}</small>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Invitar usuarios (solo visible para el líder) --}}
                @if($esLider && !$equipo->estaLleno())
                <div class="tarjeta tarjeta-invitaciones">
                    <h3><i class="fas fa-user-plus"></i> Invitar Usuarios</h3>
                    
                    <div class="invitar-form">
                        <div class="buscador-usuarios">
                            <input type="text" 
                                   id="buscadorUsuario" 
                                   class="input-buscador" 
                                   placeholder="Buscar usuario por nombre, username o email..."
                                   autocomplete="off">
                            <div id="resultadosBusqueda" class="resultados-busqueda"></div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Solicitudes pendientes (solo visible para el líder) --}}
                @if($esLider && $equipo->solicitudesPendientes->count() > 0)
                <div class="tarjeta tarjeta-solicitudes">
                    <h3><i class="fas fa-bell"></i> Solicitudes Pendientes ({{ $equipo->solicitudesPendientes->count() }})</h3>
                    
                    @foreach($equipo->solicitudesPendientes as $solicitud)
                    <div class="solicitud-item">
                        <div class="solicitud-usuario">
                            <i class="fas fa-user"></i>
                            <div>
                                <strong>{{ $solicitud->usuario->name }}</strong>
                                <small>{{ $solicitud->usuario->email }}</small>
                            </div>
                        </div>
                        <div class="solicitud-acciones">
                            <form action="{{ route('equipos.aceptarSolicitud', $solicitud->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn-aceptar">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <form action="{{ route('equipos.rechazarSolicitud', $solicitud->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn-rechazar">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Estadísticas --}}
                <div class="tarjeta">
                    <h3><i class="fas fa-chart-bar"></i> Estadísticas</h3>
                    <div class="estadistica-item">
                        <span>Espacios disponibles</span>
                        <strong>{{ $equipo->miembros_max - $equipo->miembros_actuales }}</strong>
                    </div>
                    <div class="estadistica-item">
                        <span>Tipo de acceso</span>
                        <strong>{{ $equipo->acceso }}</strong>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

@if($esLider)
<script>
function asignarRol(miembroId, rol) {
    const mensaje = rol ? `¿Deseas asignar el rol "${rol}" a este miembro?` : '¿Deseas quitar el rol a este miembro?';
    
    if (!confirm(mensaje)) {
        // Si canceló, restaurar el valor anterior
        const select = document.querySelector(`select[data-miembro-id="${miembroId}"]`);
        const evento = new Event('change', { bubbles: true });
        select.dispatchEvent(evento);
        return;
    }
    
    fetch(`/equipos/asignar-rol/${miembroId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ rol: rol || null })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error al asignar el rol');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al asignar el rol');
        location.reload();
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const buscador = document.getElementById('buscadorUsuario');
    const resultados = document.getElementById('resultadosBusqueda');
    let timeoutId;

    if (!buscador) return;

    buscador.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(timeoutId);
        
        if (query.length < 2) {
            resultados.innerHTML = '';
            resultados.style.display = 'none';
            return;
        }

        timeoutId = setTimeout(() => {
            buscarUsuarios(query);
        }, 300);
    });

    function buscarUsuarios(query) {
        fetch(`{{ route('equipos.buscarUsuarios', $equipo->id) }}?q=${encodeURIComponent(query)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            mostrarResultados(data.usuarios);
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function mostrarResultados(usuarios) {
        if (usuarios.length === 0) {
            resultados.innerHTML = '<div class="sin-resultados">No se encontraron usuarios</div>';
            resultados.style.display = 'block';
            return;
        }

        resultados.innerHTML = usuarios.map(usuario => `
            <div class="usuario-resultado" data-user-id="${usuario.id}">
                <div class="usuario-info">
                    <div class="usuario-avatar-mini">
                        ${usuario.avatar ? 
                            `<img src="/storage/${usuario.avatar}" alt="${usuario.name}">` : 
                            `<i class="fas fa-user"></i>`
                        }
                    </div>
                    <div>
                        <strong>${usuario.name}</strong>
                        <small>${usuario.username || usuario.email}</small>
                    </div>
                </div>
                <button class="btn-invitar" onclick="enviarInvitacion(${usuario.id}, '${usuario.name.replace(/'/g, "\\'")}')">
                    <i class="fas fa-paper-plane"></i> Invitar
                </button>
            </div>
        `).join('');
        resultados.style.display = 'block';
    }

    window.enviarInvitacion = function(userId, userName) {
        if (confirm(`¿Deseas invitar a ${userName} al equipo?`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('equipos.enviarInvitacion', $equipo->id) }}`;
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            
            const userIdInput = document.createElement('input');
            userIdInput.type = 'hidden';
            userIdInput.name = 'user_id';
            userIdInput.value = userId;
            
            form.appendChild(csrf);
            form.appendChild(userIdInput);
            document.body.appendChild(form);
            form.submit();
        }
    };

    // Cerrar resultados al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (buscador && resultados && !buscador.contains(e.target) && !resultados.contains(e.target)) {
            resultados.style.display = 'none';
        }
    });
});
</script>

<style>
.buscador-usuarios {
    position: relative;
}

.input-buscador {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.9rem;
    transition: border-color 0.2s;
}

.input-buscador:focus {
    outline: none;
    border-color: #6b21a8;
    box-shadow: 0 0 0 3px rgba(107, 33, 168, 0.1);
}

.resultados-busqueda {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-top: 0.5rem;
    max-height: 300px;
    overflow-y: auto;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    z-index: 100;
}

.usuario-resultado {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.2s;
}

.usuario-resultado:hover {
    background: #f9fafb;
}

.usuario-resultado:last-child {
    border-bottom: none;
}

.usuario-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
}

.usuario-avatar-mini {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.usuario-avatar-mini img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.usuario-info strong {
    display: block;
    color: #374151;
    font-size: 0.9rem;
}

.usuario-info small {
    display: block;
    color: #6b7280;
    font-size: 0.8rem;
}

.btn-invitar {
    padding: 0.5rem 1rem;
    background: #6b21a8;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: background 0.2s;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-invitar:hover {
    background: #581c87;
}

.sin-resultados {
    padding: 1rem;
    text-align: center;
    color: #6b7280;
    font-size: 0.9rem;
}
</style>
@endif

{{-- Modal para seleccionar rol --}}
<div id="modalRol" class="modal-rol" style="display: none;">
    <div class="modal-rol-content">
        <span class="close-modal-rol" onclick="cerrarModalRol()">&times;</span>
        <h3>Selecciona el rol que deseas</h3>
        <form id="formSolicitarRol" method="POST">
            @csrf
            <input type="hidden" name="rol_solicitado" id="rolSeleccionado">
            <div class="roles-selector">
                <div class="rol-option" data-rol="Frontend" onclick="seleccionarRol('Frontend')">
                    <i class="fas fa-code"></i>
                    <span>Frontend</span>
                </div>
                <div class="rol-option" data-rol="Backend" onclick="seleccionarRol('Backend')">
                    <i class="fas fa-server"></i>
                    <span>Backend</span>
                </div>
                <div class="rol-option" data-rol="Full-Stack" onclick="seleccionarRol('Full-Stack')">
                    <i class="fas fa-layer-group"></i>
                    <span>Full-Stack</span>
                </div>
                <div class="rol-option" data-rol="Diseñador UX/UI" onclick="seleccionarRol('Diseñador UX/UI')">
                    <i class="fas fa-palette"></i>
                    <span>Diseñador UX/UI</span>
                </div>
            </div>
            <button type="submit" class="btn-submit-rol" disabled>Confirmar</button>
        </form>
    </div>
</div>

<style>
.modal-rol {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-rol-content {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    max-width: 500px;
    width: 90%;
    position: relative;
}

.close-modal-rol {
    position: absolute;
    right: 1rem;
    top: 1rem;
    font-size: 2rem;
    cursor: pointer;
    color: #999;
}

.close-modal-rol:hover {
    color: #333;
}

.roles-selector {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin: 1.5rem 0;
}

.rol-option {
    padding: 1.5rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
}

.rol-option:hover {
    border-color: #6b21a8;
    background: #f9fafb;
}

.rol-option.selected {
    border-color: #6b21a8;
    background: #ede9fe;
}

.rol-option i {
    font-size: 2rem;
    color: #6b21a8;
    display: block;
    margin-bottom: 0.5rem;
}

.rol-option span {
    font-weight: 600;
    color: #374151;
}

.btn-submit-rol {
    width: 100%;
    padding: 0.75rem;
    background: #6b21a8;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-submit-rol:hover:not(:disabled) {
    background: #581c87;
}

.btn-submit-rol:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.rol-estado-item {
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    border-radius: 6px;
}

.rol-estado-item.rol-disponible {
    background: #f0f9ff;
    border: 1px solid #bae6fd;
}

.rol-estado-item.rol-ocupado {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
}

.rol-estado-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
}

.rol-estado-nombre {
    flex: 1;
}

.rol-estado-contador {
    font-size: 0.85rem;
    opacity: 0.8;
}

.rol-estado-miembros {
    margin-top: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px solid rgba(0,0,0,0.1);
}

.rol-estado-miembros small {
    display: block;
    color: #666;
    font-size: 0.85rem;
}
</style>

<script>
let equipoIdActual = null;
let accesoEquipo = null;

function mostrarModalRol(equipoId, acceso) {
    equipoIdActual = equipoId;
    accesoEquipo = acceso;
    document.getElementById('modalRol').style.display = 'flex';
    document.getElementById('formSolicitarRol').action = `/equipos/${equipoId}/solicitar`;
    document.getElementById('rolSeleccionado').value = '';
    document.querySelectorAll('.rol-option').forEach(opt => opt.classList.remove('selected'));
    document.querySelector('.btn-submit-rol').disabled = true;
}

function cerrarModalRol() {
    document.getElementById('modalRol').style.display = 'none';
}

function seleccionarRol(rol) {
    document.querySelectorAll('.rol-option').forEach(opt => opt.classList.remove('selected'));
    event.target.closest('.rol-option').classList.add('selected');
    document.getElementById('rolSeleccionado').value = rol;
    document.querySelector('.btn-submit-rol').disabled = false;
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const modal = document.getElementById('modalRol');
    if (event.target == modal) {
        cerrarModalRol();
    }
}
</script>

@endsection