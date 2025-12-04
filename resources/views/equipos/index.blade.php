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
                        <form action="{{ route('equipos.solicitarUnirse', $equipo->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-unirse">
                                <i class="fas fa-user-plus"></i> 
                                @if($equipo->acceso === 'Público')
                                    Unirse Ahora
                                @else
                                    Solicitar Unirse
                                @endif
                            </button>
                        </form>
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

{{-- Enlazar JavaScript --}}
<script src="{{ asset('js/equipos.js') }}"></script>

@endsection