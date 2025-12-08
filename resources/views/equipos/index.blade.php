@extends('layouts.inicio')

@section('title', 'Gestión de Equipos')

@section('content')

{{-- Enlazar CSS --}}
<link rel="stylesheet" href="{{ asset('css/equipos.css') }}">

<div class="equipos-section">
    <div class="contenedor-equipos">

        {{-- Alertas --}}
        @if(session('success'))
        <div style="background: #D4EDDA; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #C3E6CB;">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div style="background: #F8D7DA; color: #721C24; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #F5C6CB;">
            {{ session('error') }}
        </div>
        @endif

        {{-- Header --}}
        <div class="header-equipos">
            <div>
                <h1>Gestión de Equipos</h1>
                <p>Encuentra equipos para unirte o crea tu propio equipo para participar en hackatons</p>
            </div>
            <button class="btn-crear-equipo" onclick="abrirModal()">
                <i class="fas fa-plus-circle"></i> Crear Equipo
            </button>
        </div>

        {{-- Filtros --}}
        <div class="filtros-container">
            <input type="text" class="input-busqueda" placeholder="Buscar equipos por nombre, descripción o tecnologías...">
            <select class="select-filtro">
                <option>Todos los torneos</option>
            </select>
            <select class="select-filtro">
                <option>Todos los roles</option>
            </select>
        </div>

        {{-- Pestañas --}}
        <div class="tabs-bar">
            <button class="tab-item active" onclick="filtrar('disponibles', this)">Equipos disponibles ({{ count($equipos) }})</button>
            <button class="tab-item" onclick="filtrar('mios', this)">Mis equipos</button>
            <button class="tab-item" onclick="filtrar('todos', this)">Todos los equipos</button>
        </div>

        {{-- Lista de Equipos --}}
        <div class="lista-equipos" id="contenedor-tarjetas">
            @forelse($equipos as $equipo)
            @php
                $esMio = (Auth::check() && $equipo->user_id == Auth::id());
                $esMiembro = Auth::check() && $equipo->esMiembro(Auth::id());
                $estaLleno = $equipo->estaLleno();
                
                // Verificar si tiene solicitud pendiente
                $tieneSolicitudPendiente = false;
                if (Auth::check()) {
                    $tieneSolicitudPendiente = \App\Models\SolicitudEquipo::where('equipo_id', $equipo->id)
                        ->where('user_id', Auth::id())
                        ->where('estado', 'pendiente')
                        ->exists();
                }
            @endphp

            <div class="card-equipo tarjeta-item" data-mio="{{ $esMio ? 'si' : 'no' }}">
                <div class="indicador-miembros">
                    {{ $equipo->miembros_actuales }}/{{ $equipo->miembros_max }} Miembros
                </div>

                <h3>
                    {{ $equipo->nombre }} 
                    @if($estaLleno)
                        <span class="badge badge-completo">Completo</span>
                    @else
                        <span class="badge badge-reclutando">Reclutando</span>
                    @endif
                    
                    @if($equipo->acceso === 'Privado')
                        <span class="badge badge-privado"><i class="fas fa-lock"></i> Privado</span>
                    @endif
                </h3>
                
                <p style="color: #444; margin-bottom: 5px;">{{ $equipo->descripcion }}</p>

                <div style="color: #777; font-size: 0.9rem; margin: 10px 0;">
                    <span style="margin-right: 15px;"><i class="fas fa-trophy"></i> {{ $equipo->torneo }}</span>
                    <span style="margin-right: 15px;"><i class="fas fa-map-marker-alt"></i> {{ $equipo->ubicacion }}</span>
                    <span><i class="far fa-calendar-alt"></i> Creado {{ $equipo->created_at->format('d/m/Y') }}</span>
                </div>

                <div style="margin-top: 10px;">
                    <strong style="color: #4A148C; font-size: 0.9rem;">Líder:</strong> 
                    <span style="color: #666;">{{ $equipo->lider->name ?? 'Sin líder' }}</span>
                </div>

                {{-- Roles del equipo --}}
                <div style="margin-top: 15px;">
                    <strong style="color: #4A148C; font-size: 0.9rem; display: block; margin-bottom: 8px;">Roles del Equipo:</strong>
                    <div class="roles-equipo">
                        @php
                            $rolesEstado = $equipo->getRolesEstado();
                        @endphp
                        @foreach($rolesEstado as $rolEstado)
                            <div class="rol-item {{ $rolEstado['ocupado'] ? 'rol-ocupado' : 'rol-disponible' }}">
                                <span class="rol-nombre">{{ $rolEstado['rol'] }}</span>
                                @if($rolEstado['rol'] === 'Diseñador UX/UI')
                                    <span class="rol-contador">({{ $rolEstado['actual'] }}/{{ $rolEstado['max'] }})</span>
                                @endif
                                @if($rolEstado['ocupado'])
                                    <i class="fas fa-check-circle" style="color: #28a745;"></i>
                                @else
                                    <i class="fas fa-circle" style="color: #ccc;"></i>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="acciones-card">
                    {{-- Botón Ver Detalles --}}
                    <a href="{{ route('equipos.show', $equipo->id) }}" class="btn-detalles">Ver Detalles</a>
                    
                    {{-- Botones según el estado del usuario --}}
                    @if($esMio)
                        {{-- Es mi equipo (soy el líder) --}}
                        <a href="{{ route('equipos.show', $equipo->id) }}" class="btn-unirse" style="background:#555">
                            <i class="fas fa-cog"></i> Gestionar
                        </a>
                    @elseif($esMiembro)
                        {{-- Soy miembro del equipo --}}
                        <button class="btn-unirse" style="background:#28a745" disabled>
                            <i class="fas fa-check"></i> Miembro
                        </button>
                    @elseif($estaLleno)
                        {{-- El equipo está lleno --}}
                        <button class="btn-unirse" style="background:#ccc" disabled>
                            <i class="fas fa-ban"></i> Equipo lleno
                        </button>
                    @elseif($tieneSolicitudPendiente)
                        {{-- Ya envié solicitud --}}
                        <button class="btn-unirse" style="background:#FF9800" disabled>
                            <i class="fas fa-clock"></i> Solicitud Pendiente
                        </button>
                    @else
                        {{-- Puedo solicitar unirme --}}
                        <button type="button" class="btn-unirse" onclick="mostrarModalRol({{ $equipo->id }}, '{{ $equipo->acceso }}')">
                            <i class="fas fa-user-plus"></i> 
                            @if($equipo->acceso === 'Público')
                                Unirse Ahora
                            @else
                                Solicitar Unirse
                            @endif
                        </button>
                    @endif
                </div>
            </div>
            @empty
            <div style="text-align: center; padding: 40px; color: #999;">
                <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 15px;"></i>
                <p style="font-size: 1.2rem;">No hay equipos disponibles aún</p>
                <p>¡Sé el primero en crear uno!</p>
            </div>
            @endforelse
        </div>

    </div>
