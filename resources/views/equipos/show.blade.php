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
                    <form action="{{ route('equipos.solicitarUnirse', $equipo->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-principal">
                            <i class="fas fa-user-plus"></i> Solicitar Unirse
                        </button>
                    </form>
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
                                <span class="miembro-rol {{ $miembro->rol === 'Líder' ? 'rol-lider' : '' }}">
                                    @if($miembro->rol === 'Líder')
                                        <i class="fas fa-crown"></i>
                                    @endif
                                    {{ $miembro->rol }}
                                </span>
                            </div>
                            <div class="miembro-contacto">
                                <span>{{ $miembro->usuario->email }}</span>
                            </div>
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

@endsection