</div>

{{-- MODAL FLOTANTE (Oculto por defecto) --}}
<div id="modalRegistro" class="modal-backdrop" onclick="cerrarModal(event)">
    <div class="modal-caja" onclick="event.stopPropagation()">
        <h2 class="modal-titulo">REGISTRAR EQUIPO</h2>

        <form action="{{ route('equipos.store') }}" method="POST">
            @csrf

            <div class="input-wrapper">
                <i class="far fa-user"></i>
                <input type="text" name="nombre" class="modal-field" placeholder="Nombre del equipo" required>
            </div>

            <div class="input-wrapper">
                <i class="far fa-file-alt"></i>
                <input type="text" name="descripcion" class="modal-field" placeholder="Descripción" required>
            </div>

            <div class="input-wrapper">
                <i class="fas fa-map-marker-alt"></i>
                <input type="text" name="ubicacion" class="modal-field" placeholder="Ubicación" required>
            </div>

            <div class="input-wrapper">
                <i class="fas fa-trophy"></i>
                <select name="torneo" class="modal-field" required>
                    <option value="" disabled selected>Selecciona un Torneo</option>
                    @foreach($torneos as $torneo)
                    <option value="{{ $torneo->titulo }}">{{ $torneo->titulo }}</option>
                    @endforeach
                </select>
            </div>

            <div class="input-wrapper">
                <i class="fas fa-lock"></i>
                <select name="acceso" class="modal-field" required>
                    <option value="" disabled selected>Tipo de Acceso</option>
                    <option value="Público">Público (Cualquiera puede unirse)</option>
                    <option value="Privado">Privado (Requiere aprobación)</option>
                </select>
            </div>

            <button type="submit" class="btn-modal-submit">Crear Equipo</button>
        </form>
    </div>
</div>

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

.roles-equipo {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.rol-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-size: 0.85rem;
}

.rol-disponible {
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    color: #0369a1;
}

.rol-ocupado {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
}

.rol-nombre {
    font-weight: 500;
}

.rol-contador {
    font-size: 0.75rem;
    opacity: 0.8;
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

{{-- Enlazar JavaScript --}}
<script src="{{ asset('js/equipos.js') }}"></script>

@endsection